<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use \App\MyClasses\num;

class DublikatController extends Controller
{
    public function get(Request $request) {
        $post = $info = array();
        $dublikaty = StreamData::find($request->id);
        $project = Projects::whereRaw("CONCAT_WS('', vkid, rule)=?", $dublikaty->user)->distinct()->get()->toArray();
        $cut = $project[0]['cut'];
        $dublikaty_array = array_diff(explode(', ', $dublikaty->dublikat), array(0, '', NULL));

        $items = StreamData::leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id'])->whereIn('stream_data.id', $dublikaty_array)->get();




        $vk = new VKApiClient();
        $posts = '';
        foreach ($items as $item) if ($item['event_type'] == 'post' OR $item['event_type'] == 'share')
        $posts .= str_replace('https://vk.com/wall', '', $item['event_url']).',';
        $params = array(
  				'posts'		       => $posts,
  				'v' 			       => '5.107'
  			);
        try {
          if(!empty($posts)) $posts = $vk->wall()->getById(session('token'), $params);
        } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
            return response()->json( array('success' => false));
        }




      foreach ($items as &$item) {
        $count_of_posts = array_keys((array_column($posts, 'id')), $item['post_id']);
        if ($count_of_posts !== FALSE) {
            $post_count = ' ';
            foreach ($count_of_posts as $count_of_post) if ($posts[$count_of_post]['from_id'] == $item['author_id'])
              $post_count = $count_of_post;
          if (is_numeric($post_count)) {
            $count_of_posts = $post_count;
            if (!empty($posts[$count_of_posts]['comments']['count']))
              $post[$item['id']]['comments'] = $posts[$count_of_posts]['comments']['count'];
            if (!empty($posts[$count_of_posts]['likes']['count']))
              $post[$item['id']]['likes'] = $posts[$count_of_posts]['likes']['count'];
            if (!empty($posts[$count_of_posts]['reposts']['count']))
              $post[$item['id']]['reposts'] = $posts[$count_of_posts]['reposts']['count'];
            if (!empty($posts[$count_of_posts]['views']['count']))
              $post[$item['id']]['views'] = $posts[$count_of_posts]['views']['count'];
           }
         }

        if ($item['author_id'] > 0) $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/id'.$item['author_id'];
        else $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/public'.-$item['author_id'];
        if (!empty($item['name'])) $item['author_id'] .= '" data-toggle="tooltip" title="Автор">'.$item['name'].'</a>';
        else $item['author_id'] .= '">Автор</a>';

        $re = '/\[(.+)\|(.+)\]/U';
        $subst = '<a rel="nofollow" target="_blank" href="https://vk.com/$1">$2</a>';
        $item['data'] = preg_replace($re, $subst, $item['data']);
      }




        $info['found'] = '<span class="ml-2">Есть <b>'.num::declension (count($items), array('</b> дубликат', '</b> дубликата', '</b> дубликатов')).'</span>';
        if (count($items) == 0) {
          $info['found'] .= '. Возможно они когда-то были, но вы их удалили';
          $dublikaty->dublikat = NULL;
          $dublikaty->save();
        }
        $info['project_name'] = $project[0]['project_name'];
        $dublikat_render = 1;
        $returnHTML = view('inc.posts', ['dublikat_render' => $dublikat_render, 'cut' => $cut, 'info' => $info, 'items' => $items, 'post' => $post])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }
}
