<?php

namespace App\Http\Services;

use Cartalyst\Stripe\Stripe;
use App\Http\Services\SubscriptionService;
use App\Http\Repositories\PlanRepository;

class PlanService
{
	static public function isCanceledPlan($user)
	{
        if ($user->subscribed('Canceled') || $user->plans_id == 'canceled-contractortexter') {
			return true;
        }

        return false;
    }

	static public function isSubscribed($user)
	{
        $has_subscription = false;
        $plan = PlanRepository::getByPlanId($user->plans_id);
        if ($user->subscribed($plan->name)) {
            $has_subscription = true;
        }
        return $has_subscription;
    }

	static public function getPlanName($plan_id)
	{
        $plan = PlanRepository::getByPlanId($plan_id);
        return $plan->name;
    }

	static public function getByPlanId($plan_id)
	{
        return PlanRepository::getByPlanId($plan_id);
    }
}
