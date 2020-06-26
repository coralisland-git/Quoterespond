<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use Carbon\Carbon;
use App\Jobs\Activate;

use App\Http\Services\UserService;
use App\Http\Services\SettingService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getSettings()
    {
        $user = auth()->user();
        $settings = SettingService::getSettings($user->id);
        $settings->user = $user;

        return $settings;
    }
    
    public function saveSettings(Request $request, $user_id)
    {
        $data = $request->all();
        $settings = $data['settings'];
        
        SettingService::saveSettings($settings, $user_id);

        if ( ! empty($settings['company_name'])) {
            UserService::updateDetials($settings, $user_id);
        }

        return $this->message(__('Settings were saved'), 'success');
    }

    public function activate($user_id)
    {
        SettingService::activate($user_id);
        $owner = UserService::getOwner();
        $user = UserService::getUser($user_id);
        $job = (new Activate($owner, $user))->delay(0)->onQueue('emails');
        $this->dispatch($job);
    }
}
