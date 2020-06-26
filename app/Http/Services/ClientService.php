<?php

namespace App\Http\Services;

use App\Http\Services\HomeAdvisorService;

use App\Http\Repositories\LeadRepository;
use App\Http\Repositories\ClientRepository;
use App\Http\Repositories\CompanyRepository;

use Carbon\Carbon;

class ClientService
{
	public function clientHandler($name, $user_id, $email)
	{
        $client_data = [
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
        ];

        $client = ClientRepository::create($client_data);

        return $client;
    }
    
    public function all()
	{
        return auth()->user()->clients->each(function($item, $key) {
                $user_offset = auth()->user()->offset;
                $item->created_at_string = Carbon::parse($item->created_at)->subHours($user_offset)->format('F dS g:i A');
                return $item;
            })->toArray();
    }
}
