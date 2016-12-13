<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-12
 * Time: 下午1:58
 */

use \liwd\Router;

$route = new Router();

$route->on(['get', 'post'], '/:controller/:method', true, function($request, $response) {
    return $request->controller;
});

$route->dispatch();
