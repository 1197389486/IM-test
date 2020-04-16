<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'register']]);
    }

    //发送消息
    public function sendMessage(Request $request)
    {
//        $this->validate($request, [
//            'id' => 'required',
//            'type' => 'required',
//            'target_id' => 'required',
//            'content' => 'required',
//        ]);

        $data = request(['user_id', 'type', 'target_id', 'content']);
        $data = UserService::SendMessage(Auth::user(), $data['type'], $data['target_id'], $data['content'], $data['user_id']);
        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);
    }

    //获取主页列表数据
    public function getHomeMessage(Request $request)
    {
        $data = UserService::getIndexListData(Auth::user());
        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);
    }

    //获取聊天框好友消息
    public function getMessageList(Request $request)
    {
        $request = request(['user_id', 'chat_id']);
        $data = UserService::getMesageDetailList($request['user_id'],$request['chat_id']);
        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);

    }

    //添加好友到聊天室
    public function addFriend(Request $request)
    {

        //type 1-新增 2-剔除
        $request = request(['user_id', 'chat_id', 'user_list', 'type']);
        $data = UserService::AddGroup($request['user_id'], $request['user_list'], $request['type'], $request['chat_id']);

        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);
    }

    //获取我的好友通讯录
    public function getMyfriendList(Request $request)
    {
        $data = UserService::getFriendsList(Auth::user());
        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);

    }

    //添加通讯录好友
    public function addBookFriend(Request $request)
    {
        $request  = request(['user_id','friend_id']);
        $data = UserService::addFriend($request['user_id'],$request['friend_id']);
        return response()->json([
            'code' => 0,
            'msg' => "",
            'data' => $data
        ]);
    }

    //todo 退出群聊


}

