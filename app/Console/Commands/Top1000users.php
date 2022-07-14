<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \XLSXWriter;
use \App\MyClasses\num;
use \App\MyClasses\GetAge;
use App\Models\Top1000UsersM;
use App\Models\TopUsers;

class Top1000users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Top1000users:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
