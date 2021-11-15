<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VkGroups;
use App\Models\NewUsers;
use \VK\Client\VKApiClient;
use \XLSXWriter;
use \SimpleXLSX;
use \App\MyClasses\GetProgress;
use \App\MyClasses\GetUsers;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use \App\MyClasses\GetAge;
use \App\MyClasses\ListNewGroups;
use Illuminate\Support\Facades\Route;

class NewUsersController extends Controller
{
    public function main() {
      session()->flash('previous-route', Route::current()->getName());
      $list_new_groups = new ListNewGroups();
      $items = $list_new_groups->getFollowList();
      return view('new-users', ['items_groups' => $items]);
    }



    public function add(Request $request) {
      $info = array();
      $items = array();
      $access_token = session('token');
      $vk = new VKApiClient();

      $user = new VKUser(session('vkid'));
      if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
        $limit=1;
        $info['demo'] = TRUE;
      } else $limit = 10;

      $get_users = new GetUsers();
      $groupid = $get_users->groupId($request->group);
      if (empty($groupid)) $info['warning'] = 'Невозможно определить подписчиков группы';
        elseif ($groupid == 'auth vk') $info['token'] = TRUE;
          else try {

            $group_get = $vk->groups()->getById($access_token, array(
                'group_id'		 => $groupid,
                'fields'    	 => 'members_count',
                'v' 			     => '5.101'
            ));
          } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
              $info['token'] = TRUE;
          }
      $new_group = new NewUsers();
      $check_limit = $new_group->where('vkid', session('vkid'))->count();
      if ($check_limit >= $limit) $info['warning'] = 'Вы уже достигли лимита по количеству отслеживаемых групп';


      if (empty ($info['warning']) AND empty($group_get[0]['members_count'])) $info['warning'] = 'Невозможно определить подписчиков группы';
      elseif (empty ($info['warning']) AND $group_get[0]['members_count'] > 500000) $info['warning'] = 'Слишком большая группа, допускается отслеживать группы, не более 500 000 подписчиков';
      if (isset($info['warning'])) {
        $returnHTML = view('layouts.new-users-ajax', ['items' => $items, 'info' => $info])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }

      $data = array();
        if (isset($group_get[0])) {
        	$data['group_id'] = $group_get[0]['id'];
        	$data['name'] = $group_get[0]['name'];
          $users_array = $get_users->fromGroup($groupid, null, 'new-users');

          if (isset($users_array[1001]) AND is_array($users_array)) {
            if ($users_array[1001] == 'access vk') $info['warning'] = 'Руководство группы ВК закрыло доступ к списку подписчиков. Ничего собрать не получится';
            if ($list_users[1001] == 'auth vk')  $info['token'] = TRUE;
            if ($users_array[1001] == 'limit vk') $info['warning'] = 'Достигнут лимит ВК по сбору подписчиков групп, попробуйте через несколько часов';
            $info['found'] = NULL;
            goto ex;
          }

          if ($users_array) {
            $data['uid1'] = $users_array;
            \Debugbar::disable();
            $new_group->updateOrCreate(['vkid' => session('vkid'), 'group_id' => $group_get[0]['id']], $data);
            \Debugbar::enable();
            $info['found'] = 'Группа <b>'.$group_get[0]['name'].'</b> добавлена для отслеживания подписчиков.';
          }

          ex:
          $list_new_groups = new ListNewGroups();
          $items = $list_new_groups->getFollowList();

          $returnHTML = view('layouts.new-users-ajax', ['items_groups' => $items, 'info' => $info])->render();
          return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }
    }


    public function follow(Request $request) {
        $info = array();
        $items_new = array();
        $items_old = array();
        $vkid = session('vkid');
        $token = session('token');
        $new_users = new NewUsers();
        $get_users = new GetUsers();
        $list_users1 = $new_users->where('vkid', $vkid)->where('group_id', $request->id)->first();
        $list_users2 = $get_users->fromGroup($request->id, null, 'new-users');

        if (isset($list_users2[1001]) AND is_array($list_users2)) {
          if ($list_users2[1001] == 'access vk') $info['warning'] = 'Руководство группы ВК закрыло доступ к списку подписчиков. Ничего собрать не получится';
          if ($list_users2[1001] == 'auth vk') $info['token'] = TRUE;
          if ($list_users2[1001] == 'limit vk') $info['warning'] = 'Достигнут лимит ВК по сбору подписчиков групп, попробуйте через несколько часов';
          $info['found'] = NULL;
          goto ex;
        }

        $progress = new GetProgress ($vkid, 'new-users', 'Идёт вычисление новых подписчиков', 1, 1);
        $list_users_old = explode(',', $list_users1['uid1']);
        $list_users_new = explode(',', $list_users2);
        $list_users1->uid1 = $list_users2;
        \Debugbar::disable();
        $list_users1->save();
        \Debugbar::enable();


        $new_users = array_diff($list_users_new, $list_users_old);
        $leave_users = array_diff($list_users_old, $list_users_new);
        $info['new'] = num::declension (count($new_users), (array('</b>новый подписчик', '</b>новых подписчика', '</b>новых подписчиков')));
        $info['leave'] = num::declension (count($leave_users), (array('</b>подписчик', '</b>подписчика', '</b>подписчиков')));

        $info['found'] = 'Всего нашлось <b>'.$info['new'].', а покинуло группу <b>'.$info['leave'];

        if (!empty($new_users) OR !empty($leave_users)) {
            $vk = new VKApiClient();
            $progress = new GetProgress ($vkid, 'new-users', 'Идёт подготовка файла Excel', 1, 1);
      			$writer = new XLSXWriter();
      			if ( $xlsx = SimpleXLSX::parse("storage/new-users/{$vkid}_{$request->id}.xlsx")) {
      					foreach ($xlsx->rows() as $row) $writer->writeSheetRow('Sheet1', $row);
      					if (filesize("storage/new-users/{$vkid}_{$request->id}.xlsx") > 8000000) $info['warning'][] = 'Ваш файл с историей отслеживания новичков слишком велик и скоро будет удалён и заменён на новый. Скачайте и сохраните его, если он вам нужен.';
      					if (filesize("storage/new-users/{$vkid}_{$request->id}.xlsx") > 10000000) unlink("storage/new-users/{$vkid}_{$request->id}.xlsx");
      			}

      														$header = array(

      														  'id'=>'integer',
      														  'Ссылка'=>'string',
      														  'Имя'=>'string',
      														  'Фамилия'=>'string',
      														  'Пол'=>'string',
      														  'Возраст'=>'string',
      														  'Город'=>'string',
      														  'сейчас онлайн'=>'string',
      														  'можно постить на стену'=>'string',
      														  'можно писать в ЛС'=>'string',
      														);
      														$writer->writeSheetHeader('Sheet1', $header );
      $count=0;
        if (count($new_users)>0) {
      				$writer->writeSheetRow('Sheet1', array('Вступили в группу ('.count($new_users).'чел) за', date('d.m.Y H:i')));

      				$count1000=intdiv(count($new_users), 1000);

      				for ($j=0; $j<=$count1000; $j++) {
      					$new_users1=array_slice($new_users, ($j*1000),1000);
      				  $user_ids = implode(",", $new_users1);

                $user_get = $vk->users()->get($token, array(
                  'user_ids'		=> $user_ids,
      						'fields'    	=> 'sex,bdate,city,country,photo_100,online,domain,can_post,can_write_private_message',
      						'v' 			=> '5.101'
                ));

        				if (!empty($user_get)) foreach ($user_get as $item) {
        					$count++;

        						if (!isset($item['photo_100'])) $item['photo_100'] = '';
        						if (isset($item['bdate'])) {
        									if (count(explode(".", $item['bdate'])) == 3)	{
        										$age = explode(".", $item['bdate']);
        										$item['bdate'] = GetAge::age($age[2], $age[1], $age[0]);
        									} else $item['bdate'] = '';

        						} else $item['bdate'] = '';
        						if (!isset($item['city']['title'])) $item['city']['title'] = '';
        						if ($item['can_write_private_message']==1) $item['can_write_private_message']='да';
        						else $item['can_write_private_message']='';
        						if ($item['can_post']==1) $item['can_post']='да';
        						else $item['can_post']='';
        						if ($item['online']==1) $item['online']='да';
        						else $item['online']='';
        						if ($item['sex']==1) $item['sex']='жен';
        						 else if ($item['sex']==2) $item['sex']='муж';
        						  else $item['sex']='';

          					$itemx['id']=$item['id']; $itemx['link']='https://vk.com/'.$item['domain']; $itemx['first_name']=$item['first_name']; $itemx['last_name']=$item['last_name'];
          					$itemx['sex']=$item['sex']; $itemx['bdate']=(string)$item['bdate']; $itemx['city']=$item['city']['title'];
          					$itemx['online']=$item['online']; $itemx['can_post']=$item['can_post']; $itemx['can_write_private_message']=$item['can_write_private_message'];
          					$writer->writeSheetRow('Sheet1', $itemx);
                    $items_new[] = $item;
        				}
      				}
          }
      $count=0;
      if (count($leave_users)>0) {

      				$writer->writeSheetRow('Sheet1', array('Отписались от группы ('.count($leave_users).'чел) за', date('d.m.Y H:i')));

      				$count1000=intdiv(count($leave_users), 1000);

      				for ($j=0; $j<=$count1000; $j++) {
      					$new_users1=array_slice($leave_users,($j*1000),1000);
      				  $user_ids = implode(",",$new_users1);
                $user_get = $vk->users()->get($token, array(
                  'user_ids'		=> $user_ids,
      						'fields'    	=> 'sex,bdate,city,country,photo_100,online,domain,can_post,can_write_private_message',
      						'v' 			=> '5.101'
                ));

      				if (!empty($user_get)) foreach ($user_get as $item) {
      					$count++;
                if (!isset($item['photo_100'])) $item['photo_100'] = '';
                if (isset($item['bdate'])) {
                      if (count(explode(".", $item['bdate'])) == 3)	{
                        $age = explode(".", $item['bdate']);
                        $item['bdate'] = GetAge::age($age[2], $age[1], $age[0]);
                      } else $item['bdate'] = '';

                } else $item['bdate'] = '';
                if (!isset($item['city']['title'])) $item['city']['title'] = '';
                if ($item['can_write_private_message']==1) $item['can_write_private_message']='да';
                else $item['can_write_private_message']='';
                if ($item['can_post']==1) $item['can_post']='да';
                else $item['can_post']='';
                if ($item['online']==1) $item['online']='да';
                else $item['online']='';
                if ($item['sex']==1) $item['sex']='жен';
                 else if ($item['sex']==2) $item['sex']='муж';
                  else $item['sex']='';

                $itemx['id']=$item['id']; $itemx['link']='https://vk.com/'.$item['domain']; $itemx['first_name']=$item['first_name']; $itemx['last_name']=$item['last_name'];
                $itemx['sex']=$item['sex']; $itemx['bdate']=(string)$item['bdate']; $itemx['city']=$item['city']['title'];
                $itemx['online']=$item['online']; $itemx['can_post']=$item['can_post']; $itemx['can_write_private_message']=$item['can_write_private_message'];
                $writer->writeSheetRow('Sheet1', $itemx);
                $items_old[] = $item;

      				}
      			}
          }
      				$writer->writeToFile("storage/new-users/{$vkid}_{$request->id}.xlsx");
				}
        ex:
        $returnHTML = view('layouts.new-users-ajax', ['items_new' => $items_new, 'items_old' => $items_old, 'info' => $info])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }


    public function del(Request $request) {
      $vkid = session('vkid');
      $gid = $request->del;
      if ($gid) {
        $new_group = new NewUsers();
        $name = $new_group->where('vkid', $vkid)->where('group_id', $gid)->first();
        $name->delete();
        if (file_exists("storage/new-users/{$vkid}_{$gid}.xlsx")) unlink("storage/new-users/{$vkid}_{$gid}.xlsx");

        return back()->with('success', 'Группа '.$name->name.' больше не отслеживается');
      }
      else return back()->with('warning', 'Не удалось определить группу для удаления');
    }
}
