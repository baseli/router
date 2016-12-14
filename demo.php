<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-12
 * Time: 下午1:58
 */

include 'autoload.php';

use \liwd\Router;

$route = new Router();

$route->on(['get', 'post'], '/:controller/:method', true, function($request, $response) {
    $controller = $request->controller;

    return $controller;
});

$route->dispatch();
