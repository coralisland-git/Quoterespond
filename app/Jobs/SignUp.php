<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignUpForAdmin;

class SignUp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $owner;
    protected $user;
    protected $url;
    protected $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($owner, $user)
    {
        $this->owner = $owner;
        $this->user = $user;
        $this->url = config('app.url');
        $this->name = config('app.name');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->owner)->send(new SignUpForAdmin($this->user, $this->url, $this->name));
        /* $default_text = DefaultText::first();
        $text = $default_text->thank_you_signup;

        $global_dialog = new GeneralMessage();
        $global_dialog->type = 'thank_you_signup';
        $global_dialog->phone = $this->user->phone;
        $global_dialog->firstname = $this->user->firstname;
        $global_dialog->lastname = $this->user->lastname;
        $global_dialog->text = $text;
        $global_dialog->my = 1;
        $global_dialog->status = 0;
        $global_dialog->save(); */
    }
}
