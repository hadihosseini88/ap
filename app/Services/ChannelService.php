<?php

namespace App\Services;

use App\Http\Requests\Channel\UpdateChannelRequest;

class ChannelService extends BaseService
{

    public static function updateChannelInfo(UpdateChannelRequest $request)
    {
        $channelId = $request->route('id');
        //TODO: بررسی اینکه آیا ادمین میخواد اطلاعات کانال های دیگران بروزرسانی کنه
        $channel = auth()->user()->channel;
        $channel->name = $request->name;
        $channel->info = $request->info;
        $channel->save();
        return $channel;
        return $request->validated();
    }
}
