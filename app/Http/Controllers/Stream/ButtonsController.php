<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream\Links;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\StreamKey;
use \App\MyClasses\MyRules;

class ButtonsController extends Controller
{
  public function check($button_name, Request $request) {

    if ($button_name == 'ruleErasePosts') {
      $rule_tag	= $request->tag;
      $project	= $request->project;
      $vkid	= session('vkid');

      $posts = new StreamData();
      $projects = new Projects();
      $posts->where('user', $vkid.$rule_tag)->where('check_flag', 0)->where(function ($query) {
          $query->where('user_links', '')
          ->orWhere('user_links', 'Доп.посты');
      })->delete();
      $rule = $projects->where('vkid', $vkid)->where('project_name', $project)->where('rule', $rule_tag)->update(['re_cloud' => 1]);
      return response()->json(array('success' => 'Удалены посты из правила <b>'.$rule_tag));
    }


    if ($button_name == 'ruleDelete') {
      $stream = new Streamkey();
      $projects = new Projects();
      $posts = new StreamData();
      $stream = $stream->find(1);
      $vkid	= session('vkid');
      $rule_tag = $request->rule_tag;

      if ($request->admin == 'true') $vkid = '';
      $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '{"tag":"'.$vkid.$rule_tag.'"}');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$out = json_decode(curl_exec($ch), true);
			curl_close($ch);
      if ($request->admin == 'true' AND !empty($out['code']) AND $out['code'] == 200) {
        $projects->where('vkid', session('vkid'))->where('rule', str_replace(session('vkid'), '', $rule_tag))->delete();
        return back()->with('success', 'Правило <b>'.$rule_tag.'</b> удалено из VK Streaming API');
      }
      $vkid = session('vkid');
      if (!empty($out['code']) AND $out['code'] == 200) {
        $count_posts = $posts->where('user', $vkid.$rule_tag)->first();
        if ($count_posts) $rule = $projects->where('vkid', $vkid)->where('rule', $rule_tag)->update(['re_cloud' => 1, 'old' => 1]);
        else $rule = $projects->where('vkid', $vkid)->where('rule', $rule_tag)->delete();
				return back()->with('success', 'Правило <b>'.$rule_tag.'</b> удалено. Посты по нему больше собираться не будут. Однако те, что уже были собраны, будут доступны по ссылке из раздела "Старые правила" в боковом меню. Если вы захотите возобновить сбор постов по нему, создайте новое правило с точно таким же названием');
			} else return back()->with('error', 'Что-то пошло не так и правило не удалилось');
    }


    if ($button_name == 'oldRuleDelete') {
      $projects = new Projects();
      $vkid	= session('vkid');
      $rule_tag = $request->rule_tag;
      $rule = $projects->where('vkid', $vkid)->where('rule', $rule_tag)->delete();
      return back()->with('success', 'Правило <b>'.$rule_tag.'</b> удалено окончательно');
    }


    if ($button_name == 'userLinksErasePosts') {
      $user_link	= $request->tag;
      $project	= $request->project;
      $vkid	= session('vkid');
      $posts = new StreamData();
      $rules = array_column(array_merge(MyRules::getRules($project), MyRules::getOldRules($project)), 'rule');
        $rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $rules);
      $posts->whereIn('user', $rules)->where('user_links', $user_link)->where('check_flag', 0)->update(['check_trash' => 2]);
      return response()->json(array('success' => 'Папка <b>'.$user_link.'</b> очищена'));
    }


    if ($button_name == 'userLinksDelete') {
      $user_link	= $request->tag;
      if ($user_link = 'Доп.посты') return back()->with('error', 'Папка <b> Доп.посты </b> не может быть удалена, она нужна для корректной работы системы');
      $project	= $request->project;
      $vkid	= session('vkid');
      $posts = new StreamData();
      $rules = array_column(array_merge(MyRules::getRules($project), MyRules::getOldRules($project)), 'rule');
        $rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $rules);
      $posts->whereIn('user', $rules)->where('user_links', $user_link)->where('check_flag', 0)->update(['check_trash' => 2]);

      $links = new Links();
      $links->where('vkid', session('vkid'))->where('project_name', $request->project)->where('link_name', $request->user_link)->delete();
      return back()->with('success', 'Папка <b>'.$request->user_link.'</b> удалена');
    }


    if ($button_name == 'trashErase') {
      $project	= $request->project;
      $vkid	= session('vkid');
      $posts = new StreamData();
      $rules = array_column(array_merge(MyRules::getRules($project), MyRules::getOldRules($project)), 'rule');
        $rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $rules);
      $posts->whereIn('user', $rules)->where('check_trash', 1)->update(['check_trash' => 2]);
      return response()->json(array('success' => 'Корзина проекта <b>'.$project.'</b> очищена'));
    }


    if ($button_name == 'flagErase') {
      $project	= $request->project;
      $vkid	= session('vkid');
      $posts = new StreamData();
      $rules = array_column(array_merge(MyRules::getRules($project), MyRules::getOldRules($project)), 'rule');
        $rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $rules);
      $posts->whereIn('user', $rules)->where('check_flag', 1)->update(['check_trash' => 2]);
      return response()->json(array('success' => 'Папка Избранное проекта <b>'.$project.'</b> очищена'));
    }
  }
}
