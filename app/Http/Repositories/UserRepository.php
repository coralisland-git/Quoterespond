<?php

namespace App\Http\Repositories;

use App\User;

class UserRepository
{
	static public function getById($user_id)
	{
		return User::find($user_id);
	}
	
	static public function getByCompanyName($companyName)
	{
		return User::where('company_name', $companyName)->first();
	}

	static public function getUserByFirstAndLastName($firstname, $lastname)
	{
		return User::where('firstname', $firstname)->where('lastname', $lastname)->first();
	}

	static public function getOwner()
	{
		return User::where('owner', 1)->first();
	}

    static public function getUserByForwardingEmail($forwarding_email)
    {
        return User::where('forwarding_email', $forwarding_email)->first();
    }

    static public function getLiveUsers()
    {
        return User::where('type', '2')->where('plans_id', '!=', 'canceled-quoterespond')->get();
    }

    static public function updateDetials($data, $user_id)
    {
		$user = User::find($user_id);
		$user->update([
			'company_name' => $data['company_name'],
		]);
    }

    static public function storeCustomerInfo($customer_id, $card, $user_id)
    {
		$user = User::find($user_id);
		$user->update([
			'stripe_id' => $customer_id,
			'card_brand' => $card->brand,
			'card_last_four' => $card->last4,
		]);
    }
}