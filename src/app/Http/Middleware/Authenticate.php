<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Redis;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->auth->guard($guards)->guest()) {
            return response()->json(['error' => 'Unauthorized','code'=>'401'], 401);
        }
//        $redis = Redis::connection()


//        $this->authenticate($request, $guards);
//        exit(var_dump(5555));
//        exit(var_dump($guards));

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
//    protected function authenticate($request, array $guards)
//    {
//        if (empty($guards)) {
//            $guards = [null];
//        }
//
//        foreach ($guards as $guard) {
//            if ($this->auth->guard($guard)->check()) {
//                return $this->auth->shouldUse($guard);
//            }
//        }
//
//        $this->unauthenticated($request, $guards);
//    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
//    protected function unauthenticated($request, array $guards)
//    {
//        throw new AuthenticationException(
//            'Unauthenticated.', $guards, $this->redirectTo($request)
//        );
//    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
//    protected function redirectTo($request)
//    {
//        if (! $request->expectsJson()) {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//    }
}
