<?php

namespace App\Http\Services;

//use Cartalyst\Stripe\Stripe;
use App\Http\Services\SettingService;
use App\Http\Services\PlanService;
use App\Http\Services\UserService;
use App\Http\Repositories\SubscriptionRepository;

use Stripe\Stripe;
use Carbon\Carbon;

class SubscriptionService
{
	static public function storeSubscriptionInfo($data, $plan, $user_id)
	{
        SubscriptionRepository::storeSubscriptionInfo($data, $plan, $user_id);
        SettingService::storeSubscriptionItem($data->items->data[0]->id, $user_id);
    }
    
    static public function createCustomerReport($user)
	{
        $settings = SettingService::getSettings($user->id);
        $stripe = \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $customer = \Stripe\Customer::retrieve($user->stripe_id);
        $record = \Stripe\SubscriptionItem::createUsageRecord($settings->subscription_id,
            [
                'quantity' => $settings->usage,
                'action' => 'set',
                'timestamp' => Carbon::now()->timestamp,
            ]);
    }
    
    static public function subscribe($token, $user)
	{
		$plan = PlanService::getByPlanId($user->plans_id);

		if ( ! $user->subscribed($plan->name)) {
            if ( ! $user->customer_id) {
                $customer = self::createCustomer($token, $user);
            }
            $subscription = self::createSubscribtion($customer, $plan, $user->id);
		}
    }

    static public function createCustomer($token, $user)
	{
        $stripe = \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $customer = \Stripe\Customer::create([
            'email' => $user->email,
        ]);
        $card = \Stripe\Customer::createSource($customer->id,
            ['source' => $token]
        );

        UserService::storeCustomerInfo($customer->id, $card, $user->id);
		return $customer;
    }
    
    static public function createSubscribtion($customer, $plan, $user_id)
	{
        $subscription = $customer->subscriptions->create([
            'plan' => $plan->plans_id,
        ]);
        self::storeSubscriptionInfo($subscription, $plan, $user_id);

		return $subscription;
    }
    
    static public function subscribed($user_id, $plan_name)
	{
        $isSubscribed = false;
        $subscription = SubscriptionRepository::getByName($user_id, $plan_name);

        if ($subscription) {
            $isSubscribed = true;
        }

        return $isSubscribed;
    }
    
    static public function getSubscriptionInfo($user_id)
	{
        return SubscriptionRepository::getByUserId($user_id);
    }
}
