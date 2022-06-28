<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \XLSXWriter;
use App\Models\Top;
use \App\MyClasses\Groups;

class Top1000date extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Top1000date:get';

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

        $group = new Groups();

        $group->read('public/temp/top1000.xlsx');

      //  $group->getLastPostDate($token, NULL, 'toppost');

      //  $group->getReactions();

        $group->write("public/temp/top1000.xlsx");
        $top1000->update(['time' => date('U')]);
    }
  }


}
