<?php
/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/13
 * Time: 18:13
 */

namespace Dingdone2017\Saasign;


use GuzzleHttp\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class Saasseion
{
    private $config;
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    private function checkUserLogin($sessionId)
    {
        $param = [
            'session_id' => $sessionId
        ];
        $http = new Client([
            'headers' => $this->getHeader(),
            'body'  => \GuzzleHttp\json_encode($param)
        ]);
        $client = $http->request( 'GET', $this->getThirdUri('ddCore','validate',[]));
        $response = $client->getBody()->getContents();

        if( $response && $client->getStatusCode() == 200 ) {
            $result = \GuzzleHttp\json_decode($response);
            if( $result->error_code ) {
                $res = new Response($response);
                throw new HttpResponseException($res);
            }
            return true;
        }
        throw new AuthenticationException;
    }


    public function isLogin()
    {
        if (!$sessionId = '321312312312') {
            return false;
        }
        $checkUser = $this->checkUserLogin($sessionId);
        return $checkUser;
    }

    private function getHeader()
    {
        $apiKey     = $this->config['ddCore']['key'];
        $apiSecret = $this->config['ddCore']['secret'];
        $apiVersion = '1.0';
        $timestamp = microtime(true);
        $sign = $apiKey.'&'.$apiSecret.'&'.$apiVersion.'&'.$timestamp;

        $header = array(
            'X-API-KEY'       => $apiKey,
            'X-API-TIMESTAMP' => $timestamp,
            'X-API-VERSION'   => $apiVersion,
            'X-API-SIGNATURE' => sha1($sign),
            'CLIENT_ID'       => request()->header('X-CLIENT-ID'),
            'Content-Type'    => 'application/json',
        );

        return $header;
    }

    private function getThirdUri($third, $path, $params = []) {
        if ( !$this->config[$third]) {
            return false;
        }
        $config = $this->config[$third];
        if( !isset($config['api'][$path]) ) {
            return false;
        }
        $uri_path = $config['api'][$path];
        if($params){
            preg_match_all('/\{\w{1,}\}/', $uri_path, $matches);
            if(isset($matches[0]) && count($matches[0])){
                $matches = $matches[0];
                foreach($matches as $var){
                    $uri_path = str_replace($var, array_get($params, trim($var, '{}'), -1), $uri_path);
                }
            }
        }
        return $config['protocol'].$config['host'].$uri_path;
    }
}