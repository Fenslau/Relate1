<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \App\MyClasses\MyRules;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\Russia;
use \App\MyClasses\num;
use Carbon\Carbon;

class PostController extends Controller
{
    public function main($project_name, Request $request) {
      if(empty($project_name)) return redirect()->route('stream');
      $video = $post = $posts = $videos = $items = $info = array();
      $items = $this->getPost($project_name, $request);

      $info['project_name'] = $project_name;
      if (!empty($request->rule)) $info['rule'] = $request->rule;
      elseif (!empty($request->user_link)) $info['rule'] = $request->user_link;
      elseif (!empty($request->flag)) $info['rule'] = 'Избранное';
      elseif (!empty($request->trash)) $info['rule'] = 'Корзина';

      if ($items->total() > 0) $info['found'] = 'Всего нашлось <b>'.num::declension ($items->total(), array('</b> запись', '</b> записи', '</b> записей'));

      $vk = new VKApiClient();
      try {
        $post = $video = array();
        $video_ids = implode('', array_diff(array_column($items->items(), 'video_player'), array('', NULL)));
        $params = array(
  				'videos'		      => $video_ids,
  				'v' 			        => '5.107'
  			);
        if(!empty($video_ids)) $videos = $vk->video()->get(session('token'), $params);

        $posts = '';
        foreach ($items as $item) if ($item['event_type'] == 'post' OR $item['event_type'] == 'share')
        $posts .= str_replace('https://vk.com/wall', '', $item['event_url']).',';
        $params = array(
  				'posts'		      => $posts,
  				'v' 			        => '5.107'
  			);
        if(!empty($posts)) $posts = $vk->wall()->getById(session('token'), $params);
      } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
          return back()->with('warning', 'Закончилось действие токена ВК, залогиньтесь заново, чтобы продолжить');
      }

      foreach ($items as &$item) {

        if (!empty($videos['items']) AND !empty($item['video_player'])) {
            $video_players = array_diff(explode(",", $item['video_player']), array('', NULL));
            $i=0;
            foreach ($video_players as $video_player) {
              preg_match_all("'_(.+?)_'", $video_player, $matches);

              $index_of_videos = array_search($matches[1][0], (array_column($videos['items'], 'id')));

              if ($index_of_videos !== FALSE) {
                if (!empty($videos['items'][$index_of_videos]['player'])) $video[$item['id']][$i]['player'] = $videos['items'][$index_of_videos]['player'];
                else $video[$item['id']][$i]['player'] = '';
                if (!empty($videos['items'][$index_of_videos]['title'])) $video[$item['id']][$i]['title'] = $videos['items'][$index_of_videos]['title'];
                else $video[$item['id']][$i]['title'] = '';
                if (!empty($videos['items'][$index_of_videos]['description'])) $video[$item['id']][$i]['description'] = $videos['items'][$index_of_videos]['description'];
                elseif (!empty($videos['items'][$index_of_videos]['content_restricted'])) $video[$item['id']][$i]['description'] = $videos['items'][$index_of_videos]['content_restricted_message'];
                else $video[$item['id']][$i]['description']  = '';
              } else {
                $video[$item['id']][$i]['title'] = '';
                $video[$item['id']][$i]['description'] = 'Это видео можно посмотреть только перейдя по ссылке поста';
                $video[$item['id']][$i]['player'] = '';
              }
              $i++;
            }
        }

        if (!empty($posts)) $count_of_posts = array_keys((array_column($posts, 'id')), $item['post_id']);
				if (!empty($count_of_posts) AND $count_of_posts !== FALSE) {
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
      $countries = $cities = $ages = $dates = array();
      $countries = array(0 => 'Россия');
      $cities = array(0 => 'Москва');

      return view('streaming.main', ['request' => $request, 'cut' => MyRules::getCut($project_name), 'projects' => MyRules::getProjects(), 'rules' => MyRules::getRules($project_name), 'old_rules' => MyRules::getOldRules($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $items, 'video' => $video, 'post' => $post, 'countries' => $countries, 'cities' => $cities, 'dates' => $dates, 'ages' => $ages]);
    }

    public function getPost($project_name, Request $request) {

      $vkid = session('vkid');
      $posts = new StreamData();
      $projects = new Projects();

      if (!empty($request->age)) {
        $age = explode(' - ', $request->age);
        $min_age = $age[0];
        $max_age = $age[1];
      }
      else {
        $min_age = 13;
        $max_age = 118;
      }
      $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id']);

      $actual_rules = MyRules::getRules($project_name);
      $old_rules = MyRules::getOldRules($project_name);

      $all_rules = array_column(array_merge($actual_rules, $old_rules), 'rule');
      $all_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $all_rules);

      $actual_rules = array_column($actual_rules, 'rule');
      $actual_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $actual_rules);

      $old_rules = array_column($old_rules, 'rule');
      $old_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $old_rules);

