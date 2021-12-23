<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream\Links;

class UserlinkController extends Controller
{
      public function add(Request $request) {
        if (empty(session('vkid'))) return back()->with('error', 'Ваша сессия устарела, необходимо авторизоваться заново');
        if (empty($request->link_name)) return back()->with('error', 'Задайте какое-нибудь имя папки.');
        $links = new Links();
        $link = $links->where('vkid', session('vkid'))->where('project_name', $request->project_name)->pluck('link_name')->toArray();
        if (!in_array($request->link_name, $link)) {
          $links->vkid = session('vkid');
          $links->project_name = $request->project_name;
          $links->link_name = $request->link_name;
          $links->save();
          return back()->with('success', 'Пользовательская папка <b>'.$request->link_name.'</b> добавлена');
        }
          else return back()->with('error', 'Папка с таким именем уже существует');
        }

}
