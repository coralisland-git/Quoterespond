<?php

namespace App\Http\Repositories;

use App\Client;
use Carbon\Carbon;

class ClientRepository
{
	static public function create($data)
	{
		return Client::create($data);
	}

	static public function update($client_id, $data)
	{
		$client = Client::find($client_id);
		$client->update($data);
		return $client;
	}

	static public function getById($client_id)
	{
		return Client::find($client_id);
	}

	static public function getByPhone($phone)
	{
		return Client::where('phone', $phone)->where('duplicate', '0')->first();
	}

	static public function getVonageClients()
	{
		return Client::whereIn('source', ['Vonage', 'NewVonage'])
                    ->where('ready', '0')
                    ->where('created_at', '>', Carbon::today())
                    ->limit(100)
                    ->get();
	}

    static public function updateReadyStatus($client_id, $data)
	{
		Client::find($client_id)->update($data);
	}

	static public function getClientsByUserId($user_id, $offset)
	{
		return Client::where('user_id', $user_id)->with('company')->with('dialogs')->orderBy('created_at', 'desc')->offset($offset)->limit(50)->get()->each(function($item, $key) {
			$offset = auth()->user()->offset;
			Carbon::setToStringFormat('F dS g:i A');
			$item->created_at_string = $item->created_at->subHour($offset)->__toString();
			Carbon::resetToStringFormat();
			return $item;
		});
	}

	static public function getLeadFullName($lead_id)
	{
		return Client::select('firstname', 'lastname')->where('id', $lead_id)->first();
	}

    static public function removeTestCraftJackLead($phone)
	{
        Client::where('phone', $phone)->delete();
    }
}