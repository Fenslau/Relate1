<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use App\Models\Top;
use \SimpleXLSX;


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
      $group_ids_all = explode(',', $top1000->top1000);
      $token = $top1000->token;

      if ( $xlsx = SimpleXLSX::parse('temp/top1000date.xlsx') ) {
      	foreach( $xlsx->rows() as $r ) $date[]=$r;
      } else echo SimpleXLSX::parseError();


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

      $group_ids = '';
      for ($i=0; $i<500; $i++) {

      	if (isset($group_ids_all[$i])) $group_ids=$group_ids.','.$group_ids_all[$i];
      }
      $vk = new VKApiClient();
      $group_get = $vk->groups()->getById($token, array(
        'group_ids'		 => $group_ids,
				'fields'    	 => 'members_count,can_post,links,wall',
				'access_token' => $token,
				'v' 			     => '5.95'
      ));
      		if (isset($group_get['error'])) goto ex;
      		$group=$group_get;

      $group_ids = '';
      for ($i=500; $i<1000; $i++) {

      	if (isset($group_ids_all[$i])) $group_ids=$group_ids.','.$group_ids_all[$i];
      }

      $group_get = $vk->groups()->getById($token, array(
        'group_ids'		   => $group_ids,
        'fields'    	   => 'members_count,can_post,links,wall',
        'access_token' 	 => $token,
        'v' 			       => '5.95'
      ));

      if (isset ($group_get)) for ($k=0; $k<500; $k++) ($group[$k+500] = $group_get[$k]);

      for ($j=1; $j<=40; $j++) {

      		$code_1 = 'return[';

      		for ($i=1; $i<=25; $i++) {												//цикл наполнения статистики
      			$k=($j-1)*25+$i;
      			if (isset($group_ids_all[$k])) $code_1 = $code_1.'API.stats.get({"group_id":'.$group_ids_all[$k].',"v":5.95,"intervals_count":1}),';
      		}
      		$code_1 = $code_1.'];';

      retrys:
      $stat1 = $vk->getRequest()->post('execute', $token, array(
        'code' 			    => $code_1,
        'access_token'  => $token,
        'v' 			      => '5.95'
      ));

      		for ($i=0; $i<25; $i++) {												//цикл записи в файл
      			$k=($j-1)*25+$i;

      		if (!isset($group[$k]['wall'])) $group[$k]['wall'] = ' ';
      		if ($group[$k]['wall'] == '0') $group[$k]['wall'] = ' ';					//стена
      		if ($group[$k]['wall'] == '1') $group[$k]['wall'] = ' ';
      		if ($group[$k]['wall'] == '2') $group[$k]['wall'] = 'только комментарии';
      		if ($group[$k]['wall'] == '3') $group[$k]['wall'] = ' ';

      		if (!isset($group[$k]['can_post'])) $group[$k]['can_post'] = ' ';
      		if ($group[$k]['can_post'] == '1') $group[$k]['can_post'] = 'посты и комментарии';	//можно писать на стене
      		if ($group[$k]['can_post'] == '0') $group[$k]['can_post'] = ' ';


      		if (!isset($group[$k]['type'])) $group[$k]['type'] = ' ';
      		if ($group[$k]['type'] == 'group') $group[$k]['type'] = 'группа';			//группа/страница/событие
      		if ($group[$k]['type'] == 'page') $group[$k]['type'] = 'страница';
      		if ($group[$k]['type'] == 'event') $group[$k]['type'] = 'мероприятие';

      		if (!isset($group[$k]['is_closed'])) $group[$k]['is_closed'] = ' ';
      		if ($group[$k]['is_closed'] == '0') $group[$k]['is_closed'] = 'открытое';	//закрытая/открытая/частная
      		if ($group[$k]['is_closed'] == '1') $group[$k]['is_closed'] = 'закрытое';
      		if ($group[$k]['is_closed'] == '2') $group[$k]['is_closed'] = 'частное';

      $item=array();
      			$item['num'] = $k+1;
      			if (!empty($group[$k]['id'])) $item['id'] = $group[$k]['id'];
      			if (!empty($group[$k]['name'])) $item['name'] = $group[$k]['name'];
      			if (!empty($group[$k]['members_count']))
      				$item['members_count'] = $group[$k]['members_count'];
      				else $item['members_count'] = '';
      		if (!empty($stat1[$i])) {
      			@$item['grouth'] = $stat1[$i][0]['activity']['subscribed']-$stat1[$i][0]['activity']['unsubscribed'];
      			if ($item['grouth']==0) $item['grouth'] = ' ';
      			$item['reach'] = $stat1[$i][0]['reach']['reach'];
      			if ($stat1[$i][0]['reach']['reach_subscribers'] <> 0)
      				$item['reach_to_sub'] = round(($stat1[$i][0]['reach']['reach']/$stat1[$i][0]['reach']['reach_subscribers']), 2);
      				else $item['reach_to_sub']  = ' ';
      			$item['reach_subscribers'] = $stat1[$i][0]['reach']['reach_subscribers'];
      			if ($item['reach_to_sub'] <> 0) {
      				$item['sub_to_reach'] = round((1/$item['reach_to_sub']*100), 2);
      				$item['sub_to_reach'] = $item['sub_to_reach'].'%';
      			} else $item['sub_to_reach'] = ' ';
      			if (!empty($stat1[$i][0]['visitors']['sex'][0]['count']))
      				$item['female'] = $stat1[$i][0]['visitors']['sex'][0]['count']; else $item['female'] = '';
      			if (!empty($stat1[$i][0]['visitors']['sex'][0]['count']) AND $stat1[$i][0]['visitors']['visitors'] <> 0) {
      				$item['female_proc'] = round(($stat1[$i][0]['visitors']['sex'][0]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
      				$item['female_proc'] = $item['female_proc'].'%';
      			} else $item['female_proc'] = ' ';
      			if (!empty($stat1[$i][0]['visitors']['sex'][1]['count']))
      				$item['male'] = $stat1[$i][0]['visitors']['sex'][1]['count']; else $item['male'] = '';
      			if (!empty($stat1[$i][0]['visitors']['sex'][1]['count']) AND $stat1[$i][0]['visitors']['visitors'] <> 0) {
      				$item['male_proc'] = round(($stat1[$i][0]['visitors']['sex'][1]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
      				$item['male_proc'] = $item['male_proc'].'%';
      			} else $item['male_proc'] = ' ';
      			$item['visitors'] = $stat1[$i][0]['visitors']['visitors'];
      			if ($stat1[$i][0]['visitors']['visitors'])
      				$item['views_to_visit'] = round(($stat1[$i][0]['visitors']['views']/$stat1[$i][0]['visitors']['visitors']), 2);
      			else $item['views_to_visit'] = ' ';
      			$item['over_18'] = $stat1[$i][0]['visitors']['visitors'] - @$stat1[$i][0]['visitors']['age'][0]['count'];

      			if ($stat1[$i][0]['visitors']['visitors'] <> 0) {
      				$item['over_18_procent'] = round(($item['over_18']/$stat1[$i][0]['visitors']['visitors']*100), 2);
      				$item['over_18_procent'] = $item['over_18_procent'].'%';
      			} else $item['over_18_procent'] = ' ';
      			if (!empty($stat1[$i][0]['visitors']['cities'][0]['name']))
      				$item['cities'] = $stat1[$i][0]['visitors']['cities'][0]['name'];
      				else $item['cities'] = '';
      			if (!empty($stat1[$i][0]['visitors']['cities'][0]['count']))
      				$item['cities_count'] = $stat1[$i][0]['visitors']['cities'][0]['count'];
      				else $item['cities_count'] = '';
      			if ($stat1[$i][0]['visitors']['visitors'] <> 0 AND isset($stat1[$i][0]['visitors']['cities'][0]['count'])) {
      				$item['max_visitors'] = round(($stat1[$i][0]['visitors']['cities'][0]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
      				$item['max_visitors'] = ' ('.$item['max_visitors'].'%)';
      			} else $item['max_visitors'] = ' ';
      		} else { $item['views_to_visit'] = ' '; $item['visitors'] = ' '; $item['male_proc'] = ' '; $item['male'] = ' '; $item['female_proc'] = ' ';
      			$item['female'] = ' '; $item['sub_to_reach'] = ' '; $item['reach_to_sub']  = ' '; $item['grouth'] = ' '; $item['reach'] = ' ';
      			$item['over_18'] = ' '; $item['over_18_procent'] = ' '; $item['cities'] = ' '; $item['cities_count'] = ' '; $item['max_visitors'] = ' ';
      			$item['visitors'] = ' '; $item['reach_subscribers'] = ' ';
      		}
      			$item['can_post'] = $group[$k]['can_post'];
      			$item['wall'] = $group[$k]['wall'];
      			$item['is_closed'] = $group[$k]['is_closed'];
      			$item['type'] = $group[$k]['type'];
      			if (!empty($date[$k][0]) AND is_numeric($date[$k][0])) $item['date'] = date('d.m.Y', $date[$k][0]); else $item['date'] = ' ';

      			if (!empty($group[$k]['photo_100'])) $item['photo'] = $group[$k]['photo_100'];

      		$writer->writeSheetRow('Sheet1', $item);
      		}

      		}


      $writer->writeToFile("public/temp/top1000.xlsx");
      ex:
    }
}
