<?php

namespace App\Http\Controllers;

use App\User;
use App\GeneralMessage;
use App\Plan;

use App\Libraries\Jwt;
use App\Jobs\SendGeneralText;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UsersPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Twilio\Exceptions\RestException;
use Cartalyst\Stripe\Stripe;
use App\Http\Services\UserService;

class UsersController extends Controller
{
    public function info($id)
    {
    	return User::find($id);
	}

	public function all()
	{
		return User::allUsers()->each(function($item, $key) {
            $ha = $item->homeadvisors()->first();
            $item->rep = $ha['rep'];
            return $item;
		});
	}

    public function getLiveUsers()
	{
		return UserService::getLiveUsers();
	}

	public function getCanceledUsers()
	{
		return User::canceledUsers()->each(function($item, $key) {
			$ha = $item->homeadvisors()->first();
			$item->rep = $ha['rep'];
			return $item;
		});
	}

	public function create(UsersCreateRequest $request)
	{
		$data = $request->only(['plans_id', 'firstname', 'lastname', 'email', 'password', 'view_phone']);
		$data['type'] = 2;
		$data['teams_leader'] = true;
		$data['active'] = true;
		$data['password'] = UserService::password($data['password']);
		$data['phone'] = UserService::phoneToNumber($data);
		$data['teams_id'] = UserService::createTeam($data);
		$data['offset'] = config('app.offset');
		$data = array_filter($data, 'strlen');

		$user = User::create($data);
		LinksService::create($user);

		return $this->message('Teammate was successfully saved', 'success');
	}

	public function update(UsersCreateRequest $request, $id)
	{
		$data = $request->only(['plans_id', 'firstname', 'lastname', 'email', 'password', 'view_phone']);
		$data['password'] = UserService::password($data['password']);
		$data['phone'] = UserService::phoneToNumber($data);
		$data = array_filter($data, 'strlen');

		$user = User::find($id)->update($data);

		return $this->message('Teammate was successfully saved', 'success');
	}

	public function profile(UsersCreateRequest $request)
	{
		$data = $request->only(['firstname', 'lastname', 'email', 'view_phone']);
		$data['phone'] = UserService::phoneToNumber($data);
		$data['offset'] = config('app.offset');
		$data = array_filter($data, 'strlen');

		$user = auth()->user()->update($data);

		return $this->message('Profile was successfully saved', 'success');
	}

	public function remove($id)
	{
		$user = User::find($id);
		$user->links()->delete();

		$user->delete();
		return $this->message('User was successfully removed', 'success');
	}

	public function magic($id)
	{
		$user = User::find($id);
		$user->admins_id = auth()->id();
		$user->save();

		auth()->login($user);
	}

	public function magicInbox(User $user, Client $client, Dialog $dialog, $action)
	{
		auth()->login($user);
		if ($action == 'reply') {
			$dialog->update(['reply_viewed' => 1]);
		}

		$link = config('app.url').'/marketing/inbox/'.$client->id;

		if ($user->plans_id == 'vonage-contractortexter') {
			$link = config('app.url').'/vonage/list/'.$client->id;
		}

		return redirect($link);
	}

	public function password(UsersPasswordRequest $request)
	{
		$user = auth()->user();
		if (Hash::check($request->old_password, $user->password)) {
			$user->password = UserService::password($request->password);
			$user->save();
			return $this->message('Password was successfully changed', 'success');
		}

		return $this->message('Old Password is incorrect');
	}

    public function saveSettings(Request $request)
    {
    	$data = $request->all();
    	auth()->user()->update([
			'company_name' => $data['company_name'],
			'additional_phones' => implode(',', $data['additional_phones']),
		]);

		return $this->message('Settings was successfully saved.', 'success');
	}

	public function allowAccess($request)
	{
		$user = User::find($request);
		$user['allow_access'] = ! $user['allow_access'];
		$user->update();
	}

    public function saveLog($data, $source)
    {
        if ( ! file_exists('logs')) {
            mkdir('logs', 0777);
        }
        file_put_contents('logs/logger.txt', date('[Y-m-d H:i:s] ').$source.': '.print_r($data, true).PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	public function addForwardingEmail(Request $request, $user_id)
    {
		$user = User::find($user_id);
		$user->update([
			'forwarding_email' => $request['forwarding_email']
		]);
		return $this->message('Forwarding Email was successfully saved', 'success');
    }
}
