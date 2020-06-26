<?php

namespace App\Http\Repositories;

use App\Subscription;

class SubscriptionRepository
{
    static public function storeSubscriptionInfo($data, $plan, $user_id)
	{
		$info = [
            'user_id' => $user_id,
            'name' => $plan->name,
            'stripe_id' => $data->id,
            'stripe_plan' => $plan->plans_id,
            'quantity' => 0,
        ];

        Subscription::create($info);
	}

    static public function getByName($user_id, $plan_name)
	{
        return Subscription::where('user_id', $user_id)->where('name', $plan_name)->first();
	}

    static public function getByUserId($user_id)
	{
        return Subscription::where('user_id', $user_id)->first();
	}
}