<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\GetUsers;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;

class GetUsersController extends Controller
{
    public function main (Request $request) {

      $info = array();
      $items = array();
      $access_token = session('token');
      $vk = new VKApiClient();
      $user = new VKUser(session('vkid'));
      $rand = $request->rand;
      if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
        //$limit=10;
        $info['demo']=TRUE;
      } //else $limit = 100000;
      $get_users = new GetUsers();
      $groupid = $get_users->groupId($request->groups);
      if (empty($groupid)) $info['warning'] = 'Невозможно определить подписчиков группы';
      else {
        $fields=array();
      	if (isset ($request->common_info)) $fields[] = 'sex,bdate,city,country,online,online_mobile,domain,can_post,can_see_all_posts,can_write_private_message,last_seen';

      	if (isset ($request->site)) $fields[] = 'site';
      	if (isset ($request->contacts)) $fields[] = 'contacts';
      	if (isset ($request->social)) $fields[] = 'connections';
      	if (isset ($request->relation) OR isset ($request->half2)) $fields[] = 'relation';
      	if (isset ($request->bday)) $fields[] = 'bdate';
        if (!empty($fields)) $fields = implode(',', $fields); else $fields = '';

        $items = $get_users->fromGroup($groupid, $fields, 'getusers', $request);
        if (!empty($items) AND count($items) < 1000) $info['found'] = 'Всего нашлось <b>'.num::declension (($get_users->members_count), array('</b> подписчик', '</b> подписчика', '</b> подписчиков'));
        if (!empty($items) AND count($items) >= 1000) $info['found'] = 'Нашлось более 1000 подписчиков';
        if (!empty($items)) {
          if (!isset($info['demo'])) $info['found'] .= '. Полный их список находится в файле Excel';
        } else $info['warning'] = 'Невозможно определить подписчиков группы, соответствующих заданным критериям';

        if (!empty($items[1001]) AND $items[1001] == 'limit vk') {
          unset($items[1001]);
          $info['warning'] = 'При сборе подписчиков группы был достигнут лимит, который устанавливает ВК. Посмотрите файл Excel. Если вы собирали из нескольких групп, попробуйте через несколько часов продолжить с той группы, где остановились.';
          if (count($items) < 2) {
            $items = array();
            $info['found'] = NULL;
            $info['warning'] = 'При сборе подписчиков группы был достигнут лимит, который устанавливает ВК. Попробуйте через несколько часов';
          }
        }
        if (!empty($items[1001]) AND $items[1001] == 'access vk') {
          if (count($groupid) == 1) {
            $info['found'] = NULL;
            $info['warning'] = 'Руководство группы ВК закрыло доступ к списку подписчиков. Ничего собрать не получится';
          } else $info['warning'] = 'Примите во внимание: Руководство одной или нескольких групп ВК закрыло доступ к списку подписчиков. Для этих групп собрать их не получится';
        }
        if (!empty($items[1001]) AND $items[1001] == 'auth vk') {
          $info['found'] = NULL;
          $info['token'] = TRUE;
        }
      }

      $returnHTML = view('layouts.getusers-ajax', ['request' => $request, 'items' => $items, 'info' => $info])->render();
      return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }
}
