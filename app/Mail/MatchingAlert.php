<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MatchingAlert extends Mailable
{
    use Queueable, SerializesModels;

    protected $matching;
    protected $name_string;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($matching, $name_string)
    {
        $this->matching = $matching;
        $this->name_string = $name_string;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.matching_alert')
        ->subject('New matching alert from '. $this->matching->source)
        ->with([
                'matching' => $this->matching,
                'name_string' => $this->name_string,
                'url' => config('app.url'),
            ]);
    }
}
