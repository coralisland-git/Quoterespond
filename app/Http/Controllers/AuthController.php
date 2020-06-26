<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use App\Constant;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Jobs\SignUp;
use App\Jobs\Support;
use App\Jobs\Recovery;
use App\Http\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public $salt = 'eEZue4JfUvJJKn9N';

    public function info()
    {
        return auth()->user();
    }

    public function signin(SignInRequest $request)
    {
        $old_pass = hash_hmac('sha1', $request->password, $this->salt);
        $users = User::where('password', $old_pass)->get();
        foreach ($users as $user) {
            $user->update([
                'password' => UserService::password($request->password)
            ]);
        }
        $user = User::where('email', $request->email)->first();

        if (auth()->validate($request->all()) && $user->plans_id != 'canceled-contractortexter') {
            auth()->attempt($request->all());
            return $this->message('You are in', 'success');
        }

        return $this->message('Invalid Email or Password');
    }

    public function signup(SignUpRequest $request)
    {
        $data = $request->only(['plans_id', 'email', 'password', 'firstname', 'lastname']);
        $data['plans_id'] = $data['plans_id'].'-'.strtolower(config('app.name'));
        $data['type'] = 2;
		$data['password'] = UserService::password($data['password']);
        $data['trial_ends_at'] = UserService::trialEndsAt($data['plans_id']);

        $data = array_filter($data, 'strlen');
        $user = User::create($data);

        $settings = [
            'user_id' => $user->id,
            'text' => Constant::DEFAULT_RESPOND_TEXT,
        ];

        Setting::create($settings);

        auth()->login($user);
        $owner = User::where('owner', 1)->first();
        $job = (new SignUp($owner, $user))->delay(0)->onQueue('emails');
        $this->dispatch($job);

        return $this->message('You were successfully registered', 'success');
    }

    public function signout()
    {
        $user = auth()->user();
        if ( ! empty($user->admins_id)) {
            $admin = User::find($user->admins_id);
            auth()->login($admin);

            $user->admins_id = 0;
            $user->save();
        } else {
            auth()->logout();
        }

        return $this->message('You are out', 'success');
    }

    public function support(Request $request)
    {
        $data = $request->only(['name', 'email', 'message', 'subject']);
        $owner = User::where('owner', 1)->first();

        $job = (new Support($owner, $data))->onQueue('emails');
        $this->dispatch($job);

        return $this->message('Your email successfully sent', 'success');
    }

    public function recovery(Request $request)
    {
        $user = User::where('email', strtolower($request->email))->first();
        if ( ! empty($user)) {
            $password = crypt($user->password, time());
            $user->password = UserService::password($password);
            $user->save();

            $job = (new Recovery($request->email, $password))->onQueue('emails');
            $this->dispatch($job);

            return $this->message('New password was sent to your email address', 'success');
        }
        return $this->message('Invalid email');
    }
}