      if (empty($request->rule)) $posts->whereIn('user', $actual_rules);
      else $posts->where('user', $vkid.$request->rule);

      if(!empty($request->author_id)) {
        $posts->where('author_id', $request->author_id)->where(function ($query) {
            $query->where('dublikat', '!=', 'd:'.$request->author_id)
            ->orWhereIsNull('dublikat');
        });
      }

      if (!empty($request->followers_from) AND is_numeric($request->followers_from)) $posts->where('members_count', '>=', $request->followers_from);
      if (!empty($request->followers_to) AND is_numeric($request->followers_to)) $posts->where('members_count', '<=', $request->followers_to);

      if (!empty($request->calendar_from)) $posts->where('action_time', '>=', strtotime($request->calendar_from));
      if (!empty($request->calendar_to)) $posts->where('action_time', '<=', strtotime($request->calendar_to)+60*60*24);

    	if (!empty($request->city)) {
          $city = $request->city;
      		if (!empty($request->in_region)) {
            $russia = new Russia();
      			$region = $russia->whereIn('title', $request->city)->pluck('region');
      			$city = $russia->where('region', $region)->pluck('title');
      		}
      $posts->whereIn('city', $city);
      }

      if (!empty($request->not_city)) {
          $not_city = $request->not_city;
      		if (!empty($request->in_region_not)) {
            $russia = new Russia();
      			$region = $russia->whereIn('title', $request->not_city)->pluck('region');
      			$not_city = $russia->where('region', $region)->pluck('title');
      		}
      $posts->whereNotIn('city', $not_city);
      }

      if (!empty($request->country)) $posts->whereIn('country', $request->country);

      if (!empty($request->type)) {
        		$posts->where(function ($query) use ($request){
              foreach ($request->type as $rec_type) {
                if ($rec_type == 'comment') $query->orWhere('event_type', 'comment')->orWhere('event_type', 'reply');
                else $query->orWhere('event_type', $rec_type);
              }
            });
      }

      if (!empty($request->man) OR !empty($request->woman) OR !empty($request->group)) {

        $posts->where(function ($query) use ($request) {
            if (!empty($request->man)) $query->orWhere('sex', 2);
            if (!empty($request->woman)) $query->orWhere('sex', 1);
            if (!empty($request->group)) $query->orWhere('stream_data.author_id', '<', 0);
        });
      }

      if ($min_age > 13 OR $max_age < 118)
      $posts->whereBetween('age', [$min_age, $max_age]);


      if (!empty($request->user_link)) {
      	$posts = new StreamData();
      	$posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id'])->whereIn('user', $all_rules)->where('user_links', $request->user_link);
      }

      if (empty($request->trash)) $posts->where('check_trash', 0);

      if (empty($request->stat)) {
      	//if (empty($request->flag)) $posts->where('check_flag', 0);
      	if (empty($request->user_link)) $posts->where('user_links', '');
      }
      else {
        $posts->where('user_links', '!=', 'Доп.посты');
      	if (!empty($request->user_links)) {
      		if ($request->user_links == 'on') $posts->where('user_links', '!=', '');
      		if ($request->user_links == 'off') $posts->where('user_links', '');
          if ($request->user_links == 'all') {}
      	}
      }

      if (!empty($request->trash)) {
      	$posts = new StreamData();
        $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id'])->whereIn('user', $all_rules)->where('check_trash', 1);
      }
      if (!empty($request->flag)) {
        $posts = new StreamData();
        $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id'])->whereIn('user', $all_rules)->where('check_flag', 1);
      }

      if (empty($request->author_id) AND (empty($request->user_link) OR $request->user_link == 'Доп.посты') AND empty($request->flag)) {
      $posts->where(function ($query) {
          $query->where('dublikat', 'NOT LIKE' ,'d%')
          ->orWhereNull('dublikat');
      });
      }
      $items = $posts->orderBy('action_time', 'desc')->paginate(100)->withQueryString()->fragment('begin');

      return $items;
    }
}
