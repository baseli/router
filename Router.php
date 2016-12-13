<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 2016/11/10
 * Time: 22:11
 */

namespace liwd;

use SplQueue;

class Router
{
    private $server;
    private $request;
    private $matchVariable = '/\:\w+/';
    private $matchedStr = '\w+';
    private $routeQueue;

    public function __construct()
    {
        $this->server = new Server($_SERVER);
        $this->request = new Request();
        $this->routeQueue = new SplQueue();
    }

    public function on($method, $pattern, $useDir = false, $callback)
    {
        // adjust the method, if not include, return 404
        $this->adjustMethod($method);


    }

    /**
     * 判断是否是允许访问的http方法
     * @param $method
     */
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

    /**
     * 根据匹配规则去匹配URL
     * @param $pattern
     * @param $useDir
     * @return bool
     */
    public function adjustPattern($pattern, $useDir)
    {
        $selfUri = $this->server->getUri();
        $isRegular = $this->checkIsRegular($pattern);

        if (false !== $useDir) {
            $diff = $this->getDifferenceInDocumentRootAndDirectory();

            $selfUri = substr($selfUri, strlen($diff), strlen($selfUri));
        }

        if (!$isRegular) {
            $matchPattern = preg_replace($this->matchVariable, $this->matchedStr, $pattern);
            $matchPattern = str_replace('/', '\/', $matchPattern);
            $matchPattern = '/^' . $matchPattern . '$/';
        } else {
            $matchPattern = $this->addStartAndEndPattern($pattern);
        }

        $result = (boolean) preg_match($matchPattern, $selfUri);

        return $result;
    }

    /**
     * 将参数进行匹配，并进行保存
     * @param $pattern
     * @param $uri
     */
    public function matchPattern($pattern, $uri)
    {
        $matchedParam = [];

        $patternExplode = explode('/', $pattern);
        $uriExplode = explode('/', $uri);

        $patternExplode = array_map(function($value) {
            if (preg_match($this->matchVariable, $value)) {
                return substr($value, 1);
            } else {
                return null;
            }
        }, $patternExplode);

        foreach ($patternExplode as $key => $value) {
            if (null !== $value) {
                $this->request->setItem($value, $uriExplode[$key]);
            }
        }
    }

    /**
     * 判断是否是平常的正则还是:pattern这种
     * @param $pattern
     * @return bool
     */
    private function checkIsRegular($pattern)
    {
        $isRegular = preg_match($this->matchVariable, $pattern);
        $isRegular = (boolean) !$isRegular;

        return $isRegular;
    }

    /**
     * 给自己写的正则加上开头和结尾,从而保证能准确匹配
     * @param $pattern
     * @return string
     */
    private function addStartAndEndPattern($pattern)
    {
        $startPosition = strpos($pattern, '^');
        $endPosition = strpos($pattern, '$');
        $patternLength = strlen($pattern);

        // 对匹配正则中的(/)进行转义
        $pattern = str_replace('/', '\/', $pattern);

        if (false === $startPosition) {
            $pattern = '/^' . $pattern;
        } elseif (0 === $startPosition) {
            $pattern = '/' . $pattern;
        }

        if (false === $endPosition) {
            $pattern = $pattern . '$/';
        } elseif ($endPosition == ($patternLength - 1)) {
            $pattern = $pattern . '/';
        }

        return (string) $pattern;
    }

    /**
     * 获取uri中去除目录的部分
     * @return string
     */
    private function getDifferenceInDocumentRootAndDirectory()
    {
        $documentRoot = $this->server->getDocumentRoot();
        $directoryName = $this->server->getDirName();

        $uriLength = strlen($directoryName);
        $documentRootLength = strlen($documentRoot);

        return substr($directoryName, $documentRootLength, $uriLength);
    }
}