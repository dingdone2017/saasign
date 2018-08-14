<?php
/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/13
 * Time: 16:25
 */

namespace Dingdone2017\Saasign;


use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Redis;

class HandleSession
{
    public function __construct(Saasseion $saasseion, Member $member)
    {
        $this->saasseion = $saasseion;
        $this->member = $member;
    }

    public function handle(Request $request, Closure $next)
    {
        //必要参数,不可以为空
        if($this->saasseion->isLogin()){
            if( $request->has('userInfo')) {
                $platform = $request->userInfo['platform'];
                $uid = $request->userInfo['user_id'];
                $auth_id = $platform.'_'.$uid;
                if(!Redis::exists('verify:'.$auth_id)){
                    $this->member->updateVerify($auth_id,1);
                    Redis::set('verify'.$auth_id,1);
                }
                return $next($request);
            }
            return $next($request);
        }
        throw new AuthenticationException;
    }
}