<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VKGroups;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;

class GroupSearchController extends Controller
{
  public function search(Request $request) {
    $progress = new GetProgress(session('vkid'), 'open_wall_search', 'Идёт сбор информации по вашему запросу...', 1, 1);
    $query = new VKGroups();

    if (!empty($request->name)) $query = $query->where('name', 'like', '%'.$request->name.'%');
    if (!empty($request->city)) $query = $query->where('city', 'like', $request->city.'%');
    if (is_numeric($request->members_count_from)) $query = $query->where('members_count', '>=', $request->members_count_from);
    if (is_numeric($request->members_count_to))$query =  $query->where('members_count', '<=', $request->members_count_to);

    if (!empty($request->comments) AND !empty($request->wall)) $query = $query->whereBetween('wall', [1, 2]);
    else {
    	if (!empty($request->wall)) $query = $query->where('wall', '=', '1');
    	if (!empty($request->comments)) $query = $query->where('wall', '=', '2');
    }
    if (!empty($request->market)) $query = $query->where('market', '=', '1');
    if (!empty($request->open)) $query = $query->where('is_closed', '=', '0');
    if (!empty($request->verify)) $query = $query->where('verified', '=', '1');

    $info = array();
    $user = new VKUser(session('vkid'));
    if ($user->tarif->demo === FALSE) {
      $limit=10;
      $info['demo']=TRUE;
    } else $limit = 100000;

    $query = $query->take($limit);

    $items = $query->get()->toArray();

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

    if(count($items) > 0) {
      $info['found'] = 'Всего найдено <b> '.num::declension (count($items), (array('группа</b>', 'группы</b>', 'групп</b>')));
      $info['found'] .= '. Полный список найденных групп находится в файле Excel.';
    } else $info['found'] = NULL;
    $progress = new GetProgress(session('vkid'), 'open_wall_search', 'Записывается файл Excel', count($items), 1);

    foreach ($items as &$item) {
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
    }
    $writer->writeToFile('storage/open_wall_search/'.session('vkid').'_open_wall_search.xlsx');

//    return view('groupsearch', ['items' => $items, 'info' => $info]);
$returnHTML = view('layouts.groupsearch-ajax', ['items' => $items, 'info' => $info])->render();
return response()->json( array('success' => true, 'html'=>$returnHTML) );
  }
}
