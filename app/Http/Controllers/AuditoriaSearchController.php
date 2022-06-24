<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VkGroups;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\GetUsers;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;

class AuditoriaSearchController extends Controller
{
  public function search(Request $request) {
    $retry = FALSE;
    if ($request->to > 100000) $request->to = 100000;
    $info = array();
    $info['found'] = 1;
    $items = array();
    $array_groups = array();
    $vk = new VKApiClient();
    $get_users = new GetUsers();
    $access_token = session('token');
    $rand = $request->rand;
    $groupid = $get_users->groupId($request->group);
    if (empty($groupid)) $info['found'] = NULL;
    else {
      try {
          $list_users = $vk->groups()->getMembers($access_token, array(
              'group_id'		 => $groupid,
              'v' 			     => '5.101'
          ));
      } catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
            $info['warning'] = 'При сборе подписчиков группы был достигнут лимит, который устанавливает ВК. Попробуйте через несколько часов';
            $info['found'] = NULL;
            goto ex;
          }
          catch (\VK\Exceptions\Api\VKApiAccessException $exception) {
            $info['found'] = NULL;
            $info['warning'] = 'Руководство группы ВК закрыло доступ к списку подписчиков. Ничего собрать не получится';
            goto ex;
          }
          catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
              $info['found'] = NULL;
              $info['token'] = TRUE;
              goto ex;
          }
    }

    if (empty($list_users['count'])) $info['warning'] = 'Невозможно определить подписчиков группы';
    elseif ($list_users['count'] > 100000) $info['warning'] = 'Слишком большая исходная группа, допускаются группы, не более 100 000 подписчиков';
    if (isset($info['warning']) OR $info['found'] === NULL) {
      $info['found'] = NULL;
      $returnHTML = view('layouts.auditoria-ajax', ['items' => $items, 'info' => $info])->render();
      return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }

    $list_users = $get_users->fromGroup($groupid, null, 'auditoria');
    if (isset($list_users[1001]) AND is_array($list_users)) {
      if ($list_users[1001] == 'access vk') $info['warning'] = 'Руководство группы ВК закрыло доступ к списку подписчиков. Ничего собрать не получится';
      if ($list_users[1001] == 'auth vk') $info['token'] = TRUE;
      if ($list_users[1001] == 'limit vk') $info['warning'] = 'Достигнут лимит ВК по сбору подписчиков групп, попробуйте через несколько часов';
      $info['found'] = NULL;
      goto ex;
    }
    $list_users = explode(',', $list_users);
    $message = 'Идёт сбор информации о группах подписчиков';
    if (count($list_users) > 30000) $message = 'Вы указали группу более 30 000 подписчиков. Поиск групп с похожей целевой аудиторией может занять более трёх часов, т.к. Вконтакте ограничивает скорость передачи данных';
    $count25 = intdiv(count($list_users), 25);
    $array_groups = array();
    $progress = new GetProgress(session('vkid'), 'auditoria'.$rand, $message, $count25, 1);
    for ($j=0; $j<=$count25; $j++) {
			$new_users1=array_slice($list_users,($j*25), 25);

					$code_1 = 'return[';
					for ($i=0; $i<25; $i++) {
						if (isset($new_users1[$i])) $code_1 = $code_1.'API.users.getSubscriptions({"user_id":'.$new_users1[$i].',"v":5.101,"extended":1,"fields":"members_count","count":200}),';
					}
					$code_1 = $code_1.'];';

retry:   try {
	       $stat1 = $vk->getRequest()->post('execute', $access_token, array(
            'code' 			    => $code_1,
            'access_token'  => $access_token,
            'v' 			      => '5.95'
          ));}
          catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {

            goto retry;
          }
          catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
            $info['found'] = NULL;
            $info['warning'] = 'Истёк срок действия токена ВК, авторизуйтесь заново';
            $returnHTML = view('layouts.auditoria-ajax', ['items' => $items, 'info' => $info])->render();
            return response()->json( array('success' => true, 'html'=>$returnHTML) );
          }
          catch (\VK\Exceptions\VKClientException $exception) {
            if (!$retry) {
              $retry = TRUE;
              sleep(5);
              goto retry;
            }
            $info['warning'] = 'Во время сбора информации произошел сбой со стороны ВК, поэтому данные могут быть неполными';
            $retry = FALSE;
            continue;
          }
      $retry = FALSE;
			foreach ($stat1 as $item)
				if (isset($item['items'])) foreach ($item['items'] as $item1)
				if (isset($item1['members_count'])) if ($item1['members_count'] > $request->from AND $item1['members_count'] < $request->to)
					if ($item1['id'] != $groupid) $array_groups[]=$item1['id'];

		$progress->step();
		}

    unset($progress);
    $progress = new GetProgress(session('vkid'), 'auditoria'.$rand, 'Идёт определение наиболее часто встречающихся групп...', 1, 1);

    $popular = array_count_values($array_groups);
    $max = 2;
    $maxi = 0;
    $group_ids=array();
    $index=array();
    if ((count($popular)) < 3) {
      $info['warning'] = 'Исходная группа слишком мала.';
      if ((count($popular)) < 2) goto ex;
    }
    while ($max > 1) {
      $maxi++;
      if ($maxi>500) break;
      if (!empty($popular)) $max = max($popular);
      $result = array_search($max, $popular);

      $group_ids[] = $result;
      $index[]=$max;
      $popular[$result]=0;
    }

    $group_ids=array_slice($group_ids, 0, 500);
    $group_ids=implode(',', $group_ids);

    $group_get = $vk->groups()->getById($access_token, array(
        'group_ids'		 => $group_ids,
        'lang'   		   => 'ru',
        'fields'    	 => 'site,verified,wall,city,market,members_count',
        'v' 			     => '5.101'
    ));

    $user = new VKUser(session('vkid'));
    if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
      $limit=10;
      $info['demo']=TRUE;
    } else $limit = 500;

    unset($progress);
    $progress = new GetProgress(session('vkid'), 'auditoria'.$rand, 'Записывается файл Excel', 1, 1);


    $writer = new XLSXWriter();
                            $header = array(
                              'id группы'=>'integer',
														  'Ссылка'=>'string',
														  'Название'=>'string',
														  'Город'=>'string',
														  'Подписчики'=>'integer',
														  'Сайт'=>'string',
														  'Верифицированно'=>'string',
														  'Стена'=>'string',
														  'Частота'=>'integer',
                              'Аватарка'=>'string',
                            );
                            $writer->writeSheetHeader('Sheet1', $header );

                            for ($i=0; $i<min($limit, count($index)); $i++) {
                              $item = array();
                            	if (!isset($group_get[$i]['city']['title'])) $group_get[$i]['city']['title'] = '';
                            		if (@$group_get[$i]['wall']==0) $group_get[$i]['wall']='выключена';
                            		if ($group_get[$i]['wall']==1) $group_get[$i]['wall']='открытая';
                            		if ($group_get[$i]['wall']==2) $group_get[$i]['wall']='ограниченная';
                            		if ($group_get[$i]['wall']==3) $group_get[$i]['wall']='закрытая';
                            		if (@$group_get[$i]['verified']==1) $group_get[$i]['verified']='да';
                            			else $group_get[$i]['verified']='';

                            		$item['group_id']=$group_get[$i]['id'];
                            		$item['link']='https://vk.com/'.$group_get[$i]['screen_name'];
                            		$item['name']=$group_get[$i]['name'];
                            		$item['city']=$group_get[$i]['city']['title'];
                            		$item['members_count']=@$group_get[$i]['members_count'];

                            		if (isset($group_get[$i]['site'])) $item['site']=$group_get[$i]['site']; else $item['site']='';
                            		$item['verified']=$group_get[$i]['verified'];
                            		$item['wall']=$group_get[$i]['wall'];
                            		$item['freq']=$index[$i];
                            		$item['photo']=$group_get[$i]['photo_200'];
                            		$writer->writeSheetRow('Sheet1', $item);
                                $items[] = $item;

                            }
    $info['found'] = 'Нашлось <b>'.num::declension (count($items), (array('</b>группа,', '</b>группы,', '</b>групп,'))).' где сидит аудитория, похожая на подписчиков исходной группы';
    $writer->writeToFile('storage/auditoria/'.session('vkid').'_auditoria.xlsx');
ex:
//    return view('auditoria', ['items' => $items, 'info' => $info]);
 $returnHTML = view('layouts.auditoria-ajax', ['items' => $items, 'info' => $info])->render();
 return response()->json( array('success' => true, 'html'=>$returnHTML) );
  }
}
