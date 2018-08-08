<?php
/**
 * User: Janice
 * Date: 2018/8/7
 * Time: 14:20
 */

namespace Dingdone2017\Saasign;

use Closure;
use Illuminate\Support\Facades\Redis;

class HandleSign
{
    public function __construct(Saasign $saasign, Member $member)
    {
        $this->saasign = $saasign;
        $this->member = $member;
    }

    /**
     * @param $request
     * @param 需要的头部信息包括 x-member,x-api-timestap,x-api-key
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //必要参数,不可以为空
        if( !$request->hasHeader('X-API-KEY') ) {
            throw new SaasignException('Param no appkey error.', 403);
        }

        //必要参数,不可以为空
        if( !$request->hasHeader('X-API-SIGNATURE') ) {
            throw new SaasignException('Param no signature error.', 403);
        }

        //校验头部信息的签名是否正取,如果签名信息错误,则说明数据被窜改,直接抛错
        if (! $this->saasign->check($request)) {
            throw new SaasignException('Not allowed.', 403);
        }

        if ( !$this->saasign->checkClientSource($request->header('x-client-source'))) {
            throw new SaasignException('Unknow client source', 403);
        }

        //签名信息正取,获取header中的member信息,解析member数据

        if( $request->hasHeader('X-MEMBER') ) {
            $member = $request->header('X-MEMBER');
            $user = $this->saasign->decode($member);
            if(Redis::exists(md5($member))) {
                $request->merge(['userInfo'=>$user]);
            }
            if($user && isset($user['platform']) && isset($user['user_id'])){
                if ( !$this->saasign->checkPlatform($user['platform'])) {
                    throw new SaasignException('unknow platform', 403);
                }
                $auth_id = $user['platform'].'_'.$user['user_id'];
                if( $this->member->get($auth_id) ){
                    $this->member->update($auth_id,$user['nick_name'],$user['avatar'],$user['type']);
                }else{
                    $this->member->create($auth_id,$user['nick_name'],$user['avatar'],$user['type']);
                }
                Redis::setex(md5($member),3600,json_encode($user));
                //将会员信息作为userInfo传给业务逻辑
                $request->merge(['userInfo'=>$user]);
            }
        }
        return $next($request);
    }
}