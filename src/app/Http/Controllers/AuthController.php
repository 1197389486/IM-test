<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return voids
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //添加redis保存会员token信息
        $redis = Redis::connection();



        $user = Auth::user();
        $redis->set("UserToken:". $user->getJWTIdentifier(), $token, 'EX', 3600*24);

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $username = $request['username'];
        $password = $request['password'];
        $credentials['username'] = $username;
        $credentials['password'] = $password;
        $user = new User($credentials);
        $user->salt = salt();
        $user->email = $request['email'];
        $user->password = password_encrypt($password,$user->salt);
        $user->save();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        //注册成功后，默认每个人一个test好友
        UserService::addFriend($user->id);
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(['code'=>0,'msg'=>'','data'=>auth()->user()]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
//        return ;
        return response()->json(
            [            'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60]
        );
    }



}

