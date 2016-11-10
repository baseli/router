<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 2016/11/10
 * Time: 22:17
 */

namespace liwd;

class Router
{
    private $server;

    public function __construct($serverInfo)
    {
        $this->server = $serverInfo;
    }

    public function getUri()
    {
        return $this->server['REQUEST_URI'];
    }

    public function getMethod()
    {
        return strtoupper($this->server['REQUEST_METHOD']);
    }

    public function getDirName()
    {
        return dirname($this->server['SCRIPT_NAME']);
    }
}