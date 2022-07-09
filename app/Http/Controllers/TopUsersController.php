<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;

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

      if (!isset($info['demo'])) $info['found'] .= '. Полный список найденных пользователей находится в файле Excel. '; else $info['found'] .= '. Оплатите <a href="'.route('tarifs').'">доступ</a>, чтобы система показала полный список пользователей по вашему запросу.';
    } else $info['found'] = NULL;

    $count = 0;
    foreach ($items as $item) {
      $sheet = array();
      $sheet['name'] = $item['first_name'].' '.$item['last_name'];
      $sheet['link'] = 'https://vk.com/id'.$item['vkid'];
      $sheet['country'] = $item['country'];
      $sheet['city'] = $item['city'];
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
      $sheet['about'] = $item['about'];
      $sheet['activity'] = $item['activity'];
      $sheet['occupation'] = $item['occupation'];

      if ($item['can_write_private_message']==1) $sheet['can_write_private_message'] = $item['can_write_private_message']='да';
      else $sheet['can_write_private_message'] = $item['can_write_private_message']='';
      if ($item['can_send_friend_request']==1) $sheet['can_send_friend_request'] = $item['can_send_friend_request']='да';
      else $sheet['can_send_friend_request'] = $item['can_send_friend_request']='';
      if ($item['can_post']==1) $sheet['can_post'] = $item['can_post']='да';
      else $sheet['can_post'] = $item['can_post']='';
      if ($item['verified']==1) $sheet['verified'] = $item['verified']='да';
      else $sheet['verified'] = $item['verified']='';

      if ($item['status']==1) $sheet['status'] = $item['status']='не женат (не замужем)';
      if ($item['status']==2) $sheet['status'] = $item['status']='встречается';
      if ($item['status']==3) $sheet['status'] = $item['status']='помолвлен(-а)';
      if ($item['status']==4) $sheet['status'] = $item['status']='женат (замужем)';
      if ($item['status']==5) $sheet['status'] = $item['status']='всё сложно';
      if ($item['status']==6) $sheet['status'] = $item['status']='в активном поиске';
      if ($item['status']==7) $sheet['status'] = $item['status']='влюблен(-а)';
      if ($item['status']==8) $sheet['status'] = $item['status']='в гражданском браке';

      $sheet['twitter'] = $item['twitter'];
      $sheet['livejournal'] = $item['livejournal'];
      $sheet['scype'] = $item['scype'];

      $writer->writeSheetRow('Sheet1', $sheet);
      $count++;
      if ($count<=1000) $items[] = $item;
      if (isset($info['demo']) AND $count > 9) break;
    }
    $writer->writeToFile('storage/topusers_search/'.session('vkid').'_topusers_search.xlsx');

    $returnHTML = view('layouts.topusers-ajax', ['items' => $items, 'info' => $info])->render();
    return response()->json( array('success' => true, 'html'=>$returnHTML) );
  }
}
