<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use App\Models\Stream\Projects;
use App\Models\Stream\Links;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
  public function add(Request $request) {
    $request->validate([
        'project_name' => 'required|min:2|max:63',
    ]);
    $user = new VKUser(session('vkid'));
    if ($user->project_limit == 0) return back()->with('error', 'Ваш тариф не допускает создания проектов');
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
    $projects = new Projects();
    $project = $projects->find($request->del);
    if ($project) {
      $links = new Links();
      $user_links = $links->where('vkid', session('vkid'))->where('project_name', $project->project_name)->pluck('id');
      $project_name = $project->project_name;
      $project_to_del = $projects->where('vkid', session('vkid'))->where('project_name', $project->project_name)->pluck('id');
      $projects->destroy($project_to_del);
      $links->destroy($user_links);
      DB::statement('DROP TABLE IF EXISTS clouds_'.session('vkid').$project_name.'');
      DB::statement('DROP TABLE IF EXISTS tags_'.session('vkid').$project_name.'');
      return back()->with('success', 'Проект <b>'.$project_name.'</b> успешно удалён');
    } else return back()->with('warning', 'Проект не найден, возможно он уже был удалён');

  }
}
