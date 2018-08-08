<?php
namespace Hgdev\Saasign;

/**
 * Created by PhpStorm.
 * User: Janice
 * Date: 2018/8/7
 * Time: 17:52
 */
use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class Member
{
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
        $this->connectionName = null;
    }

    public function get($authid)
    {
        $result = $this->getConnection()->table('member')
            ->where('auth_id', $authid)
            ->first();

        if (is_null($result)) {
            return;
        }
        return $result;
    }

    public function create($authid, $user_name = '', $avatar = '',$type = '')
    {
        $this->getConnection()->table('member')->insert([
            'auth_id' => $authid,
            'nick_name' => $user_name,
            'avatar' => $avatar,
            'verify' => 0,
            'type'  => $type,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function update($authid, $user_name = '', $avatar = '',$type = '')
    {
        $this->getConnection()->table('member')->where('auth_id','=',$authid)->update([
            'nick_name' => $user_name,
            'avatar' => $avatar,
            'verify' => 0,
            'type'  => $type,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateVerify($authid, $verify = 0)
    {
        $this->getConnection()->table('member')->where('auth_id','=',$authid)->update([
            'verify' => $verify,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getConnection()
    {
        return $this->resolver->connection($this->connectionName);
    }

    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function getConnectionName()
    {
        return $this->connectionName;
    }
}