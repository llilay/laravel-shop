<?php

/**
 * 自定义辅助函数
 *
 * 使用 composer 的 autoload 功能来自动引入,打开 composer.json 文件，并找到 autoload 段, 添加
 * "files": [
 * "bootstrap/helpers.php"
 * ]
 *
 *
 */

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function ngrok_url($routeName, $parameters = [])
{
    // 开发环境，并且配置了 NGROK_URL
    if (app()->environment('local') && $url = config('app.ngrok_url')) {
        // route() 函数第三个参数代表是否绝对路径
        return $url.route($routeName, $parameters, false);
    }

    return route($routeName, $parameters);
}