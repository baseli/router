<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-12
 * Time: 下午2:13
 * 收集数据
 */

namespace liwd;

class Request
{
    private $paramData = [];
    private $bodyData = null;
    private $cookieData = [];

    public function __construct()
    {
        $this->paramData = array_merge($_GET, $_POST);
        $this->cookieData = $_COOKIE;
    }

    public function param($key, $default = null)
    {
        return isset($this->paramData[$key]) ? $this->paramData[$key] : $default;
    }

    public function body()
    {
        if (null === $this->bodyData) {
            $this->bodyData = @file_get_contents('php://input');
        }

        return $this->bodyData;
    }

    public function cookieParam($key, $default = null)
    {
        return isset($this->cookieData[$key]) ? $this->cookieData[$key] : $default;
    }

    public function file()
    {
        return $_FILES;
    }

    public function setItem($key, $value)
    {
        $this->$key = $value;
    }

    public function getItem($key)
    {
        return $this->$key;
    }
}