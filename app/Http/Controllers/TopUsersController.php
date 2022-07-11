<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use \App\MyClasses\GetAge;
use App\Models\TopUsers;

class TopUsersController extends Controller
{
  public function search(Request $request) {
    $items = array();
    $rand = $request->rand;
    $progress = new GetProgress(session('vkid'), 'topusers'.$rand, 'Идёт сбор информации по вашему запросу...', 1, 1);

    $info = array();
    $user = new VKUser(session('vkid'));
    if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
      $limit=100000;
      $info['demo']=TRUE;
    } else $limit = 100000;

    $result = new TopUsers();
    if(!empty($request->name)) $result = $result->where('first_name', 'like', '%'.$request->name.'%')->orWhere('last_name', 'like', '%'.$request->name.'%')->orWhere('screen_name', 'like', '%'.$request->name.'%');
    if(!empty($request->keyword)) $result = $result->where('about', 'like', '%'.$request->keyword.'%')->orWhere('activities', 'like', '%'.$request->keyword.'%')->orWhere('occupation', 'like', '%'.$request->keyword.'%');
    if(!empty($request->city)) $result = $result->where('city', 'like', $request->city.'%');
    if(!empty($request->members_count_from)) $result = $result->where('followers_count', '>=', $request->members_count_from);
    if(!empty($request->members_count_to)) $result = $result->where('followers_count', '<=', $request->members_count_to);
    if(!empty($request->sex)) $result = $result->whereIn('sex', $request->sex);
    if(!empty($request->can_send_friend_request)) $result = $result->where('can_send_friend_request', 1);
    if(!empty($request->can_post)) $result = $result->where('can_post', 1);
    if(!empty($request->can_write_private_message)) $result = $result->where('can_write_private_message', 1);
    if(!empty($request->verify)) $result = $result->where('verified', 1);
    if(!empty($request->status)) $result = $result->whereIn('status', $request->status);
    $result = $result->orderBy('followers_count', 'desc')->take(50000)->get()->toArray();
    unset($progress);
    $progress = new GetProgress(session('vkid'), 'topusers'.$rand, 'Записывается файл Excel', 1, 1);
    $writer = new XLSXWriter();
														$header = array(

														  'Имя Фамилия'=>'integer',
														  'Ссылка'=>'string',
														  'Страна'=>'string',
														  'Город'=>'string',
                              'Возраст'=>'integer',
														  'Подписчики'=>'integer',
														  'О себе'=>'string',
														  'Деятельность'=>'string',
                              'Место работы'=>'string',
														  'Можно написать ЛС'=>'string',
														  'Можно позвать в друзья'=>'string',
														  'Можно постить'=>'string',
														  'Верифицированный пользователь'=>'string',
														  'Отношения'=>'string',
                              'Твиттер'=>'string',
                              'LiveJournal'=>'string',
                              'Scype'=>'string',
														);
														$writer->writeSheetHeader('Sheet1', $header );
    $info['found'] = '';
    if(count($result) > 0) {
      $info['found'] .= 'Всего найдено <b> '.num::declension (count($result), (array('пользователь</b>', 'пользователя</b>', 'пользователей</b>')));

      if (!isset($info['demo'])) $info['found'] .= '. Полный список найденных пользователей находится в файле Excel. '; elseif (count($result) > 10) $info['found'] .= '. Оплатите <a href="'.route('tarifs').'">доступ</a>, чтобы система показала полный список пользователей по вашему запросу.';
    } else $info['found'] = NULL;

    $count = 0;
    foreach ($result as $item) {
      $sheet = array();
      $sheet['name'] = $item['first_name'].' '.$item['last_name'];
      $sheet['link'] = 'https://vk.com/id'.$item['vkid'];
      if (isset($item['country'])) $sheet['country'] = $item['country']; else $sheet['country'] = '';
      if (isset($item['city'])) $sheet['city'] = $item['city']; else $sheet['city'] = '';
      if (isset ($item['bdate'])) {
        if (count(explode(".", $item['bdate'])) == 3)	{
          $age = explode(".", $item['bdate']);
          $age = GetAge::age($age[2], $age[1], $age[0]);
        } else $age = '';
              if (count(explode(".", $item['bdate'])) == 2) {
                $bday = (strtotime($item['bdate'].'.'.date('Y'))-date('U'))/(3600 * 24);
              }
              elseif (count(explode(".", $item['bdate'])) == 3) {
                $bday = explode(".", $item['bdate']);
                $bday = (strtotime($bday[0].'.'.$bday[1].'.'.date('Y'))-date('U'))/(3600 * 24);
              }

      } else $age = '';
      $sheet['age'] = $item['age'] = $age;
      $sheet['followers_count'] = $item['followers_count'];
      if (isset($item['about'])) $sheet['about'] = $item['about']; else $sheet['about'] = '';
      if (isset($item['activities'])) $sheet['activities'] = $item['activities']; else $sheet['activities'] = '';
      if (isset($item['occupation'])) $sheet['occupation'] = $item['occupation']; else $sheet['occupation'] = '';

      if ($item['can_write_private_message']==1) $sheet['can_write_private_message'] = $item['can_write_private_message']='да';
      else $sheet['can_write_private_message'] = $item['can_write_private_message']='';
      if ($item['can_send_friend_request']==1) $sheet['can_send_friend_request'] = $item['can_send_friend_request']='да';
      else $sheet['can_send_friend_request'] = $item['can_send_friend_request']='';
      if ($item['can_post']==1) $sheet['can_post'] = $item['can_post']='да';
      else $sheet['can_post'] = $item['can_post']='';
      if ($item['verified']==1) $sheet['verified'] = $item['verified']='да';
      else $sheet['verified'] = $item['verified']='';
      if (isset($item['status'])) {
        if ($item['status']==1) $sheet['status'] = $item['status']='не женат (не замужем)';
        if ($item['status']==2) $sheet['status'] = $item['status']='встречается';
        if ($item['status']==3) $sheet['status'] = $item['status']='помолвлен(-а)';
        if ($item['status']==4) $sheet['status'] = $item['status']='женат (замужем)';
        if ($item['status']==5) $sheet['status'] = $item['status']='всё сложно';
        if ($item['status']==6) $sheet['status'] = $item['status']='в активном поиске';
        if ($item['status']==7) $sheet['status'] = $item['status']='влюблен(-а)';
        if ($item['status']==8) $sheet['status'] = $item['status']='в гражданском браке';
        if ($item['status']==0) $sheet['status'] = $item['status']='';
      } else $sheet['status'] = '';
      if (isset($item['twitter'])) $sheet['twitter'] = $item['twitter']; else $sheet['twitter'] = '';
      if (isset($item['livejournal'])) $sheet['livejournal'] = $item['livejournal']; else $sheet['livejournal'] = '';
      if (isset($item['scype'])) $sheet['scype'] = $item['scype']; else $sheet['scype'] = '';

      $writer->writeSheetRow('Sheet1', $sheet);
      $count++;
      if ($count<=1000) $items[] = $item;
      if (isset($info['demo']) AND $count > 9) break;
    }
    $writer->writeToFile('storage/topusers_search/'.session('vkid').'_topusers_search.xlsx');
    unset($progress);
    $returnHTML = view('layouts.topusers-ajax', ['items' => $items, 'info' => $info])->render();
    return response()->json( array('success' => true, 'html'=>$returnHTML) );
  }
}
