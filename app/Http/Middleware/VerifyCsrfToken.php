<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF 白名单
     *
     * @var array
     */
    protected $except = [
        // 由于我们这个 URL 是给支付宝服务器调用的，肯定不会有 CSRF Token
        'payment/alipay/notify',
        'payment/wechat/notify',
    ];
}
