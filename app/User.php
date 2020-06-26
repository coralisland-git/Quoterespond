<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Http\Services\SubscriptionService;

class User extends Authenticatable
{
	use Notifiable;

    protected $hidden = ['password'];
    protected $guarded = [];
    protected $dates = ['trial_ends_at'];

    public function username()
    {
        return 'email';
    }
    
    public function clients()
    {
        return $this->hasMany('App\Client', 'user_id');
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    static public function canceledUsers()
    {
        return self::where('type', '2')->where('plans_id', 'canceled-quoterespond')->get();
    }

    public function subscribed($plan_name)
    {
        return SubscriptionService::subscribed($this->id, $plan_name);
    }
}
