<?php
/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/2
 * Time: 10:55
 */

namespace Dingdone2017\Saasign;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class Saasign
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * 校验头部信息
     * @param Request $request
     * @return string
     */
    public function sign(Request $request)
    {
        $data = [
            'appkey' => $request->header('Z-API-KEY'),
            'appsecret' => $this->getSecret($request->header('X-CLIENT-SOURCE')),
            'timestape' => $request->header('X-API-TIMESTAPE'),
            'member' => $request->header('X-MEMBER'),
        ];
        $str = http_build_query($data);
        return $this->encrpy($str);
    }

    private function getSecret($client_source)
    {
        if(isset($this->config['secret'][$client_source])){
            return $this->config['secret'][$client_source];
        }
        return $this->config['secret']['default'];
    }

    /**
     * 签名校验
     * @param Request $request
     * @param $sign
     * @return bool
     */
    public function check(Request $request)
    {
        if( $request->header('X-API-SIGNATURE') !== $this->sign($request) ) {
            return false;
        }
        return true;
    }

    /**
     * 签名加密方式
     * @param string $str
     * @return string
     */
    protected function encrpy($str = '')
    {
        return sha1($str);
    }


    /**
     * 解析会员信息
     * @param Request $request
     * @return bool
     */
    public function decode($member)
    {
        $member_str = base64_decode($member);
        parse_str($member_str,$member_arr);
        if( $member_arr && is_array($member_arr)){
            foreach ($member_arr as $item=>$value){
                $member_arr[$item] = $value ? trim($value) : '';
            }
        }
        return $member_arr;
    }

    /**
     *
     */
    protected function checkMember()
    {

    }

    public function checkPlatform($platform)
    {
        if( !$platform ) {
            return false;
        }
        if( in_array($platform,$this->config['platForm'])){
            return true;
        }
        return false;
    }

    public function checkClientSource($source){
        if( !$source ) {
            return false;
        }
        if( in_array($source,$this->config['clientSource'])){
            return true;
        }
        return false;
    }
}