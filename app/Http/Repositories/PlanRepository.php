<?php

namespace App\Http\Repositories;

use App\Plan;

class PlanRepository
{
    static public function getByPlanId($plan_id)
	{
		return Plan::where('plans_id', $plan_id)->first();
	}
}