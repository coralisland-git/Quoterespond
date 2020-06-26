<?php

namespace App\Http\Repositories;

use App\Setting;

class SettingRepository
{
    static public function getSettingsByUserId($user_id)
	{
		return Setting::where('user_id', $user_id)->first();
	}
    
    static public function saveSettings($data, $user_id)
	{
		$settings = Setting::where('user_id', $user_id)->first();
		$settings->update([
			'text' => $data['text'],
		]);
	}

    static public function activateByUserId($user_id)
	{
        $settings = Setting::where('user_id', $user_id)->first();
        $settings->active = 1;
        $settings->update();
	}

    static public function increaseUsage($user_id)
	{
        $settings = Setting::where('user_id', $user_id)->first();
        $settings->usage = $settings->usage + 1;
        $settings->update();
	}

    static public function storeSubscriptionItem($subscription_id, $user_id)
	{
        $settings = Setting::where('user_id', $user_id)->first();
        $settings->subscription_id = $subscription_id;
        $settings->update();
	}
}