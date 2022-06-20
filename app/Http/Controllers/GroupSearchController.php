<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VkGroups;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use \App\MyClasses\SafeMySQL;

class GroupSearchController extends Controller
{
  public function search(Request $request) {
    $rand = $request->rand;
    $progress = new GetProgress(session('vkid'), 'groupsearch'.$rand, 'Идёт сбор информации по вашему запросу...', 1, 1);

    $db = new SafeMysql(array('host' => env('DB_HOST'), 'user' => env('DB_USERNAME'), 'pass' => env('DB_PASSWORD'),'db' => env('DB_DATABASE'), 'charset' => 'utf8mb4'));

    $where_parsed = array();
    if (!empty($request->city)) $where_parsed[] = $db->parse("city LIKE ?s", "$request->city%");
    if (!empty($request->name)) $where_parsed[] = $db->parse("name LIKE ?s", "%".$request->name."%");
    if (!empty($request->members_count_from) AND is_numeric($request->members_count_from)) $where_parsed[] = $db->parse("members_count >= ?i", $request->members_count_from);
    if (!empty($request->members_count_to) AND is_numeric($request->members_count_to)) $where_parsed[] = $db->parse("members_count <= ?i", $request->members_count_to);
    if (!empty($request->comments) AND !empty($request->wall)) $where_parsed[] = $db->parse("wall BETWEEN 1 AND 2");
    else {
    	if (!empty($request->wall)) $where_parsed[] = $db->parse("wall = 1");
    	if (!empty($request->comments)) $where_parsed[] = $db->parse("wall = 2");
    }
    if (!empty($request->market)) $where_parsed[] = $db->parse("market = 1");
    if (!empty($request->open)) $where_parsed[] = $db->parse("is_closed = 0");
    if (!empty($request->verify)) $where_parsed[] = $db->parse("verified = 1");

    if (count($where_parsed)) $where = "WHERE ".implode(' AND ', $where_parsed);
    else $where = '';

    $info = array();
    $user = new VKUser(session('vkid'));
    if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
      $limit=100000;
      $info['demo']=TRUE;
    } else $limit = 100000;

    $result = $db->query("SELECT * FROM vk_groups ?p ORDER BY members_count DESC LIMIT 0, ?i", $where, $limit);

    unset($progress);
    $progress = new GetProgress(session('vkid'), 'groupsearch'.$rand, 'Записывается файл Excel', $result->num_rows, 1);
    $writer = new XLSXWriter();
														$header = array(

														  'id группы'=>'integer',
														  'Ссылка'=>'string',
														  'Название'=>'string',
														  'Город'=>'string',
														  'Подписчики'=>'integer',
														  'тип сообщества'=>'string',
														  'Стена'=>'string',
														  'Сайт'=>'string',
														  'Верифицированно'=>'string',
														  'Магазин'=>'string',
														  'Открытое/закрытое'=>'string',
														  'Контакты'=>'string',
														);
														$writer->writeSheetHeader('Sheet1', $header );
    $info['found'] = '';
    if($result->num_rows > 0) {
      $info['found'] .= 'Всего найдено <b> '.num::declension ($result->num_rows, (array('группа</b>', 'группы</b>', 'групп</b>')));
      if ($result->num_rows >= 100000 AND !isset($info['demo'])) {
        $info['found'] = '<span class="text-danger"><b>Слишком много групп за раз. </b></span>	Найдено более <b>100 000</b> групп. Группы упорядочены по количеству подписчиков. Если вам нужны остальные (которые не вошли в эти 100 000) , то задайте ограничение в графе "Подписчиков...до" ';
      }
      $info['found'] .= '. Полный список найденных групп находится в файле Excel. ';
    } else $info['found'] = NULL;

    $items = array();
    $count = 0;
    while ($item = $db->fetch($result)) {
      $sheet = array();
      $sheet['id'] = $item['group_id'];
      $sheet['link'] = 'https://vk.com/public'.$item['group_id'];
      $sheet['name'] = $item['name'];
      $sheet['city'] = $item['city'];
      $sheet['members_count'] = $item['members_count'];
      $sheet['type'] = $item['type'];
      if ($item['wall']==0) $sheet['wall'] = $item['wall']='выключена';
      if ($item['wall']==1) $sheet['wall'] = $item['wall']='открытая';
      if ($item['wall']==2) $sheet['wall'] = $item['wall']='только комментарии';
      if ($item['wall']==3) $sheet['wall'] = $item['wall']='закрытая';

      $sheet['site'] = $item['site'];

      if ($item['verified']==1) $sheet['verified'] = $item['verified']='да';
      else $sheet['verified'] = $item['verified']='';

      if ($item['market']==1) $sheet['market'] = $item['market']='да';
      else $sheet['market'] = $item['market']='';

      if ($item['is_closed']==1) $sheet['is_closed'] = $item['is_closed']='закрыто';
      else $sheet['is_closed'] = $item['is_closed']='';
      $sheet['contacts'] = $item['contacts'];

      $writer->writeSheetRow('Sheet1', $sheet);
      $count++;
      if ($count<=1000) $items[] = $item;
      if (isset($info['demo']) AND $count > 9) break;
    }
    $writer->writeToFile('storage/open_wall_search/'.session('vkid').'_open_wall_search.xlsx');

//    return view('groupsearch', ['items' => $items, 'info' => $info]);
$returnHTML = view('layouts.groupsearch-ajax', ['items' => $items, 'info' => $info])->render();
return response()->json( array('success' => true, 'html'=>$returnHTML) );
  }
}
