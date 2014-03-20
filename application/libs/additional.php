<?php

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * This is the "additional class". For make pretty error with whoops and logging application access with monolog.
 */
class Additional
{
    /**
     * @var null Message
     */
    private static $message = null;

    /**
     * @var null Instance
     */
    private static $instance = null;

    public static function error()
    {
        // load whoops and register the pretty handler
        // @see https://github.com/filp/whoops/
        self::$instance = new Run();
        self::$message  = new PrettyPageHandler();
        self::$message->setPageTitle('Whoops! There was a problem.');
        self::$message->setEditor('sublime');
        
        // check if AJAX requests, returns information on them as a JSON string
        if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            self::$instance->pushHandler(new JsonResponseHandler());
        } else {
            self::$instance->pushHandler(self::$message);
        }

        return self::$instance->register();
    }

    public static function log()
    {
        // define log access message, such as : ip address, time access, referrer, query string, request url and user agent
        self::$message = array(
                            'IP'          => getenv( 'REMOTE_ADDR' ),
                            'TIME'        => date( 'M j G:i:s Y' ),
                            'REFERRER'    => getenv( 'HTTP_REFERER' ),
                            'QUERYSTRING' => getenv( 'QUERY_STRING' ),
                            'REQUESTURL'  => getenv( 'REQUEST_URI' ),
                            'USERAGENT'   => getenv( 'HTTP_USER_AGENT' )
                        );

        // load monolog and create a log channel
        self::$instance = new Logger('access');
        self::$instance->pushHandler(new StreamHandler(PATH_LOG.'access.log', Logger::INFO));
        self::$instance->addInfo('DATA:', self::$message);
    }
}