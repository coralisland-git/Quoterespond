<?php

namespace App\Http\Repositories;

use App\Matching;
use Carbon\Carbon;

class MatchingRepository
{
	static public function create($data)
	{
		return Matching::create($data);
	}

	static public function getLeadsByNameString($name_string)
	{
		return Matching::where('name_string', $name_string)->where('matched', '0')->get();
	}

	static public function getLeadByPhone($phone)
	{
		return Matching::where('phone', $phone)->first();
	}
}