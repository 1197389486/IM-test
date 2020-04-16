<?php

namespace App\Services;

use App\Models\MessageList;
use App\Models\MessageListDetail;
use App\Models\MessageUserList;
use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Support\Facades\DB;

class UserService
{
    const  MESSAGE_LIST_TYPE = ['person' => 1, 'group' => 2];
    const ENABLE = 1;
    const DISABLE = 2;
    const MESSAGE_LIST_DETAIL_TYPE = ['normal' => 1, 'invite' => 2];

    //获取主页我的消息列表
    static function getIndexListData($userInfo, $mesageId = "")
    {
        //查询群聊消息
        $messageuserIdList = MessageUserList::select(['message_list_id','created_at'])->where(['user_id' => $userInfo->id, 'status' => static::ENABLE])->get()->toArray();
        $mesageIdGroup = array_column($messageuserIdList, 'message_list_id');

        $messageTime = array_combine(array_column($messageuserIdList, 'message_list_id'), array_column($messageuserIdList, 'created_at'));

        $messageDataId = MessageList::select(['id'])->where(function ($query) use ($userInfo) {
            $query->orWhere(['user_id' => $userInfo->id]);
            $query->orWhere(['target_id' => $userInfo->id]);
        })->get()->toArray();

        //查询个人聊天消息数据
        $messageAll = array_column($messageDataId, 'id');
        $messageAll = array_values(array_merge($messageAll, $mesageIdGroup));


        $messageDataList = DB::table('message_list_detail as d')->leftJoin('message_list as l', 'l.id', '=', 'd.message_list_id')
            ->select(['l.id', 'l.name', 'd.content', 'd.created_at', 'd.message_from as user_id', 'd.message_to'])
            ->whereIn('message_list_id', $messageAll)
            ->groupBy("d.id")
            ->get()->toArray();
        $messageDataListArray = [];

        foreach($messageDataList as $kzz =>$vzz){
            $messageDataListArray[] = (array)$vzz;
        }
        $messageDataList = $messageDataListArray;
        $userListId = array_column($messageDataList, 'user_id');
        $userDatalist = [];
        if (empty($userListId)) {
            $userList = User::where('id', $userListId)->get()->toArray();
            foreach ($userList as $kk => $vv) {
                $userDatalist[$vv['id']] = $vv;
            }
        } else {
            $userDatalist = [];
        }

        foreach ($messageDataList as $k => $v) {
            if (isset($messageTime[$v['id']]) && $messageTime[$v['id']] > $v['created_at']) {
                $messageuserIdList[$k]['content'] = '';
            }
            $messageDataList[$k]['username'] = isset($userDatalist[$v['user_id']]['username']) ? $userDatalist[$v['user_id']]['username'] : "testEmptyName";
            $messageDataList[$k]['chat_id'] = $v['id'];
        }
        return $messageDataList;
    }

    //发送消息
    static function SendMessage($user, $type, $targetId, $content, $userId = "")
    {

        $user_id = $user->id;
        if (!empty($userId)) {
            $user_id = $userId;
        }
        //单个人发送消息
        if ($type == 1) {
            $messageInfo = MessageList::where(['user_id' => $user_id, 'target_id' => $targetId, 'type' => 1])->first();
            if (empty($messageInfo)) {
                $messageListModel = new MessageList();
                $messageListModel->user_id = $user_id;
                $messageListModel->type = static::MESSAGE_LIST_TYPE['person'];
                $messageListModel->target_id = $targetId;
                $messageListModel->save();
                $messageId = $messageListModel->id;
            } else {
                $messageId = $messageInfo->id;
            }
            $messageListDetailModel = new MessageListDetail();
            $messageListDetailModel->message_from = $user_id;
            $messageListDetailModel->message_to = $targetId;
            $messageListDetailModel->content = $content;
            $messageListDetailModel->message_list_id = $messageId;
            $messageListDetailModel->save();

        } else {
            $messageInfo = MessageList::where(['user_id' => $user_id, 'type' => 2, 'id' => $targetId])->first();
            $messageId = $messageInfo->id;
            if (empty($messageInfo)) {
                return false;
            }
            $messageListDetailModel = new MessageListDetail();
            $messageListDetailModel->message_from = $user_id;
            $messageListDetailModel->message_to = $targetId;
            $messageListDetailModel->content = $content;
            $messageListDetailModel->message_list_id = $messageId;
            $messageListDetailModel->save();
        }
        return true;


    }

