![MINI2 - A naked barebone PHP application](Mini/_install/mini2.png)

# MINI 2

## What is MINI 2 ?

An extremely simple PHP barebone / skeleton application built on top of the wonderful Slim router / micro framework
[[1](http://www.slimframework.com/)] [[2](https://github.com/codeguy/Slim)] [[docs](http://docs.slimframework.com)].

MINI is by intention as simple as possible, while still being able to create powerful applications. I've built MINI
in my free-time, unpaid, voluntarily, just for my personal commercial and private use and uploaded it on GitHub as it
might be useful for others too. Nothing more. Don't hate, don't complain, don't vandalize, don't bash (this needs to be 
said these days as people treat tiny free open-source private scripts like they paid masses of money for them). 
If you don't like it, don't use it. If you see issues, please create a ticket. In case you want to contribute, please 
create a feature-branch, never commit into master. Thanks :)

Mini currently uses [Slim 2.6.0](https://github.com/codeguy/Slim/releases).

There's also [MINI 1](https://github.com/panique/mini), an earlier version of MINI2, but with totally different code!
Since August 2016 there's also [MINI 3](https://github.com/panique/mini3), an improved version of MINI 1. While MINI2
uses Slim under the hood, MINI 1 and 3 are 100% native PHP.

## Features

- built on Slim
- RESTful routes
- extremely simple: the entire application is just 2 .php files (plus external dependencies plus view templates)
- uses Twig as template engine, others are possible (via Slim packages)
- uses pure PDO instead of ORM (it's easier to handle)
- basic CRUD functions: create, read, update/edit and delete content
- basic search
- basic AJAX demo
- (optional) shows emulated PDO SQL statement for easy debugging
- (optional) compiles SCSS to CSS on the fly
- (optional) minifies CSS on the fly
- (optional) minifies JS on the fly
- (optional) dev/test/production switch

By default MINI allows user access to /public folder. The rest of the application (including .git files, swap files,
etc) is not accessible.

## Requirements

- PHP 5.3+
- MySQL
- mod_rewrite activated, document root routed to /public (tutorial below)

Maybe useful: Simple tutorials on setting up a LAMP stack on 
[Ubuntu 14.04 LTS](http://www.dev-metal.com/installsetup-basic-lamp-stack-linux-apache-mysql-php-ubuntu-14-04-lts/)
and [12.04 LTS](http://www.dev-metal.com/setup-basic-lamp-stack-linux-apache-mysql-php-ubuntu-12-04/).

## License

MIT, so feel free to use the project for everything you like.

## Screenshot

![MINI2 - A naked PHP skeleton application on top of Slim](Mini/_install/mini-screenshot.png)

## Support the project

[![Support the project](Mini/_install/banner-rackspace.png)](http://tracking.rackspace.com/SH1ES)
![Support banner tracking pixel](http://tracking.rackspace.com/aff_i?offer_id=2&aff_id=3472)

## Installation (in Vagrant, 100% automatic)

If you are using Vagrant for your development, then you can install MINI with one click (or one command on the
command line). MINI comes with a demo Vagrant-file (defines your Vagrant box) and a demo bootstrap.sh which 
automatically installs Apache, PHP, MySQL, PHPMyAdmin, git and Composer, sets a chosen password in MySQL and PHPMyadmin
and even inside the application code, downloads the Composer-dependencies, activates mod_rewrite and edits the Apache
settings, downloads the code from GitHub and runs the demo SQL statements (for demo data). This is 100% automatic,
you'll end up after +/- 5 minutes with a fully running installation of MINI2 inside an Ubuntu 14.04 LTS Vagrant box.

To do so, put `Vagrantfile` and `bootstrap.sh` from `Mini/_vagrant` inside a folder (and nothing else). 
Do `vagrant box add ubuntu/trusty64` to add Ubuntu 14.04 LTS ("Trusty Thar") 64bit to Vagrant (unless you already have 
it), then do `vagrant up` to run the box. When installation is finished you can directly use the fully installed demo 
app on `192.168.33.77`. As this just a quick demo environment the MySQL root password and the PHPMyAdmin root password 
are set to `12345678`, the project is installed in `/var/www/html/myproject`.

## Auto-Installation on Ubuntu 14.04 LTS (in 10 seconds)

You can install MINI2 including Apache, MySQL, PHP and PHPMyAdmin, mod_rewrite, Composer, all necessary settings and even the passwords inside the configs file by simply downloading one file and executing it, the entire installation will run 100% automatically. See the bootstrap.sh file for more infos (and the default passwords). Keep in mind that this is quick dev setup, not a perfect choice for production for sure. This should work perfectly in every naked Ubuntu 14.04 LTS.

Download the installer script

```bash
wget https://raw.githubusercontent.com/panique/mini2/master/Mini/_vagrant/bootstrap.sh
```

Make it executable [is this necessary ?]

```bash
chmod +x bootstrap.sh
```

Run it! Boooooom. Give it some minutes to perform all the tasks. And yes, you can thank me later :)

```bash
sudo ./bootstrap.sh
```

## Installation (manual)

##### 1. Activate mod_rewrite and ...

Tutorials for [Ubuntu 14.04 LTS](http://www.dev-metal.com/enable-mod_rewrite-ubuntu-14-04-lts/) and 
[Ubuntu 12.04 LTS](http://www.dev-metal.com/enable-mod_rewrite-ubuntu-12-04-lts/).
 
##### 2. ... route all requests to /public folder of the script
 
Change the VirtualHost file from `DocumentRoot /var/www/html` to `DocumentRoot /var/www/html/public` and from
`<Directory "/var/www/html">` to `<Directory "/var/www/html/public">`. Don't forget to restart. By the way this is also 
mentioned in the official Slim documentation, but 
[hidden quite much](http://docs.slimframework.com/#Route-URL-Rewriting).

##### 3. Edit the development database configs

Inside `public/index.php` edit the database credentials and fill in your values.

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

Index.php holds the configs for a development environment. Self-explaining.

```php
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'debug' => true,
        'database' => array(
            'db_host' => 'localhost',
            'db_port' => '',
            'db_name' => 'mini',
            'db_user' => 'root',
            'db_pass' => '12345678'
        )
    ));
});
```

### Environment switch (development / test / production)

To implement a production config simply copy the whole config block above and replace *development* with *production*.
Add an environment variable to your Apache config. More [here](http://docs.slimframework.com/#Application-Modes) and
[here](http://kb.mediatemple.net/questions/36/Using+Environment+Variables+in+PHP#gs).

### Before/after hooks

Slim can perform things at certain points in the lifetime of an application instance, for example *before* everything
is started. MINI uses this to perform SASS-to-CSS compiling and CSS / JS minification via external tools (loaded via
Composer btw). This is inside the above development environment configuration to make sure these actions are not made
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

#### Why $_POST['x'] instead of Slim's post/get handler ?

Because it's simpler and more native. Feel free to use the Slim handlers if this fits more your workflow.

#### Why is the deletion of a song not made with a DELETE request ?

Because (against popular opinion) HTML4/5 does not support other HTTP methods than GET/POST ([but the browsers themselves
do](http://stackoverflow.com/questions/16805956/why-dont-browsers-support-put-and-delete-reqests-and-when-will-they)).
The most easy workaround is doing this with GET/POST. Please write a ticket if I'm totally wrong here.

#### Glitches

1. `/songs/` is not the same as `/songs` !
2. Writing the initialization in the short syntax like `$app = new \Slim\Slim(array('view' => ...));` has some issues 
and might eventually break your application. Using the syntax like in index.php works fine.
@see http://help.slimframework.com/discussions/questions/954-twig-getenvironment-function-no-longer-available-using-slim-views

#### Useful: Organize view templates in sub-folders

It's possible to organize the view templates for sure, simply do `$app->render('folder1/folder2/index.twig');`.

#### Useful: Multiple router files

When all the routes in index.php are too much for you: Create a folder `routers`, put your route(r)s into files like 
`xxx.router.php` and load them like this:

```php
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}
```

#### Useful: get URL inside view (1)

Twig can get the URL, use this in the app

```php
$twig->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
```

and then use `{{ baseUrl() }}` in the view template.

#### Useful: get URL inside view (2)

Or manually add the `baseUrl`: 

```php
$app->hook('slim.before', function () use ($app) {
    $app->view()->appendData(array('baseUrl' => '/base/url/here'));
});
```

and use it in the view template via `{{ baseUrl }}`. 
More [here](http://stackoverflow.com/questions/11481210/slim-framework-base-url).

#### Scripts used

SASS Compiler
https://github.com/panique/php-sass

CSS / JS Minifier
http://www.mullie.eu/dont-build-your-own-minifier/

#### Interesting

http://de.slideshare.net/jeremykendall/keeping-it-small-getting-to-know-the-slim-php-microframework

Injecting stuff into $app:
http://www.slimframework.com/news/version-230

Slim apps
https://github.com/ccoenraets/wine-cellar-php
https://github.com/xsanisty/SlimStarter

https://github.com/indieisaconcept/slim-bower-server/blob/master/app/config.php

Route URL Rewriting / Installation
http://docs.slimframework.com/#Route-URL-Rewriting

## Change log

- [panique] upgrade from Slim 2.5 to 2.6
- [panique] upgrade from Slim 2.4.3 to 2.5.0
- [sim2github] renamed model path to uppercase to fit PSR-4

## Support the project

[![Support the project](Mini/_install/banner-rackspace.png)](http://tracking.rackspace.com/SH1ES)
