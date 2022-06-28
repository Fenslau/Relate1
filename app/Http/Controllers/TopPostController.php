<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toppost;
use \App\MyClasses\num;
use Illuminate\Support\Facades\DB;
use \App\MyClasses\GetPostInfo;
use \VK\Client\VKApiClient;
use App\Models\IgnoredGroups;
use App\Models\Top;

class TopPostController extends Controller
{
    public function main (Request $request) {
      $info['project_name'] = 'Toppost2409';
      $info['toppost'] = TRUE;
      if (!empty($request->mode)) $mode = $request->mode;
      else $mode = '6hour';

      $ignored = IgnoredGroups::pluck('group_id')->toArray();
      $posts = new Toppost();
      $post_control = new GetPostInfo();
      $posts = $posts->leftJoin('authors', 'topposts.author_id', '=', 'authors.author_id');
      switch ($mode) {
        case 'new': $items = $posts->select(DB::raw("*, topposts.id AS id, likes/views AS popular"))->where('likes', '>', 100)->where('action_time', '>', date('U')-60*60*13); break;
        case 'hot': $items = $posts->select(DB::raw("*, topposts.id AS id, (likes+comments+reposts)/views/(UNIX_TIMESTAMP(topposts.updated_at)-action_time) AS popular"))->where('likes', '>', 1000)->where('action_time', '>', date('U')-60*60*48); break;
        case 'best': $items = $posts->select(DB::raw("*, topposts.id AS id, (likes+comments+reposts)/views AS popular"))->where('likes', '>', 10000)->where('action_time', '>', date('U')-60*60*24*5); break;

        case '6hour': $items = $posts->select(DB::raw("*, topposts.id AS id, comments AS popular"))->where('action_time', '>', date('U')-60*60*6); break;
        case 'day': $items = $posts->select(DB::raw("*, topposts.id AS id, comments AS popular"))->where('action_time', '>', date('U')-60*60*24); break;
        case 'week': $items = $posts->select(DB::raw("*, topposts.id AS id, comments AS popular"))->where('action_time', '>', date('U')-60*60*24*7); break;
      }
      $items = $items->whereNotIn('topposts.author_id', $ignored)->orderBy('popular', 'desc')->paginate(100)->withQueryString();
      $items = $post_control->authorName($items);
      $time = date('d.m.y H:i', Top::find(1)->pluck('time'));
      if($request->ajax()) {
        $returnHTML = view('inc.posts', ['dublikat_render' => NULL, 'cut' => 1, 'request' => $request, 'info' => $info, 'items' => $items])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }
      return view('toppost', ['time' => $time, 'dublikat_render' => NULL, 'cut' => 1, 'request' => $request, 'info' => $info, 'items' => $items]);
    }

}