    //添加好友到群组
    static function AddGroup($userId, $user_list, $type, $chat_id = "")
    {
        $user_list = explode(",", $user_list);
        $user_list_array = [];
        foreach ($user_list as $k => $vv) {
            $user_list_array[] = trim($vv);
        }

        if ($type == 1) {
            //新添加群聊时候需要创建对应的聊天
            if (empty($chat_id)) {
                $messageListModel = new MessageList();
                $messageListModel->user_id = $userId;
                $messageListModel->type = static::MESSAGE_LIST_TYPE['group'];
//                $messageListModel->target_id = "";
//                $messageListModel->user_list = implode(",", $user_list_array);
                $messageListModel->save();
                foreach ($user_list_array as $k => $v) {
                    $messageListUserModel = new MessageUserList();
                    $messageListUserModel->user_id = $v;
                    $messageListUserModel->message_list_id = $messageListModel->id;
                    $messageListUserModel->save();

                    //添加邀请消息到消息列表
                    $inviteUserInfo = User::where(['id' => $v])->first();
                    $messageListUserDetail = new MessageListDetail();
                    $messageListUserDetail->message_from = $userId;
                    $messageListUserDetail->message_to = $v;
                    $messageListUserDetail->message_list_id = $messageListModel->id;
                    $messageListUserDetail->type = static::MESSAGE_LIST_DETAIL_TYPE['invite'];
                    $messageListUserDetail->content = "用户" . $inviteUserInfo->nick_name . "加入群聊";
                    $messageListUserDetail->save();
                }
            } else {
                $messageListModel = MessageList::where(['id' => $chat_id])->first();
                if (empty($messageListModel)) {
                    return false;
                }
                foreach ($user_list_array as $k => $v) {
                    $messageListUserModel = new MessageUserList();
                    $messageListUserModel->user_id = $v;
                    $messageListUserModel->message_list_id = $messageListModel->id;
                    $messageListUserModel->save();

                    //添加邀请消息到消息列表
                    $inviteUserInfo = User::where(['id' => $v])->first();
                    $messageListUserDetail = new MessageListDetail();
                    $messageListUserDetail->message_from = $userId;
                    $messageListUserDetail->message_to = $v;
                    $messageListUserDetail->message_list_id = $messageListModel->id;
                    $messageListUserDetail->type = static::MESSAGE_LIST_DETAIL_TYPE['invite'];
                    $messageListUserDetail->content = "用户" . $inviteUserInfo->nick_name . "加入群聊";
                    $messageListUserDetail->save();
                }
            }

        } else {
            //最多剔除到没人
            if (!empty($chat_id)) {
                $messageListModel = MessageList::where(['id' => $chat_id])->first();
                foreach ($user_list_array as $k => $v) {
                    $messageListUserModel = MessageUserList::where(['user_id' => $v, 'message_list_id' => $chat_id])->first();
                    $messageListUserModel->user_id = $v;
                    $messageListUserModel->status = static::DISABLE;
                    $messageListUserModel->message_list_id = $messageListModel->id;
                    $messageListUserModel->save();

                    //添加邀请消息到消息列表
                    $inviteUserInfo = User::where(['id' => $v])->first();
                    $messageListUserDetail = new MessageListDetail();
                    $messageListUserDetail->message_from = $userId;
                    $messageListUserDetail->message_to = $v;
                    $messageListUserDetail->message_list_id = $messageListModel->id;
                    $messageListUserDetail->type = static::MESSAGE_LIST_DETAIL_TYPE['invite'];
                    $messageListUserDetail->content = "用户" . $inviteUserInfo->nick_name . "退出群聊";
                    $messageListUserDetail->save();
                }
            }


        }
        return $messageListModel->id;

    }

    //获取我的制定消息页面
    function getMesageDetailList($userId,$chatId)
    {
        $inviteData = MessageUserList::where(['user_id'=>$userId,'message_list_id'])->first();
        $query = MessageListDetail::where(['message_list_id'=>$chatId]);
        if(!empty($inviteData) && !empty($inviteData)){
            $query->where(['created_at','>=',$inviteData['created_at']]);
        }
        $result = $query->limit(100)->toArray();
        $userLists = array_



    }

    //获取我的好友列表
    static function getFriendsList($userInfo)
    {
        $friendList = DB::table("user_friend as f")->select(['u.id', 'u.nick_name', 'u.created_at', 'u.email'])
            ->leftJoin("user as u", 'f.f_id', '=', 'u.id')
            ->where(['f.user_id' => $userInfo->id])
            ->orderBy("u.created_at", 'desc')
            ->get()->toArray();
        return $friendList;
    }

    //新增一个好友
    static function addFriend($userId, $friendId = "")
    {
        if (!empty($friendId)) {
            $friendinfo = User::where(['id' => $friendId])->first();
        } else {
            $friendinfo = User::where(['username' => 'test'])->first();
        }

        if (empty($friendinfo)) {
            return false;
        }
        $userFriendModel = new UserFriend();
        $userFriendModel->user_id = $userId;
        $userFriendModel->f_id = $friendinfo->id;
        $userFriendModel->save();
        return true;

    }
}
