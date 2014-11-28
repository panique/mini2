# MINI 2

## What is MINI 2 ?

An extremely simple PHP barebone / skeleton application built on top of the wonderful Slim router / micro framework
[Homepage](slimframework) [GitHub](https://github.com/codeguy/Slim). When working with this, always have a look into
the excellent [Slim documentation](http://docs.slimframework.com).

MINI is by intention as simple as possible, while still being able to create powerful applications. I've built MINI
in my free-time, unpaid, voluntarily, just for my personal commercial and private use and uploaded it on GitHub as it
might be useful for others too. Nothing more. Don't hate, don't complain, don't vandalize, don't bash (this needs to be 
said these days as people treat tiny free open-source private scripts like they paid masses of money for it). 
If you don't like it, don't use it. If you see issues, please create a ticket. In case you want to contribute, please 
create a feature-branch, never commit into master. Thanks :)

Mini currently uses Slim 2.4.3.

## Features

- Built on Slim
- RESTful routes
- EXTREMELY SIMPLE. The entire application, while still being MVC-ish, is basically just 2 .php files plus external 
dependencies plus view templates.
- Built using Slim View package, using Twig as template engine. It's easily possible to use all Twig features (caching 
etc.) or switch to another engine (Smarty, Mustache, etc.)
- uses pure PDO instead of ORM (it's easier to handle)

- (optionally) shows emulated PDO SQL statement for easy debugging
- (optionally) compiles SCSS to CSS on the fly
- (optionally) minifies CSS on the fly
- (optionally) minifies JS on the fly
- (optional) dev/test/production switch

- by default only allows user access to /public folder. The rest of the application (including .git files, swap files,
etc) is not accessible.

## TODO

- TODO: full error reporting
- TODO: look :)

## Requirements

- PHP 5.3+
- MySQL
- mod_rewrite activated, document root routed to /public (tutorial below)

