<?php

namespace App\Http\Controllers;

use Mail;
use Cache;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    /**
     * 激活链接
     * 当发送注册激活邮件时，我们会生成一个随机字符串，
     * 然后以邮箱为 Key、随机字符串作为值保存在缓存中，邮箱和这个随机字符串会作为激活链接的参数。
     * 当用户点击激活链接时，我们只需要从缓存中取出对应的数据并判断是否一致就可以确定这个激活链接是否正确。
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Exception
     */
    public function verify(Request $request)
    {
        // 从 url 中获取'email' 和 'token' 两个参数
        $email = $request->input('email');
        $token = $request->input('token');

        // 如果有一个为空说明不是一个合法的验证连接, 直接抛出异常
        if (!$email || !$token) {
            throw new InvalidRequestException('验证连接不正确');
        }

        // 从缓存中读取数据, 我们把url 中获取的'token'与缓存中的值作对比
        // 如果缓存不存在或者返回的值与 url 中的 'token' 不一致就抛出异常
        if ($token != Cache::get('email_verification_' . $email)) {
            throw new InvalidRequestException('验证链接不正确或已过期');
        }

        // 根据邮箱从数据库中获取对应的用户
        // 通常来说能通过 token 校验的情况下不可能出现用户不存在
        // 但是为了代码的健壮性我们还是需要做这个判断
        if (!$user = User::where('email', $email)->first()) {
            throw new InvalidRequestException('用户不存在');
        }

        // 将指定的 key 从缓存中删除，由于已经完成了验证，这个缓存就没有必要继续保留。
        Cache::forget('email_verification_' . $email);

        // 最关键的，要把对应用户的 `email_verified` 字段改为 `true`。
        $user->update(['email_verified' => true]);

        // 最后告知用户邮箱验证成功。
        return view('pages.success', ['msg' => '邮箱验证成功']);
    }

    /**
     * 用户主动请求发送激活邮件
     *
     * 有的时候可能因为网络原因或者用户邮箱原因，用户没有收到激活邮件，这个时候我们还需要一个手动发送激活邮件的入口
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Exception
     */
    public function send(Request $request)
    {
        $user = $request->user();

        // 判断用户是否已经激活
        if ($user->email_verified) {
            throw new InvalidRequestException('你已经验证过邮箱了');
        }

        // 调用 notify() 方法用来发送我们定义好的通知类
        $user->notify(new EmailVerificationNotification());

        return view('pages.success', ['msg' => '邮箱发送成功']);
    }
}
