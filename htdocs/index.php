<?php

/**
 * Bootstrap the framework and handle the request and send the response.
 */

declare(strict_types=1);

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter as Emitter;
use pereriksson\Controllers\ErrorController;
use pereriksson\Util\Util;
use FastRoute\RouteCollector;
use pereriksson\Session\PhpSession;
use pereriksson\Http\PhpHttpClient;

/**
 * Bootstrapping
 *
 * Start with bootstrapping and starting up the essentials.
 */
// Get a defined to point at the installation directory
define("INSTALL_PATH", realpath(__DIR__ . "/.."));

// Get the autoloader
require INSTALL_PATH . "/vendor/autoload.php";

error_reporting(-1);                // Report all type of errors
ini_set("display_errors", "1");     // Display all errors

$session = new PhpSession();
$session->startUniqueSession();

$http = new PhpHttpClient();


/**
 * Router
 *
 * Extract the path and route it to its handler.
 */
$method = $http->getAllServer()["REQUEST_METHOD"];
$util = new Util($http);
$path   = $util->getRoutePath();

// Load the routes from the configuration file
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $router) {
    $router->addGroup("/", function (RouteCollector $router) {
        $router->addRoute("GET", "", ["\pereriksson\Controllers\HomeController", "index"]);
    });

    $router->addGroup("/session", function (RouteCollector $router) {
        $router->addRoute("GET", "", ["\pereriksson\Controllers\SessionController", "index"]);
        $router->addRoute("POST", "", ["\pereriksson\Controllers\SessionController", "destroy"]);
    });

    $router->addGroup("/twentyone", function (RouteCollector $router) {
        $router->addRoute("GET", "", ["\pereriksson\Controllers\TwentyOneController", "index"]);
        $router->addRoute("POST", "", ["\pereriksson\Controllers\TwentyOneController", "action"]);
    });

    $router->addGroup("/yatzy", function (RouteCollector $router) {
        $router->addRoute("GET", "", ["\pereriksson\Controllers\YatzyController", "index"]);
        $router->addRoute("POST", "", ["\pereriksson\Controllers\YatzyController", "action"]);
    });
});

// Use the router to find the callback for the route path and retrieve
// the response.
$response = null;
$routeInfo = $dispatcher->dispatch($method, $path);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = (new ErrorController())->do404();
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $response = (new ErrorController())->do405($allowedMethods);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $vars[1] = new PhpSession();
        $vars[2] = new PhpHttpClient();
        $vars[0] = new Util($vars[2]);

        if (is_callable($handler)) {
            if (is_array($handler)
                && is_string($handler[0])
                && class_exists($handler[0])
            ) {
                $obj = new $handler[0]($vars[0], $vars[1], $vars[2]);
                $action = $handler[1];
                $response = $obj->$action();
            } else {
                $response = call_user_func_array($handler, $vars);
            }
        } else if (is_string($handler) && class_exists($handler)) {
            $rc = new \ReflectionClass($handler);
            if ($rc->hasMethod("__invoke")) {
                $obj = new $handler;
                $response = $obj();
            }
        }
        break;
}

// Send the reponse
if (is_null($response)) {
    echo "The response object is null.";
} else if (is_string($response)) {
    echo $response;
} else {
    (new Emitter())->emit($response);
}
