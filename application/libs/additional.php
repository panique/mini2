<?php

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * This is the "additional class". For make pretty error and logging application access.
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

    /**
     * Initialize whoops
     * @return instance register
     */
    public static function error()
    {
        // initialize
        self::$instance = new Run();
        self::$message  = new PrettyPageHandler();

        // set page title
        self::$message->setPageTitle('Whoops! There was a problem.');
        // set editor
        self::$message->setEditor('sublime');
        
        // check if ajax request push with JsonResponseHandler
        if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            self::$instance->pushHandler(new JsonResponseHandler());
        } else {
            self::$instance->pushHandler(self::$message);
        }

        // register whoops
        return self::$instance->register();
    }

    public static function log()
    {
        // make log access message : ap address, time access, referrer, query string, request url and user agent
        self::$message = array(
                            'IP'          => getenv( 'REMOTE_ADDR' ),
                            'TIME'        => date( 'M j G:i:s Y' ),
                            'REFERRER'    => getenv( 'HTTP_REFERER' ),
                            'QUERYSTRING' => getenv( 'QUERY_STRING' ),
                            'REQUESTURL'  => getenv( 'REQUEST_URI' ),
                            'USERAGENT'   => getenv( 'HTTP_USER_AGENT' )
                        );

        // initialize
        self::$instance = new Logger('access');
        // write log
        self::$instance->pushHandler(new StreamHandler(PATH_LOG.'access.log', Logger::INFO));
        self::$instance->addInfo('DATA:', self::$message);
    }
}