<?php
/**
 * Created by PhpStorm.
 * User: liwd
 * Date: 16-12-13
 * Time: 下午5:37
 */

spl_autoload_register(function($class) {
    $class = str_replace('liwd\\', '', $class);
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php';

    include $filePath;
});
