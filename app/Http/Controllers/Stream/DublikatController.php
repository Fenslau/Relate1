<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\GetPostInfo;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use \App\MyClasses\MyRules;
use \App\MyClasses\num;

class DublikatController extends Controller
{
    public function get(Request $request) {
        $post = $info = array();

        $dublikaty = StreamData::find($request->id);
        $project = Projects::whereRaw("CONCAT_WS('', vkid, rule)=?", $dublikaty->user)->first()->toArray();
        $cut = $project['cut'];
        $dublikaty_array = array_diff(explode(',', $dublikaty->dublikat), [$request->id]);

        $items = StreamData::leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id'])->whereIn('stream_data.id', $dublikaty_array)->paginate(100)->appends(['id' => $request->id])->withQueryString();

        $post_control = new GetPostInfo();

        $result['items'] = $post_control->authorName($items);

        $info['found'] = '<span class="ml-2">Есть <b>'.num::declension ($items->total(), array('</b> дубликат', '</b> дубликата', '</b> дубликатов')).'</span>';
        if (count($items) == 0) {
          $info['found'] .= '. Возможно они когда-то были, но вы их удалили';
          $dublikaty->dublikat = NULL;
          $dublikaty->save();
        }
        $info['project_name'] = $project['project_name'];
        $dublikat_render = 1;
        $returnHTML = view('inc.posts', ['request' => $request, 'dublikat_render' => $dublikat_render, 'cut' => $cut, 'links' => MyRules::getLinks($project['project_name']), 'info' => $info, 'items' => $result['items']])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }
}
