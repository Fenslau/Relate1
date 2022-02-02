<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toppost;
use \App\MyClasses\num;
use Illuminate\Support\Facades\DB;
use \App\MyClasses\GetPostInfo;
use \VK\Client\VKApiClient;

class TopPostController extends Controller
{
    public function main (Request $request) {
      $info['project_name'] = 'Toppost';
      $info['toppost'] = TRUE;
      if (!empty($request->mode)) $mode = $request->mode;
      else $mode = 'new';

      $posts = new Toppost();
      $post_control = new GetPostInfo();
      $posts = $posts->leftJoin('authors', 'topposts.author_id', '=', 'authors.author_id');
      switch ($mode) {
        case 'new': $items = $posts->select(DB::raw("*, topposts.id AS id, likes/views AS popular"))->where('likes', '>', 100)->where('action_time', '>', date('U')-60*60*18)->orderBy('popular', 'desc')->paginate(100)->withQueryString(); break;
        case 'hot': $items = $posts->select(DB::raw("*, topposts.id AS id, (likes+comments+reposts)/(UNIX_TIMESTAMP()-action_time) AS popular"))->where('likes', '>', 1000)->where('action_time', '>', date('U')-60*60*48)->orderBy('popular', 'desc')->paginate(100)->withQueryString(); break;
        case 'best': $items = $posts->select(DB::raw("*, topposts.id AS id, (likes+comments+reposts)/views AS popular"))->where('likes', '>', 10000)->orderBy('popular', 'desc')->paginate(100)->withQueryString(); break;
      }

      $items = $post_control->authorName($items);

      if($request->ajax()) {
        $returnHTML = view('inc.posts', ['dublikat_render' => NULL, 'cut' => 1, 'request' => $request, 'info' => $info, 'items' => $items])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }
      return view('toppost', ['dublikat_render' => NULL, 'cut' => 1, 'request' => $request, 'info' => $info, 'items' => $items]);
    }

}