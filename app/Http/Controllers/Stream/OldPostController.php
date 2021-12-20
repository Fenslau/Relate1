<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream\Projects;
use App\Models\Stream\OldPosts;
use App\Models\Stream\StreamData;
use \App\MyClasses\VKUser;
use \App\MyClasses\MyRules;
use Illuminate\Support\Facades\DB;

class OldPostController extends Controller
{
    public function add($project_name, Request $request) {
      if ($request->get_old_stop) {
        $rules = Projects::select('*', DB::raw("CONCAT (vkid, '', rule) AS rule_tag"))->where('vkid', session('vkid'))->where('project_name', $project_name)->whereNull('old')->whereNotNull('rule')->pluck('rule_tag')->toArray();
        OldPosts::whereIn('user', $rules)->delete();
        return back()->with('success', 'Сбор старых постов остановлен');
      }
      if (empty($request->end_date)) return back()->with('error', 'Необходимо указать дату');
        $old_post = new OldPosts();
        $data['vkid'] = session('vkid');
        $data['end_date'] = strtotime($request->end_date);
        $data['token'] = session('token');

        $user = new VKUser(session('vkid'));

        if (empty($user->old_post_fact) OR $user->old_post_fact > $user->old_post_limit) return back()->with('warning', 'Вы исчерпали лимит по сбору старых постов ('.(isset($user->old_post_limit) ? $user->old_post_limit : 0).') для вашего <a href="'.route('tarifs').'">тарифа</a>');

        $success = 'Вы заказали сбор прошлых постов ';
        if ($request->rule) {
          if (in_array($request->rule, MyRules::getOldRules($project_name))) return back()->with('warning', 'Сбор старых постов по старым правилам невозможен');
          $success .= 'по правилу <b>'.$request->rule.'</b>';
          $data['user'] = session('vkid').$request->rule;
          $data['start_date'] = StreamData::select(DB::raw("min(action_time) AS mindate"))->where('user', $data['user'])->where('check_trash', 0)->first()->mindate;
          $old_post->updateOrCreate(['vkid' => session('vkid'), 'user' => session('vkid').$request->rule], $data);
        }
        else {
          $success .= 'по проекту <b>'.$project_name.'</b>';
          foreach (array_column(MyRules::getRules($project_name), 'rule') as $project_rule) {
            $data['user'] = session('vkid').$project_rule;
            $data['start_date'] = StreamData::select(DB::raw("min(action_time) AS mindate"))->where('user', $data['user'])->where('check_trash', 0)->first()->mindate;
            $old_post->updateOrCreate(['vkid' => session('vkid'), 'user' => session('vkid').$project_rule], $data);
          }
        }
        $success .= ' до <b>'.date('d.m.yг', strtotime($request->end_date)).'</b> включительно. Посты собираются довольно быстро, но если их окажется слишком много, это может занять даже несколько дней. Следите за последними страницами, их количество будет увеличиваться по мере накопления постов';

        return back()->with('success', $success);
    }
}
