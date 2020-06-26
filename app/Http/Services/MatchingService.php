<?php

namespace App\Http\Services;

use App\Libraries\Sms;
use App\Http\Services\EmailLeadsService;
use App\Http\Repositories\AlertRepository;
use App\Http\Repositories\MatchingRepository;
use App\Http\Repositories\MatchingRelationRepository as MRRepository;

class MatchingService
{
	static public function assignUserToLead($name_string, $user)
	{
        $matching_relations = [
            'name_string' => $name_string,
			'user_id' => $user->id,
        ];

        MRRepository::create($matching_relations);

        $matching_leads = MatchingRepository::getLeadsByNameString($name_string);

		foreach($matching_leads as $lead) {
			EmailLeadsService::saveLead($lead, $user);
			$lead->update([
				'matched' => 1,
			]);
		}
	}
}