<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfEmailVerified
{
    /**
     * 我们希望用户在验证邮箱之后才能正常使用我们系统的功能，当用户尚未验证邮箱时，访问其他页面都会被重定向到一个提示验证邮箱的页面
     * 对于这种需求我们可以通过中间件来解决，把需要验证邮箱的路由放到拥有这个中间件的路由组中，当用户访问这些路由时会先执行中间件检查是否验证了邮箱。
     *
     * 当中间件被执行时，Laravel 会调用中间件的 handle 方法，第一个参数是当前请求对象，第二个参数是执行下一个中间件的闭包函数。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->email_verified) {
            // 如果是 AJAX 请求，则通过 JSON 返回
            if ($request->expectsJson()) {
                return response()->json(['msg' => '请先验证邮箱'], 400);
            }

            return redirect(route('email_verify_notice'));
        }

        return $next($request);
    }
}
