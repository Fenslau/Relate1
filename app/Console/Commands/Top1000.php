<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use App\Models\Top;
use \App\MyClasses\Groups;


class Top1000 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Top1000:get';

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
      $top = New Top();
      $top1000 = $top->find(1);
      if (!empty($top1000->top1000)) {
        $group_ids_all = explode(',', $top1000->top1000);
        $token = $top1000->token;
    		$group_date = new Groups();
    		$group_date->read('public/temp/top1000.xlsx');
        $group = new Groups();
        $group->get1000Groups($group_ids_all, $token);

        $group->getStats($token);

		foreach ($group->groups as &$row) {
			$id = array_search($row['id'], array_column($group_date->groups, 'id'));
			if (!empty($group_date->groups[$id]['date'])) $row['date'] = $group_date->groups[$id]['date'];
		}

        $group->write('public/temp/top1000.xlsx');
      ex:
    }
  }
}
