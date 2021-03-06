<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamKey;
use \VK\Client\VKApiClient;
use App\Models\Stream\FileXLS;

class StreamController extends Controller
{
    public function main() {
      $user = new VKUser(session('vkid'));
      if (strpos($user->demo, 'streaming') !== FALSE) {
      			$info['success'] = '<p class="text-uppercase text-center">Ваш <a href="'.route('tarifs').'">тариф</a> <b>'.
      			str_replace('streaming', 'post', $user->demo).'</b> оплачен до <b style="color: ';
      				if (strtotime($user->date) - date('U') < (3600*24) AND strtotime($user->date) - date('U') > 0) $info['success'] .= 'orange';
              if (strtotime($user->date) - date('U') > (3600*24)) $info['success'] .= 'green';
              if (strtotime($user->date) - date('U') < (0)) {
                $info['success'] .= 'red;">'.date("d.m.Y H:i", strtotime($user->date)).'</b>.';
                $info['warning'] = 'Срок действия вашего тарифа истёк, Вы не можете создавать или менять правила или проекты. Ваши посты будут доступны еще три дня после срока окончания.';
      				}
        			else {
        			$info['success'] .= ';">'.date("d.m.Y H:i", strtotime($user->date)).'</b>.
        			Вам доступно создание <b>'.num::declension ($user->project_limit, array('</b>проекта', '</b>проектов', '</b>проектов')).', <b>
        			'.num::declension ($user->rules_limit, array('</b>правила в проекте', '</b>правил в проекте', '</b>правил в проекте')).
        			' и закачка <b>'.num::declension ($user->old_post_limit-$user->old_post_fact, array('"</b>старого" поста', '</b>"старых" постов', '</b>"старых" постов')).' из '.$user->old_post_limit.'.</p>';
        			}
      } else $info['tarif'] = '<p class="text-uppercase text-center">Для доступа к полной версии подключте <a href="'.route('tarifs').'">тариф</a> семейства POST</p>';
      $projects = new Projects();
      $my_projects = $projects->where('vkid', session('vkid'))->whereNull('rule')->get()->toArray();
      foreach ($my_projects as &$my_project) {
        $my_project['rules_count'] = $projects->where('vkid', session('vkid'))->where('project_name', $my_project['project_name'])->whereNotNull('rule')->count();
        $my_project['count_stream_records'] = $projects->where('vkid', session('vkid'))->where('project_name', $my_project['project_name'])->pluck('count_stream_records')->sum();
      }
      $my_files = $vkids = $vk_rules = NULL;
      $files = new FileXLS();
      $my_files = $files->where('vkid', session('vkid'))->get()->toArray();

      if (!session('demo') AND (session('vkid') == 151103777 OR session('realvkid') == 151103777 OR session('vkid') == 409899462 OR session('realvkid') == 409899462)) {
        $info['admin'] = TRUE;
        $vkids = $projects->distinct()->pluck('vkid')->toArray();
        $stream = new Streamkey();
        $stream = $stream->find(1);
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>true,
                "verify_peer_name"=>true,
            ),
        );
        $rules = json_decode(file_get_contents("https://$stream->endpoint/rules?key=$stream->streamkey", false, stream_context_create($arrContextOptions)), true);
        if (!empty($rules['rules'])) $vk_rules = array_column($rules['rules'], 'tag'); else $vk_rules = NULL;
      }
      return view('stream', ['info' => $info, 'vkids' => $vkids, 'vk_rules' => $vk_rules, 'items' => $my_projects, 'files' => $my_files]);
    }

    public function gen() {
      $key = new StreamKey();
      $vk = new VKApiClient();
      $streamkey = $vk->streaming()->getServerUrl(env('ACCESS_TOKEN'),
      array(
        'v' 			=> '5.131'
      ));
      $streamkey['streamkey'] = $streamkey['key'];
      unset($streamkey['key']);
      if (!empty($streamkey['endpoint']) AND !empty($streamkey['streamkey'])) {
        $key->updateOrCreate(['id' => 1], $streamkey);
        return back()->with('success', 'Новый ключ сгенерирован и записан в базу данных');
      } else return back()->with('error', 'Не удалось получить ключ потока');

    }

    public function fakeVKID(Request $request) {
      if (is_numeric($request->fakevkid)) {
        session(['realvkid' => session('vkid')]);
        session(['vkid' => $request->fakevkid]);
        return back()->with('success', 'Ваш ВК id подменён на '.$request->fakevkid);
      } else return back()->with('warning', 'Выберите ВК id из списка');
    }

    public function demo() {
      if (empty(session('vkid'))) return back()->with('error', 'Авторизуйтесь ВК для ознакомления с услугой в демо-режиме');
      else {
        session(['demo' => 1, 'realvkid' => session('vkid'), 'vkid' => 151103777]);
        return redirect()->route('post', 'Demo');
      }
    }

    public function endDemo() {
        session(['demo' => 0, 'vkid' => session('realvkid')]);
        return back()->with('success', 'Приходите к нам ещё!');
    }
}
