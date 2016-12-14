<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-13
 * Time: 下午3:34
 */

namespace liwd;

class Response
{
    private static $usuallyCode = [
        // 2xx
        200 => 'OK',

        // 3xx
        304 => 'Not Modified',

        // 4xx
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',

        // 5xx
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        504 => 'Gateway Timeout'
    ];

    public function notAllowed()
    {
        $this->code(403);
    }

    public function code($code)
    {
        $code = (int) $code;

        $headerString = 'HTTP/1.1 ' . $code . ' ' . self::$usuallyCode[$code] . '!';

        header($headerString);
        exit();
    }

    public function json($body)
    {
        header('Content-Type: application/json;');

        if (is_array($body)) {
            $body = json_encode($body);
        }

        $this->body($body);
    }

    public function body($body)
    {
        echo (string) $body;
        exit();
    }
}