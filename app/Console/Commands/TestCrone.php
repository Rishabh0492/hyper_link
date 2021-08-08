<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;

class TestCrone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:test:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Crone';

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
        $record = 'Test Crone is working';
        
        $this->info('Test Crone. Date: '. date("Y-m-d H:i:s"));
    }
}