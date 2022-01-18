<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use App\Models\Stream\Projects;
use App\Models\Stream\Links;
use Illuminate\Support\Facades\Schema;
use App\Models\Stream\FileXLS;
use \App\MyClasses\MyRules;
use App\Models\Stream\StreamKey;

class ProjectController extends Controller
{
  public function add(Request $request) {
    if (empty(session('vkid'))) return back()->with('error', 'Ваша сессия устарела, необходимо авторизоваться заново');
    $request->validate([
        'project_name' => 'required|min:2|max:63',
    ]);
    if (session('demo')) return back()->with('error', 'В демо-режиме нельзя создавать проекты');
    $user = new VKUser(session('vkid'));
    if (empty($user->project_limit)) return back()->with('error', 'Ваш тариф не допускает создания проектов');
    $projects = new Projects();
    $project = $projects->where('vkid', session('vkid'))->groupBy('project_name')->pluck('project_name')->toArray();

    if ($user->project_limit > count($project)) {
    	if (strtotime($user->date) > date('U')) {
    		if (!in_array($request->project_name, $project)) {
          $new_project = new Projects();
          $new_project->vkid = session('vkid');
          $new_project->project_name = $request->project_name;
          $new_project->save();
    			$new_link = new Links();
          $new_link->vkid = session('vkid');
          $new_link->project_name = $request->project_name;
          $new_link->link_name = 'Доп.посты';
          $new_link->save();
          return back()->with('success', 'Проект <b>'.$request->project_name.'</b> успешно создан');
    		}	else return back()->with('warning', 'Проект с таким именем уже существует');
    	} else return back()->with('error','Срок действия тарифа истёк');
    } else return back()->with('warning', 'Вашим тарифом предусмотрено не более <b>'.num::declension ($user->project_limit, array('</b>проекта', '</b>проектов', '</b>проектов')));
  }

  public function del(Request $request) {
    if (empty(session('vkid'))) return back()->with('error', 'Ваша сессия устарела, необходимо авторизоваться заново');
    $projects = new Projects();
    $project = $projects->find($request->del);
    $stream = new Streamkey();
    $stream = $stream->find(1);
    if ($project) {
      $links = new Links();
      $user_links = $links->where('vkid', session('vkid'))->where('project_name', $project->project_name)->pluck('id');
      $project_name = $project->project_name;
      $project_to_del = $projects->where('vkid', session('vkid'))->where('project_name', $project->project_name)->pluck('id');
      $rules_to_del = MyRules::getRules($project_name);
      foreach (array_column($rules_to_del, 'rule') as $rule_tag) {
        $ch = curl_init();
  			curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
  			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  			curl_setopt($ch, CURLOPT_POSTFIELDS, '{"tag":"'.session('vkid').$rule_tag.'"}');
  			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  			$out = json_decode(curl_exec($ch), true);
  			curl_close($ch);
      }
      Schema::dropIfExists('clouds_'.session('vkid').$project_name);
      Schema::dropIfExists('tags_'.session('vkid').$project_name);
      $projects->destroy($project_to_del);
      $links->destroy($user_links);
      return back()->with('success', 'Проект <b>'.$project_name.'</b> успешно удалён');
    } else return back()->with('warning', 'Проект не найден, возможно он уже был удалён');
  }

  public function delFile(Request $request) {
    $filename = FileXLS::find($request->del);
    if ($filename->link AND unlink('storage/stream/'.$filename->link.'.xlsx')) {
      $filename->delete();
      return back()->with('success', 'Файл <b>'.$filename->link.'</b> успешно удалён');
    } else return back()->with('warning', 'Файл не найден, возможно он уже был удалён');
  }
}
