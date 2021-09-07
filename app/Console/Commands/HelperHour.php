<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Top;
use App\Models\VkGroups;
use \VK\Client\VKApiClient;

class HelperHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Help:hour';

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
      $vk = new VkGroups();
      $top = new Top();
        $group_ids_all = $vk->orderBy('members_count', 'desc')->take(1000)->pluck('group_id')->toArray();

        $group_ids_all = implode(',', $group_ids_all);
        $top1000 = $top->find(1);
        $top1000->top1000 = $group_ids_all;
        $top1000->save();
    }
}
