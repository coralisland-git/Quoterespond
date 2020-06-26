<?php

namespace App\Http\Controllers;

use App\Page;
use App\PagesAccess;
use App\PagesMenu;
use App\Plan;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagesController extends Controller
{
    public function defaultPage($post = [])
    {
		$users_plan =  auth()->user()->plans_id;
    	if (auth()->user()->type == 1) {
    		return 'users.live';
		} else {
			return 'dashboard.user';
    	}
    }

    public function menu($post = [])
    {
		$noAccess = PagesAccess::where('users_type', auth()->user()->type)->get()->pluck('code')->toArray();
		$user = auth()->user();
		$users_plans_id = $user->plans_id;
		$users_paused_plans_id = $user->paused_plans_id;
		$users_plan =  $users_plans_id == 'canceled-quoterespond' ? $users_paused_plans_id : $users_plans_id;
		$plan = empty(auth()->user()->plans_id) ? 'none' : $users_plan;

		if (empty($user->stripe_id) && empty($user->allow_access)) {
			array_push($noAccess, 'yelp-dashboard', 'plans-info');
		}

		//$menu = PagesMenu::whereNotIn('pages_code', $noAccess)->where('plans', $plan)->orderBy('pos')->get();
		$menu = PagesMenu::whereNotIn('pages_code', $noAccess)->orderBy('pos')->get();
		$codes = $menu->pluck('pages_code')->toArray();
		$pages = Page::whereIn('code', $codes)->get();

		$temp = [];
		foreach ($pages as $page) {
			$temp[$page['code']] = $page;
		}

		$items = [];
		foreach ($menu as $parent) {
			if (empty($parent['parents_code']))
			{
				$row = $temp[$parent['pages_code']]->toArray();
				$row['main'] = $parent['main'];
				$row['pages'] = [];

				foreach ($menu as $child) {
					if ($parent['pages_code'] == $child['parents_code']) {
						$val = $temp[$child['pages_code']]->toArray();
						$val['parents_code'] = $child['parents_code'];
						$val['main'] = $child['main'];
						$row['pages'][] = $val;
					}
				}
				$items[] = $row;
			}
		}

		return $items;
    }
}
