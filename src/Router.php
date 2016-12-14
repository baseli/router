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
    private $request;
    private $response;
    private $createRoute;

    private $matchVariable = '/\:\w+/';
    private $matchedStr = '\w+';

    public function __construct()
    {
        $this->server = new Server();
        $this->request = new Request();
        $this->response = new Response();
        $this->createRoute = new CreateRoute();
    }

    /**
     * 需要监听的规则
     * @param $method
     * @param $pattern
     * @param bool $useDir
     * @param $callback
     */
    public function on($method, $pattern, $useDir = false, $callback)
    {
        $this->createRoute->setRoute($method, $pattern, $useDir, $callback);
    }

    /**
     * 进行路由的分发
     */
    public function dispatch()
    {
        $result = true;
        $body = '';

        $routeQueue = $this->createRoute->getRoute();

        foreach($routeQueue as $value) {
            $result = $this->adjustAll($value);

            if (true === $result) {
                // 进行相关参数的匹配
                if (true === $value['useDir']) {
                    $uri = $this->getAbsolutePath();

                    $this->matchPattern($value['path'], $uri);
                } else {
                    $this->matchPattern($value['path'], $this->server->getUri());
                }

                if (is_callable($value['callback'])) {
                    $body = $value['callback']($this->request, $this->response);
                }
                break 1;
            }
        };

        if (false === $result) {
            $this->response->notAllowed();
        }

        $this->response->body($body);
    }


    private function adjustAll($route)
    {
        $useDir = $route['useDir'];
        $method = $route['method'];
        $pattern = $route['path'];

        $result = $this->adjustPattern($pattern, $useDir);

        $result = $this->adjustMethod($method) && $result;

        return $result;
    }

    /**
     * 判断是否是允许访问的http方法
     * @param $method
     * @return bool
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
                return false;
            }

            return true;
        } else {
            if (strtoupper($method) != $selfMethod) {
                return false;
            }

            return true;
        }
    }

    /**
     * 根据匹配规则去匹配URL
     * @param $pattern
     * @param $useDir
     * @return bool
     */
    private function adjustPattern($pattern, $useDir)
    {
        $selfUri = $this->removeQueryParamFromUri();
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
    private function matchPattern($pattern, $uri)
    {
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

    private function getAbsolutePath()
    {
        $uri = $this->server->getUri();

        $diff = $this->getDifferenceInDocumentRootAndDirectory();

        $uri = substr($uri, strlen($diff), strlen($uri));

        return $uri;
    }

    /**
     * 去除uri中的参数部分
     * @return string
     */
    private function removeQueryParamFromUri()
    {
        $uri = $this->server->getUri();

        $delimiterPosition = strpos($uri, '?');

        if (false !== $delimiterPosition) {
            $uri = substr($uri, 0, $delimiterPosition);
        }

        return (string) $uri;
    }
}