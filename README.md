# 学习用路由
> 前段时间,发现项目中使用路由有时候不是太好用,如遇到目录的时候,并不会理目录,所以决心写一个可以设置,从而将目录去掉的路由

## 服务器环境的配置
### `apache`
需要开启伪静态,然后将所有的请求交给某个单入口文件,如下
```shell
<virtualHost *:9999>
DocumentRoot "/home/liwd/code/selfrouter/"
<directory "/home/liwd/code/selfrouter">
    Options Indexes FollowSymLinks
    AllowOverride all
    DirectoryIndex test.php
    Require all granted

    <IfModule mod_rewrite.c>
        RewriteEngine On

        RewriteCond %{REQUEST_FILENAME}% !-d
        RewriteCond %{REQUEST_FILENAME}% !-f
        RewriteRule ^ index.php [L]
    </Ifmodule>
</directory>
</VirtualHost>
```
### `nginx`
```shell
location somePattern {
    try_files $uri $uri/ /index.php?$query_string;
}
```
## 使用方法
### 克隆项目
```
git clone https://github.com/baseli/selfrouter.git
```
### 开始编写
1. 引入`autoload.php`
2. 这里给出一个示例
```php
<?php
use \liwd\Router;

include 'autoload.php';

$route = new Router();

$route->on(['get', 'post'], '/:controller/:method', true, function($request, $response) {
    return $request->controller;
});

$route->dispatch();
```
上面的参数说明：<br/>
第一个参数是该接口允许访问的http方法<br/>
第二个参数是如何匹配到该条规则<br/>
第三个参数是是否忽略目录<br/>
第四个参数是回调函数<br/>

### 常用方法
#### `request`
```
param => get,post参数可以通过该接口访问
body => http头的body部分
cookieParam => 获取cookie中的参数
file => 获取$_FILES里边的内容
```
#### `request`使用示例
```php
// 获取test参数内容,默认值为liwd
$req->param('test', 'liwd');

$req->body();
// 同param参数，获取cookie中的test内容，默认为demo
$req->cookieParam('test', 'demo');

$req->file();
```
#### `response`
```
notAllowed => 403
code => 常见的一些http头，包含(200, 304, 401, 402, 403, 404, 500, 502, 504)
```
