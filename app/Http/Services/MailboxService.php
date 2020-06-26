<?php

namespace App\Http\Services;

use App\Team;
use App\Plan;
use App\User;
use Carbon\Carbon;

use App\Http\Repositories\MailboxRepository;

class MailboxService
{
    static public function all()
    {
        return MailboxRepository::all();
    }
    
    static public function getByEmail($email)
    {
        return MailboxRepository::getByEmail($email);
    }
    
    static public function add($data)
    {
        MailboxRepository::add($data);
    }
    
    static public function delete($id)
    {
        MailboxRepository::delete($id);
    }
}