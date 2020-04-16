<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
//        if ($this->auth->guard($guard)->guest()) {
//            return response('Unauthorized.', 401);
//        }
//        $user = $this->auth->guard($guard)->user();
//        // 添加对应的访问记录
//        $redis = Redis::connection();
//        $redis->lpush(RedisKeys::KEY_USER_VISIT, [json_encode(['user_id'=>$user['id'],'time'=>date("Y-m-d H:i:s"),'hall_id'=>HallDomainService::getHallID()])]);
//        // 检查账户状态(0禁用1启用)
//        UserService::checkUserStatus($user);
//        return $next($request);
    }

}
