<?php
/* Set base */
chdir(__DIR__);
if ( ! defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }

/* Load vendor code for routing, etc from composer includes */
include_once(__DIR__ . '/../vendor/autoload.php');

/* Load site specific code for sessions, db, users, etc. */
include_once(__DIR__ . '/../core/ignition.php');

/* Load site router */
include_once(__DIR__ . '/../engines/routes.php');

error_log('wow');
error_log($_SERVER['REQUEST_METHOD']);
error_log($_SERVER['SCRIPT_FILENAME']);

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;

try {
    $dispatcher = new Dispatcher($router->getData());
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    echo $response;
} catch (Exception $e) {
    if (get_class($e) == "Phroute\Phroute\Exception\HttpRouteNotFoundException") {
        // 404 Error - Just force redirect to main page
        header('Location: /');
        exit();
    } else {
        // Method error
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}