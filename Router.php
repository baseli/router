<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 2016/11/10
 * Time: 22:11
 */

namespace liwd;

class Router extends Exception
{
    private $server;
    private $data;
    private $matchVariable = '/\:[a-zA-Z0-9]+/';

    public function __construct()
    {
        $this->server = new Server($_SERVER);
        $this->data = new Data();
    }

    public function on($method, $pattern, $useDir = false, $callback)
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

    public function adjustPattern($pattern, $useDir)
    {
        $selfUri = $this->server->getUri();
        $diff = $this->getDifferenceInDocumentRootAndDirectory();

        if (false !== $useDir) {
            $selfUri = substr($selfUri, strlen($diff), strlen($selfUri));
        }

        return $selfUri;
    }

    private function getDifferenceInDocumentRootAndDirectory()
    {
        $documentRoot = $this->server->getDocumentRoot();
        $directoryName = $this->server->getDirName();

        $uriLength = strlen($directoryName);
        $documentRootLength = strlen($documentRoot);

        return substr($directoryName, $documentRootLength, $uriLength);
    }

}