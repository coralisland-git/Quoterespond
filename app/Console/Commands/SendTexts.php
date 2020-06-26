<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Repositories\SendTextRepository;
use App\Http\Services\SendTextService;

use QLogger;

class SendTexts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:texts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command cubrid_is_instance(conn_identifier, oid)nce.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $texts = SendTextRepository::textsToSend();
        if (! empty($texts)) {
            foreach ($texts as $text) {
                SendTextService::send($text);
            }
        }
    }
}
