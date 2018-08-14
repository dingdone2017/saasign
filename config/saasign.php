<?php
/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/7
 * Time: 10:43
 */
return [
    'secret' => [
        'default' => 'secret',
        'h5'    => 'secret for h5',
        'weapp'    => 'secret for weapp',
    ],
    'cacheTime'=> 3600,
    'database' => 'mysql',
    'clientSource' => ['h5','weapp','android','ios'],
    'platForm' => ['education','media','commerance','duanshu'],
    'memberKey' => [
        'user_id', 'nick_name', 'avatar', 'group', 'flatform'
    ],
    'ddCore'  => [
        'secret'  => env('DD_CORE_API_SECRET','secret'),
        'key' => env('DD_CORE_API_KEY','key'),
        'host'  => env('DD_CORE_HOST','host'),
        'protocol'  => 'http://',
        'api' => [
            'validate' => '/manager_api/members/h5/validate/',
        ],
    ]
];