Maybe useful: [How to setup a basic LAMP stack (Linux, Apache, MySQL, PHP) on Ubuntu 14.04 LTS](http://www.dev-metal.com/installsetup-basic-lamp-stack-linux-apache-mysql-php-ubuntu-14-04-lts/)
and [How to setup a basic LAMP stack (Linux, Apache, MySQL, PHP) on Ubuntu 12.04 LTS](http://www.dev-metal.com/setup-basic-lamp-stack-linux-apache-mysql-php-ubuntu-12-04/).

## Installation

##### 1. Activate mod_rewrite and ...
[Tutorial for Ubuntu 14.04 LTS](http://www.dev-metal.com/enable-mod_rewrite-ubuntu-14-04-lts/) and a
[Tutorial for Ubuntu 12.04 LTS](http://www.dev-metal.com/enable-mod_rewrite-ubuntu-12-04-lts/).
 
##### 2. ... route all requests to /public folder of the script
 
Change the VirtualHost file from
```DocumentRoot /var/www/html```
to 
```DocumentRoot /var/www/html/public``` 
and from
```<Directory "/var/www/html">``` 
to 
```<Directory "/var/www/html/public">```. 
Don't forget to restart. By the way this is also mentioned in the official Slim documentation, but hidden quite much: 
http://docs.slimframework.com/#Route-URL-Rewriting

##### 3. Edit the development database configs

Inside `public/index.php` change this:

```
        'database' => array(            
            'db_host' => 'localhost',
            'db_port' => '',
            'db_name' => 'mini',
            'db_user' => '',
            'db_pass' => ''
        )
```

##### 4. Execute the SQL statements
 
In `_install` folder (for example with PHPMyAdmin) to create the demo database.

##### 5. Get dependencies via Composer
 
Do a `composer install` in the project's root folder to fetch the dependencies (and to create the autoloader).

## Basic usage

See index.php in /public. The code below will basically show /view/subpage.twig when user moves to 
yourproject.com/subpage !  

```php
$app->get('/subpage', function () use ($app) {
    $app->render('subpage.twig');
});
```

Same like above here, but this time the $model is passed to the route (`use ($app, $model)`), so it's possible to
perform model actions (database requests, data manipulation, etc). Action getAllSongs() is called, the result $songs 
(obviously an array of songs) passed to the view (view/songs.twig) via `'songs' => $songs`.

```php
$app->get('/songs', function () use ($app, $model) {

    $songs = $model->getAllSongs();

    $app->render('songs.twig', array(
        'songs' => $songs
    ));
});
```

Inside the view the data is easily rendered like this (the template engine Twig is used here). Twig makes the view 
extremely simple and secure. Instead of doing this `<?php echo htmlspecialchars($song->id, ENT_QUOTES, 'UTF-8'); ?>` 
inside your HTML-Twig-template you can simply do `{{ song.id }}` which automatically escapes and echos `$song["id"]`, 
`$song->id` etc. Fantastic! See the full [Twig documentation here](http://twig.sensiolabs.org/). 

```twig
{% for song in songs %}
<tr>
    <td>{{ song.id }}</td>
    <td>{{ song.artist }}</td>
</tr>
{% endfor %}         
```

The content of the model (currently in `Mini\model\model.php`) is extremely simple, it's just some methods getting data.
When the model is initialized the database connection is created automatically (just one time for sure). A typical
model method:

```php
public function getAllSongs()
{
    $sql = "SELECT id, artist, track, link FROM song";
    $query = $this->db->prepare($sql);
    $query->execute();

    return $query->fetchAll();
}
```

## Configuration

Index.php holds the configs for a theoretical development and production environment, like this. Self-explaining.

```php
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'debug' => true,
        'database' => array(
            'db_host' => 'localhost',
            'db_port' => '',
            'db_name' => 'mini',
            'db_user' => 'root',
            'db_pass' => '!!!Acid123'
        )
    ));
});
```

## Environment switch (development / test / production)

TODO

## Before/after hooks

Slim can perform things at certain points in the lifetime of an application instance, for example *before* everything
is started. MINI uses this to perform SASS-to-CSS compiling and CSS / JS minification via external tools (loaded via
Composer btw). This is inside the above development enviroment configuration to make sure these actions are not made
in production for sure.

```php
    $app->hook('slim.before', function () use ($app) {

        // SASS-to-CSS compiler @see https://github.com/panique/laravel-sass
        SassCompiler::run("scss/", "css/");

        // CSS minifier @see https://github.com/matthiasmullie/minify
        $minifier = new MatthiasMullie\Minify\CSS('css/style.css');
        $minifier->minify('css/style.css');

        // JS minifier @see https://github.com/matthiasmullie/minify
        $minifier = new MatthiasMullie\Minify\JS('js/application.js');
        $minifier->minify('js/application.js');
    });
```

## Why $_POST['x'] instead of Slim's post/get handler ?

Because it's simpler and more native. Feel free to use the Slim handlers if this fits more your workflow.

## Why is the deletion of a song not made with a DELETE request ?

Because (against popular opinion) HTML4/5 does not support other HTTP methods than GET/POST ([but the browsers themselves
do](http://stackoverflow.com/questions/16805956/why-dont-browsers-support-put-and-delete-reqests-and-when-will-they)).
The most easy workaround is doing this with GET/POST. Please write a ticket if I'm totally wrong here.

## Glitches

1. `/songs/` is not the same as `/songs` !

## Useful: Organize view templates in sub-folders

It's possible to organize the view templates for sure, simply do `$app->render('folder1/folder2/index.twig');`.

## Useful: Multiple router files

When all the routes in index.php are too much for you: Create a folder `routers`, put your route(r)s into files like 
`xxx.router.php` and load them like this:

```php
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}
```

## Useful: Environment switch (develop / test / production)

TODO: im apache definen

```php
$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => $twigView,
    'view.path' => '../view/',
    'mode' => 'development' // <-- define environment
));

// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});
```

## Useful: get URL inside view (1)

Twig can get the URL, use this in the app

```php
$twig->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
```

and then use `{{ baseUrl() }}` in the view template.

## Useful: get URL inside view (2)

Or manually add the `baseUrl`: 

```php
$app->hook('slim.before', function () use ($app) {
    $app->view()->appendData(array('baseUrl' => '/base/url/here'));
});
```

and use it in the view template via `{{ baseUrl }}`. 
More [here](http://stackoverflow.com/questions/11481210/slim-framework-base-url).

## Scripts used

TODO

SASS Compiler
https://github.com/panique/php-sass

CSS / JS Minifier
http://www.mullie.eu/dont-build-your-own-minifier/

## Interesting

http://de.slideshare.net/jeremykendall/keeping-it-small-getting-to-know-the-slim-php-microframework

Injecting stuff into $app:
http://www.slimframework.com/news/version-230

Slim app
https://github.com/ccoenraets/wine-cellar-php

## TODO links useful for futher development (Slim-related)

https://github.com/indieisaconcept/slim-bower-server/blob/master/app/config.php

Route URL Rewriting / Installation
http://docs.slimframework.com/#Route-URL-Rewriting
