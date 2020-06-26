<?php

namespace App\Http\Repositories;

use App\Mailbox;

class MailboxRepository
{
    static public function all()
    {
        return Mailbox::all();
    }
    
    static public function getByEmail($email)
    {
        return Mailbox::where('email', $email)->first();
    }
    
    static public function add($data)
    {
        Mailbox::create($data);
    }
    
    static public function delete($id)
    {
        Mailbox::find($id)->delete();
    }
}