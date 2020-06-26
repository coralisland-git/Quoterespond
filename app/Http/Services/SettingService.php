<?php

namespace App\Http\Services;

use App\Team;
use App\Plan;
use App\User;
use Carbon\Carbon;

use App\Http\Repositories\SettingRepository;

class SettingService
{
    static public function getSettings($user_id)
    {
        return SettingRepository::getSettingsByUserId($user_id);
    }
    
    static public function saveSettings($data, $user_id)
    {
        SettingRepository::saveSettings($data, $user_id);
    }

    static public function activate($user_id)
    {
        SettingRepository::activateByUserId($user_id);
    }

    static public function increaseUsage($user_id)
    {
        SettingRepository::increaseUsage($user_id);
    }

    static public function storeSubscriptionItem($subscription_id, $user_id)
    {
        SettingRepository::storeSubscriptionItem($subscription_id, $user_id);
    }
}