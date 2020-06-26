<?php

namespace App\Http\Controllers;

use App\Http\Services\MailboxService;
use App\Http\Services\GmailService;
use Illuminate\Http\Request;

class MailboxController extends Controller
{
    public function all()
    {
        $mailboxes = MailboxService::all();

        return $mailboxes;
    }
    
    static public function getByEmail($email)
    {
        $mailbox = MailboxService::getByEmail($email);

        return $mailbox;
    }
    
    public function add(Request $request)
    {
        $data = $request->all();
        $mailbox = $data['mailbox'];
        
        MailboxService::add($mailbox);

        return $this->message(__('Mailbox were saved'), 'success');
    }
    
    public function delete($id, $email)
    {
        GmailService::delete($email);
        MailboxService::delete($id);

        return $this->message(__('Mailbox were removed'), 'success');
    }
}
