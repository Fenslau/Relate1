<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \XLSXWriter;
use \SimpleXLSX;
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
      $top1000 = $top->findOrFail(1);
      if (!empty($top1000->top1000)) {
        $group_ids_all = explode(',', $top1000->top1000);
        $token = $top1000->token;

        $writer = new XLSXWriter();
        if ( $xlsx = SimpleXLSX::parse('public/temp/top1000.xlsx') AND count( $xlsx->rows()) == count($group_ids_all)+1 ) {
          $items = array();
          $iter = TRUE;
          foreach( $xlsx->rows() as $row )
              if ($iter) $iter = false;
              else $items[] = $row;

          $items = Groups::getLastPostDate($items, $token);

          foreach ($items as $item) {
            $writer->writeSheetRow('Sheet1', [strtotime($item[24])]);
          }

        } else echo SimpleXLSX::parseError()."\n";

        ex: $writer->writeToFile("public/temp/top1000date.xlsx");
    }
  }


}
