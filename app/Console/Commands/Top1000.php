<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use App\Models\Top;
use \SimpleXLSX;
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
      $top1000 = $top->findOrFail(1);
      if (!empty($top1000->top1000)) {
        $group_ids_all = explode(',', $top1000->top1000);
        $token = $top1000->token;

        if ( $xlsx = SimpleXLSX::parse('public/temp/top1000date.xlsx') AND count( $xlsx->rows()) == count($group_ids_all) ) {
        	foreach( $xlsx->rows() as $r ) $date[]=$r;
        } else echo SimpleXLSX::parseError()."\n";


        $writer = new XLSXWriter();
        														$header = array(
        														  '№'=>'integer',
        														  'id группы'=>'integer',
        														  'Название'=>'string',
        														  'Подписчики'=>'integer',
        														  'Прирост'=>'integer',
        														  'Охват'=>'integer',
        														  '(отношение полного охвата к охвату подписчиков)'=>'string',
        														  'Охват подписч.'=>'integer',
        														  '(% от полного охвата)'=>'string',
        														  'Жен'=>'integer',
        														  '(% от посетителей)'=>'string',
        														  'Муж'=>'integer',
        														  '(% от посетителей) '=>'string',
        														  'Посетители'=>'integer',
        														  '(кол-во просмотров на посетителя)'=>'string',
        														  'Старше 18 лет'=>'integer',
        														  '(% от посетителей )'=>'string',
        														  'Из города'=>'string',
        														  'количество'=>'integer',
        														  '( % от посетителей)'=>'string',
        														  'Стена'=>'string',
        														  'стена'=>'string',
        														  'Тип'=>'string',
        														  'сообщества'=>'string',
        														  'Дата'=>'string',
        														  'Аватарка'=>'string',
        														);
        														$writer->writeSheetHeader('Sheet1', $header );
        $vk = new VKApiClient();
        $group = Groups::get1000Groups($group_ids_all, $token);

        $items = Groups::getStats($group, $token);

        for ($i=0; $i < count($items); $i++) {

            if (!empty($date[$i][0]) AND is_numeric($date[$i][0])) $items[$i]['date'] = date('d.m.Y', $date[$i][0]); else $items[$i]['date'] = '';
        		$writer->writeSheetRow('Sheet1', $items[$i]);
        }

        $writer->writeToFile("public/temp/top1000.xlsx");
      ex:
    }
  }
}
