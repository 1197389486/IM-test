<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Exceptions\CustomException;


class ResponseMiddleware
{
    // 统一数据格式
    public function handle(Request $request, Closure $next)
    {
        // 跨域处理
        if ($request->method() === 'OPTIONS') {
            return response("", 204, access_header());
        }
        $response = $next($request)->withHeaders(access_header());
        // 自定义错误
        if ($response->exception instanceof CustomException) {
            return $response;
        }
        $status = $response->status();
        $content = $response->getOriginalContent();
//        exit(var_dump($content));
        switch ($status) {
            case Response::HTTP_OK://200
                $response->setContent(output($content,true));
                break;
            case 401://401
//            case SysCode::FORBIDDEN://403
//            case SysCode::NOT_FOUND://404
//                $response->setContent(output('', $status));
//                break;
//            case Response::HTTP_METHOD_NOT_ALLOWED://405
//                $response->setContent(output('', SysCode::NOT_FOUND))->setStatusCode(SysCode::NOT_FOUND);
//                break;
//            case Response::HTTP_UNPROCESSABLE_ENTITY://422
//                $response->setContent(output($content, SysCode::INVALID_ARGUMENT))->setStatusCode(Response::HTTP_OK);
//                break;
        }
        return                 $response->setContent(output($content,true));
    }
}
