<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-13
 * Time: 下午3:21
 */

namespace liwd;

use SplQueue;

class CreateRoute
{
    private $routeQueue;

    public function __construct()
    {
        $this->routeQueue = new SplQueue();
    }

    public function setRoute($method, $path, $useDir, $callback)
    {
        $this->routeQueue->push([
            'method' => $method,
            'path' => $path,
            'useDir' => $useDir,
            'callback' => $callback
        ]);
    }

    public function getRoute()
    {
        yield $this->routeQueue->pop();
    }
}