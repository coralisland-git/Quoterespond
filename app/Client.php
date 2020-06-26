<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
