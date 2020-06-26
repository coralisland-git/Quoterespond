<?php

namespace App\Http\Repositories;

use App\MatchingRelation;
use Carbon\Carbon;

class MatchingRelationRepository
{
	static public function create($data)
	{
		/* $relation = MatchingRelation::where('user_id', $data['user_id'])->first();

		if (empty($relation)) {
			$relation = MatchingRelation::create($data);
		} */

		MatchingRelation::create($data);
	}

	static public function getRelationByString($name_string)
	{
		return MatchingRelation::where('name_string', $name_string)->first();
	}
}