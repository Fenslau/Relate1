<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \App\MyClasses\VKUser;
use \App\MyClasses\Groups;
use \App\MyClasses\num;
use App\Models\Top;

class MainController extends Controller
{
    public function main() {
      if (session('demo')) session(['demo' => 0, 'vkid' => session('realvkid')]);
      $user = new VKUser(session('vkid'));
      if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
        $info['demo']=TRUE;
      } else $info['demo']=NULL;
      $group = new Groups();
      $group->read('temp/top1000.xlsx');
      $time = date('d.m.y H:i', Top::find(1)->time);
      if (count($group->groups)>0) {
        return view('home', ['info' => $info, 'items' => $group->groups, 'time' => $time]);
      } else return view('home')->with('danger', 'На данный момент невозможно отобразить список топ1000 груп ВК, попробуйте через 5 минут.');
    }

    public function search(Request $request) {
      $vk = new VKApiClient();

      if (!empty($request->city)) {
        $params = array(
          'country_id' => 1,
          'q'		       => $request->city,
          'count'      => '1',
          'v'          => '5.95'
        );
        try {
          $city = $vk->database()->getCities(session('token'), $params);
        } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
          $info['token'] = TRUE;
          $returnHTML = view('layouts.home-ajax', ['items' => NULL, 'info' => $info])->render();
          return response()->json( array('success' => true, 'html'=>$returnHTML) );
        }

        if (isset($city['items'][0]['id'])) {
          $city['id'] = $city['items'][0]['id'];
          $city['title'] = $city['items'][0]['title'];
        }
        else $city['id'] = '';
      } else $city['id'] = '';

      $params = array(
        'q'		     => $request->group_name,
        'city_id'  => $city['id'],
        'sort'	   => $request->sort,
        'count'    => 1000,
        'v'        => '5.95'
      );
      $info = array();
      $user = new VKUser(session('vkid'));
      if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
        $params['count']=10;
        $info['demo']=TRUE;
      }
      $info['search']=TRUE;
      try {
        $groups = $vk->groups()->search(session('token'), $params);
      } catch (\VK\Exceptions\Api\VKApiParamException $exception) {
        $info['warning'] = 'Укажите что-нибудь в названии группы для поиска';
        $returnHTML = view('layouts.home-ajax', ['items' => NULL, 'info' => $info])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }
      if (min($params['count'], $groups['count']) > 0) {
        $info['found']='Всего по вашему запросу <b>'.$request->group_name.'</b> нашлось <b> '.num::declension ($groups['count'], (array('группа</b>', 'группы</b>', 'групп</b>')));
        if (!empty($city['id'])) $info['found'] .= ' из города <b>'.$city['title'].'</b>';
        $our_groups = array_slice($groups['items'], 0, $params['count']);
        $our_groups = array_column($our_groups, 'id');

        $group = new Groups();

      $group->get1000Groups($our_groups, session('token'), $request->rand);

    } else {
      $returnHTML = view('layouts.home-ajax', ['info' => $info])->render();
      return response()->json( array('success' => true, 'html'=>$returnHTML) );

    }

      if ($request->stat) $group->getStats(session('token'), $request->rand);

      if ($request->date) $group->getLastPostDate(session('token'), $request->rand);
      $info['filetime'] = date('d_m_y_H_i_s');
      $group->write('storage/simple_search/'.session('vkid').'_simple_search_'.$info['filetime'].'.xlsx');

      $returnHTML = view('layouts.home-ajax', ['items' => $group->groups, 'info' => $info])->render();
      return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }
}
