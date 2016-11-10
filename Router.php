<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 2016/11/10
 * Time: 22:11
 */

namespace liwd;

class Router
{
    private $server;

    public function __construct()
    {
        $this->server = new Router($_SERVER);
    }

    public function on($method, $pattern, $callback)
    {
        // adjust the method, if not include, return 404
        $this->adjustMethod($method);


    }

    private function adjustMethod($method)
    {
        $selfMethod = $this->server->getMethod();

        if (is_array($method)) {
            $flag = false;

            foreach ($method as $value) {
                if (strtoupper($value) == $this->server->getMethod()) {
                    $flag = true;
                    break 1;
                }
            }

            if (!$flag) {
                header('HTTP1.1 404 Not Found!');
            }
        } else {
            if (strtoupper($method) != $selfMethod) {
                header('HTTP1.1 404 Not Found!');
            }
        }
    }

    private function adjustPattern($pattern)
    {
        $selfUri = $this->server->getUri();
        $dirName = $this->server->getDirName();
    }
}