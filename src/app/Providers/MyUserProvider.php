<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\Redis;

class MyUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
//        exit(var_dump([$credentials['password'],$user->salt,password_encrypt($plain, $user->salt) === $user->getAuthPassword(),$user->getAuthPassword()]));
        return password_encrypt($plain, $user->salt) === $user->getAuthPassword();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $keys = explode(':', $identifier);
        $token = Auth::getToken()->get();

        $user = parent::retrieveById($keys[0]);
        if (!$this->validateToken($user, $token)) {
            Auth::logout();
            return response()->json(['error' => '登录已过期，请重新登录！！','code'=>'402'], 401);
        }
        return $user;
    }

    public function validateToken($user,$token)
    {
        $redis = Redis::connection();
        $t = $redis->get("UserToken:". $user->id);
        if(empty($t)){
            Auth::logout();
            return response()->json(['error' => '登录已过期，请重新登录！！','code'=>'402'], 401);
        }
        return $token == $t;
    }
}
