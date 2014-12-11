<?php

/******************************* LOADING & INITIALIZING BASE APPLICATION ****************************************/

// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Load Composer's PSR-4 autoloader (necessary to load Slim, Mini etc.)
require '../vendor/autoload.php';

// Initialize Slim (the router/micro framework used)
$app = new \Slim\Slim();

// and define the engine used for the view @see http://twig.sensiolabs.org
$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("../Mini/view");

/******************************************* THE CONFIGS *******************************************************/

// Configs for mode "development" (Slim's default), see the GitHub readme for details on setting the environment
$app->configureMode('development', function () use ($app) {

    // pre-application hook, performs stuff before real action happens @see http://docs.slimframework.com/#Hooks
    $app->hook('slim.before', function () use ($app) {

        // SASS-to-CSS compiler @see https://github.com/panique/php-sass
        SassCompiler::run("scss/", "css/");

        // CSS minifier @see https://github.com/matthiasmullie/minify
        $minifier = new MatthiasMullie\Minify\CSS('css/style.css');
        $minifier->minify('css/style.css');

        // JS minifier @see https://github.com/matthiasmullie/minify
        // DON'T overwrite your real .js files, always save into a different file
        //$minifier = new MatthiasMullie\Minify\JS('js/application.js');
        //$minifier->minify('js/application.minified.js');
    });

    // Set the configs for development environment
    $app->config(array(
        'debug' => true,
        'database' => array(
            'db_host' => 'localhost',
            'db_port' => '',
            'db_name' => 'mini',
            'db_user' => 'root',
            'db_pass' => 'your_password'
        )
    ));
});

// Configs for mode "production"
$app->configureMode('production', function () use ($app) {
    // Set the configs for production environment
    $app->config(array(
        'debug' => false,
        'database' => array(
            'db_host' => '',
            'db_port' => '',
            'db_name' => '',
            'db_user' => '',
            'db_pass' => ''
        )
    ));
});

/******************************************** THE MODEL ********************************************************/

// Initialize the model, pass the database configs. $model can now perform all methods from Mini\model\model.php
$model = new \Mini\model\model($app->config('database'));

/************************************ THE ROUTES / CONTROLLERS *************************************************/

// GET request on homepage, simply show the view template index.twig
$app->get('/', function () use ($app) {
    $app->render('index.twig');
});

// GET request on /subpage, simply show the view template subpage.twig
$app->get('/subpage', function () use ($app) {
    $app->render('subpage.twig');
});

// GET request on /subpage/deeper (to demonstrate nested levels), simply show the view template subpage.deeper.twig
$app->get('/subpage/deeper', function () use ($app) {
    $app->render('subpage.deeper.twig');
});

// All requests on /songs and behind (/songs/search etc) are grouped here. Note that $model is passed (as some routes
// in /songs... use the model)
$app->group('/songs', function () use ($app, $model) {

    // GET request on /songs. Perform actions getAmountOfSongs() and getAllSongs() and pass the result to the view.
    // Note that $model is passed to the route via "use ($app, $model)". I've written it like that to prevent creating
    // the model / database connection in routes that does not need the model / db connection.
    $app->get('/', function () use ($app, $model) {

        $amount_of_songs = $model->getAmountOfSongs();
        $songs = $model->getAllSongs();

        $app->render('songs.twig', array(
            'amount_of_songs' => $amount_of_songs,
            'songs' => $songs
        ));
    });

    // POST request on /songs/addsong (after a form submission from /songs). Asks for POST data, performs
    // model-action and passes POST data to it. Redirects the user afterwards to /songs.
    $app->post('/addsong', function () use ($app, $model) {

        // in a real-world app it would be useful to validate the values (inside the model)
        $model->addSong(
            $_POST["artist"], $_POST["track"], $_POST["link"], $_POST["year"], $_POST["country"], $_POST["genre"]);
        $app->redirect('/songs');
    });

    // GET request on /songs/deletesong/:song_id, where :song_id is a mandatory song id.
    // Performs an action on the model and redirects the user to /songs.
    $app->get('/deletesong/:song_id', function ($song_id) use ($app, $model) {

        $model->deleteSong($song_id);
        $app->redirect('/songs');
    });

    // GET request on /songs/editsong/:song_id. Should be self-explaining. If song id exists show the editing page,
    // if not redirect the user. Note the short syntax: 'song' => $model->getSong($song_id)
    $app->get('/editsong/:song_id', function ($song_id) use ($app, $model) {

        $song = $model->getSong($song_id);

        if (!$song) {
            $app->redirect('/songs');
        }

        $app->render('songs.edit.twig', array('song' => $song));
    });

    // POST request on /songs/updatesong. Self-explaining.
    $app->post('/updatesong', function () use ($app, $model) {

        // passing an array would be better here, but for simplicity this way it okay
        $model->updateSong($_POST['song_id'], $_POST["artist"], $_POST["track"], $_POST["link"], $_POST["year"],
            $_POST["country"], $_POST["genre"]);

        $app->redirect('/songs');
    });

    // GET request on /songs/ajaxGetStats. In this demo application this route is used to request data via
    // JavaScript (AJAX). Note that this does not render a view, it simply echoes out JSON.
    $app->get('/ajaxGetStats', function () use ($app, $model) {

        $amount_of_songs = $model->getAmountOfSongs();
        $app->contentType('application/json;charset=utf-8');
        echo json_encode($amount_of_songs);
    });

    // POST request on /search. Self-explaining.
    $app->post('/search', function () use ($app, $model) {

        $result_songs = $model->searchSong($_POST['search_term']);

        $app->render('songs.search.twig', array(
            'amount_of_results' => count($result_songs),
            'songs' => $result_songs
        ));
    });

    // GET request on /search. Simply redirects the user to /songs
    $app->get('/search', function () use ($app) {
        $app->redirect('/songs');
    });

});

/******************************************* RUN THE APP *******************************************************/

$app->run();

