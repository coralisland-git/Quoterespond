<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientsController extends Controller
{
	public function all()
	{
		return service('client')->all();
	}
}
