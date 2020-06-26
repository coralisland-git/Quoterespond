<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GmailController;

use QLogger;

class CheckYelpMailboxes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'go:yelp';

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
        $gmailCtrl = new GmailController();
        $gmailCtrl->checkMailboxesByTokenFiles();
    }
}
