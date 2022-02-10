<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\IgnoredGroups;
use App\Models\Toppost;
use App\Models\Stream\Authors;

class CheckController extends Controller
{
    public function check($check_name, Request $request) {
      $message = 'Функция выполнена некорректно';

      if ($check_name == 'ignore') {
        $author_id = Toppost::find($request->name)->author_id;
        if ($author_id) {
          IgnoredGroups::updateOrCreate(['group_id' => $author_id], ['group_id' => $author_id]);
          $name = Authors::where('author_id', $author_id)->first()->name;
          $message = 'Постов автора <b>'.$name.'</b> больше не будет в ленте после <a class="cursor-pointer text-link" onclick="location.reload();">перезагрузки</a> страницы';
        } else $message = 'Такой автор не найден';


      }

      if ($check_name == 'trash') {
          $posts = new StreamData;
          $dublikaty = $posts->find($request->name);
          $dublikat = $dublikaty->dublikat;

        	if ($request->checked == 'true') {
        		if (!empty($dublikat) AND $dublikat != 'ch') {
        			$dublikat = explode(',', $dublikat);
        			$posts->whereIn('id', $dublikat)->update(['check_trash' => 1, 'check_flag' => 0]);
        		} else {
        			$dublikaty->check_trash = 1;
              $dublikaty->check_flag = 0;
              $dublikaty->save();
        		}
            $message = 'Запись (id '.$request->name.') удалена в "Корзину"';
        	}
        	if ($request->checked == 'false') {
        		if (!empty($dublikat) AND $dublikat != 'ch') {
        			$dublikat = explode(',', $dublikat);
        			$posts->whereIn('id', $dublikat)->update(['check_trash' => 0]);
        		} else {
              $dublikaty->check_trash = 0;
              $dublikaty->save();
        		}
            $message = 'Запись (id '.$request->name.') восстановлена';
        	}
      }

      if ($check_name == 'flag') {
          $posts = new StreamData ();
          $post = $posts->find($request->name);
          if ($request->checked == 'true') {
            $message = 'Запись (id '.$request->name.') сохранена в папке "Избранное"';
            $flag = 1;
          }
          else {
            $message = 'Запись (id '.$request->name.') удалена из "Избранного"';
            $flag = 0;
          }
          $post->check_flag = $flag;
          $post->check_trash = 0;
          $post->save();
      }

      if ($check_name == 'cut') {
        if (empty(session('vkid'))) return response()->json(array('success' => false));
          $projects = new Projects;
          if ($request->checked == 'true') {
            $message = 'Длинные посты будут спрятаны под ссылку "Развернуть". Эффект будет после <a class="cursor-pointer text-link" onclick="location.reload();">перезагрузки</a> страницы';
            $cut = 1;
          }
          else {
            $message = 'Посты любой длины будут отображаться полностью. Эффект будет после <a class="cursor-pointer text-link" onclick="location.reload();">перезагрузки</a> страницы';
            $cut = 0;
          }
          $projects->where('vkid', session('vkid'))->where('project_name', $request->name)->update(['cut' => $cut]);
      }

      if ($check_name == 'user-link') {
          $posts = new StreamData ();
          $post = $posts->find($request->name);
          if (empty($request->value)) $request->value = '';
          $post->user_links = $request->value;
          $post->save();
          $message = 'Запись (id '.$request->name.') перемещена в папку <b>'.$request->value.'</b>';
          if ($request->value == '') $message = 'Запись (id '.$request->name.') перемещена в общий поток';
      }

      return response()->json(array('success' => $message));
    }
}
