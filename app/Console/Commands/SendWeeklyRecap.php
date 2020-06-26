<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Services\WeeklyRecapService;

class SendWeeklyRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:weeklyRecaps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends weekly recap each friday at 15:00 NY time for users who had any leads engagment by last week';

    /**
     * Create a new command instance.
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
        WeeklyRecapService::sendWeeklyRecaps();
    }
}
