<?php

namespace App\Http\Services;

use App\Team;
use App\Plan;
use App\User;
use Carbon\Carbon;
use App\Http\Services\PlanService;

use App\Http\Repositories\UserRepository;

class UserService
{
    const SEND_FROM = 9;
    const SEND_TO = 21;

    static public function password($password)
    {
        return ! empty($password) ? bcrypt($password) : null;
    }

	static public function phoneToNumber($data)
	{
		return ! empty($data['view_phone']) ? str_replace(['-', '.', ' ', '(', ')'], '', $data['view_phone']) : '';
    }

    static public function trialEndsAt($plans_id)
    {
        $plan = Plan::findById($plans_id);
        return Carbon::now()->addDays($plan ? $plan->trial : 0);
    }

    static public function getLiveUsers()
    {
        $live_users = UserRepository::getLiveUsers();
        foreach($live_users as $user) {
            $user->has_subscription = PlanService::isSubscribed($user);
            $user->current_plan = PlanService::getPlanName($user->plans_id);
        }
        return $live_users;
    }

    static public function getOwner()
    {
        return UserRepository::getOwner();
    }

    static public function getUser($user_id)
    {
        return UserRepository::getById($user_id);
    }

    static public function getUserByCompanyName($companyName)
    {
        return UserRepository::getByCompanyName($companyName);
    }

    static public function updateDetials($data, $user_id)
    {
        UserRepository::updateDetials($data, $user_id);
    }

    static public function storeCustomerInfo($data, $card, $user_id)
    {
        UserRepository::storeCustomerInfo($data, $card, $user_id);
    }
}