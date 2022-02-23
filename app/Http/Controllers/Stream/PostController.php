<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\MyRules;
use \App\MyClasses\GetPostInfo;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\Russia;
use App\Models\Stream\Authors;
use App\Models\Stream\OldPosts;
use \App\MyClasses\num;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function main($project_name, Request $request) {
      if (!$request->ajax() AND (empty(session('vkid')) OR empty($project_name) OR !isset($request))) return redirect()->route('stream');
      if ($request->ajax() AND empty(session('vkid'))) return response()->json(array('success' => true, 'html'=>'<p class="alert alert-danger">Необходимо авторизоваться заново, так как ваша сессия истекла</p>'));
      if(session('demo') AND $project_name != 'Demo') return redirect()->route('post', 'Demo');
      $result['video'] = $result['post'] = $items = $info = array();
      $items = $this->getPost(session('vkid'), $project_name, $request);

      $info['project_name'] = $project_name;
      if (!empty($request->rule)) $info['rule'] = $request->rule;
      elseif (!empty($request->user_link)) $info['rule'] = $request->user_link;
      elseif (!empty($request->flag)) $info['rule'] = 'Избранное';
      elseif (!empty($request->trash)) $info['rule'] = 'Корзина';
      if (!empty($request->rule) AND in_array($request->rule, MyRules::getOldRules($project_name))) $info['old_rule'] = TRUE; else $info['old_rule'] = FALSE;
      $post_control = new GetPostInfo();
    if ($request->apply_filter == 'Показать записи' OR empty($request->apply_filter)) {
        if ($items->total() > 0) $info['found'] = 'Всего нашлось <b>'.num::declension ($items->total(), array('</b> запись', '</b> записи', '</b> записей'));

        $items = $this->dublikatUnset($items);
    } else {
        if (count($items) > 0) $info['found'] = 'Всего нашлось <b>'.num::declension (count($items), array('</b> автор', '</b> автора', '</b> авторов'));
    }
      $dublikat_render = 0;
      $result['items'] = $post_control->authorName($items);
      $projects = new Projects();
      $stream = new StreamData();

      $rules = $projects->select("*", DB::raw("CONCAT (vkid, '', rule) AS rule_tag"))->where('project_name', $project_name)->whereNotNull('rule')->get()->toArray();
      if (array_sum(array_column($rules, 'count_stream_records')) < 10000) {
        $dates = $stream->select(DB::raw("max(action_time) AS maxdate, min(action_time) AS mindate"))->whereIn('user', array_column($rules, 'rule_tag'))->where('check_trash', 0)->first()->toArray();
        $countries = $stream->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(DB::raw('country, COUNT(*) as cnt'))->whereIn('user', array_column($rules, 'rule_tag'))->orderBy('cnt', 'desc')->groupBy('country')->pluck('country')->toArray();
        $countries = array_diff($countries, array('', NULL));

        $cities = $stream->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(DB::raw('city, city_id, COUNT(*) as cnt'))->whereIn('user', array_column($rules, 'rule_tag'))->whereNotNull('city')->orderBy('cnt', 'desc')->groupBy('city_id')->pluck('city', 'city_id')->toArray();

        if (!empty($dates['mindate'])) $mindate = date('Y-m-d', $dates['mindate']); else $mindate = date("Y-m-d", date('U'));
        if (!empty($dates['maxdate'])) $maxdate = date('Y-m-d', $dates['maxdate']); else $maxdate = date("Y-m-d", date('U'));
      } else {
        $mindate = date('Y-m-d', min(array_column($rules, 'mindate')));
        $maxdate = date('Y-m-d', max(array_column($rules, 'maxdate')));
        $countries = explode(',', implode(',', (array_column($rules, 'countries'))));
        $cities = explode('* ', implode('', (array_column($rules, 'cities'))));
        $cities = array_unique($cities);
        $cities1 = array();
        foreach ($cities as $city) {
          $tmp = explode('\\', $city);
          if (isset($tmp[1]) AND is_numeric($tmp[0])) $cities1[$tmp[0]]=$tmp[1];
        }
        $cities = $cities1;
      }
      if ($request->ajax()) {
        $returnHTML = view('inc.posts', ['dublikat_render' => $dublikat_render, 'request' => $request, 'cut' => MyRules::getCut($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $result['items'], 'cities' => $cities])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      }

      $old_rules = Projects::select(DB::raw("CONCAT (vkid, '', rule) AS rule_tag"))->where('vkid', session('vkid'))->where('project_name', $project_name)->whereNull('old')->whereNotNull('rule')->pluck('rule_tag')->toArray();
      $old_posts = OldPosts::whereIn('user', $old_rules)->get()->toArray();
      foreach ($old_posts as $old_post) {
          if (!empty($old_post['retry'])) $info['old_post'][$old_post['user']]['retry'] = date('H:i d.m', $old_post['retry']);
          else $info['old_post'][$old_post['user']]['retry'] = NULL;
          if (!empty($old_post['last_date'])) $info['old_post'][$old_post['user']]['last_date'] = date('H:i d.m', $old_post['last_date']);
          else $info['old_post'][$old_post['user']]['last_date'] = NULL;
          if (!empty($old_post['last_date'])) $info['old_post'][$old_post['user']]['progress'] = round(100*(($old_post['start_date']-$old_post['last_date'])/($old_post['start_date']-$old_post['end_date'])), 0);
          else $info['old_post'][$old_post['user']]['progress'] = 0;
      }

      if (!empty(OldPosts::whereIn('user', $old_rules)->first())) $info['get_old'] = TRUE;

      return view('streaming.main', ['dublikat_render' => $dublikat_render, 'request' => $request, 'cut' => MyRules::getCut($project_name), 'projects' => MyRules::getProjects(), 'rules' => MyRules::getRules($project_name), 'old_rules' => MyRules::getOldRules($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $result['items'], 'video' => $result['video'], 'post' => $result['post'], 'countries' => $countries, 'cities' => $cities, 'mindate' => $mindate, 'maxdate' => $maxdate]);
    }

    public function getPost($vkid, $project_name, $request, $offset=NULL, $get_where=NULL) {

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
      if (empty($request->apply_filter) OR $request->apply_filter == 'Показать записи') {
        $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.author_id as author_id','stream_data.id as id']);
      } else $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['stream_data.author_id', 'name', 'country', 'city', 'members_count', 'sex', 'age', DB::raw("COUNT(stream_data.author_id) AS cnt"), 'stream_data.author_id as author_id']);

      $actual_rules = MyRules::getRules($project_name, $vkid);
      $old_rules = MyRules::getOldRules($project_name, $vkid);

      $all_rules = array_merge(array_column($actual_rules, 'rule'), $old_rules);
      $all_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $all_rules);

      $actual_rules = array_column($actual_rules, 'rule');
      $actual_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $actual_rules);

      $old_rules = array_column($old_rules, 'rule');
      $old_rules = array_map(function($n) use($vkid){return ($vkid.$n);}, $old_rules);

      if (empty($request->rule)) $posts->whereIn('user', $all_rules);
      else $posts->where('user', $vkid.$request->rule);

      if (!empty($request->followers_from) AND is_numeric($request->followers_from)) $posts->where('members_count', '>=', $request->followers_from);
      if (!empty($request->followers_to) AND is_numeric($request->followers_to)) $posts->where('members_count', '<=', $request->followers_to);

      if (!empty($request->calendar_from)) $posts->where('action_time', '>=', strtotime($request->calendar_from));
      if (!empty($request->calendar_to)) $posts->where('action_time', '<=', strtotime($request->calendar_to)+60*60*24);

    	if (!empty($request->city)) {
          $city = $request->city;
      		if (!empty($request->in_region)) {
            $russia = new Russia();
      			$region = $russia->whereIn('city_id', $request->city)->pluck('region');
      			if(count($region) > 0) $city = $russia->where('region', $region)->pluck('city_id');
      		}
      $posts->whereIn('authors.city_id', $city);
      }

      if (!empty($request->not_city)) {
          $not_city = $request->not_city;
      		if (!empty($request->in_region_not)) {
            $russia = new Russia();
      			$region = $russia->whereIn('city_id', $request->not_city)->pluck('region');
      			if(count($region) > 0) $not_city = $russia->where('region', $region)->pluck('city_id');
      		}
          $posts->where(function ($query) use($not_city) {
            $query->whereNotIn('authors.city_id', $not_city)
            ->orWhereNull('authors.city_id');
          });
      }

      if (!empty($request->country)) $posts->whereIn('authors.country', $request->country);

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

      if(!empty($request->author_id)) {
        $posts->where('stream_data.author_id', $request->author_id);
      }      

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

      if (empty($request->trash)) $posts->where('check_trash', 0);

      if (!empty($get_where)) return $posts;

      if (empty($request->apply_filter) OR $request->apply_filter == 'Показать записи') {
        if ($offset === NULL) {
          $items = $posts->orderBy('action_time', 'desc')->paginate(100)->withQueryString();
        } else $items = $posts->orderBy('action_time', 'desc')->take(1000)->skip($offset)->get()->toArray();
      } else {
        if ($offset === NULL) {
          $items = $posts->groupBy('stream_data.author_id')->orderBy('cnt', 'desc')->take(10000)->get()->toArray();
        } else $items = $posts->groupBy('stream_data.author_id')->orderBy('cnt', 'desc')->take(1000)->skip($offset)->get()->toArray();
      }
      return $items;
    }

    public function getAjax ($project_name, Request $request) {

      $dublikat_render=0;
      $vkid = $request->vkid;
      $mode = $request->mode;
      $author_id = $request->author_id;

      $request = (object)unserialize($request->query_string);
      $request->author_id = $author_id;
      if ($mode == 'all') unset($request->type);
      elseif ($mode == 'post') $request->type = ['post', 'share'];
      else $request->type = [$mode];
      $request->apply_filter='';

      $items = $this->getPost($vkid, $project_name, $request);

      $items = $this->dublikatUnset($items);

      $post_control = new GetPostInfo();
      $result['items'] = $post_control->authorName($items);
      $info['project_name'] = $project_name;
      if ($result['items']->total() > 0) $info['found'] = '<h5>Активность автора '.Authors::where('author_id', $author_id)->first()->name.':</h5> Всего нашлось <b>'.num::declension ($result['items']->total(), array('</b> запись', '</b> записи', '</b> записей'));

      $returnHTML = view('inc.posts', ['dublikat_render' => $dublikat_render, 'request' => $request, 'cut' => MyRules::getCut($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $result['items']])->render();
      return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }

    public function ajaxVideoLikes (Request $request) {
      $items = $result['post'] = $result['video'] = array();
      if (empty(session('vkid'))) return response()->json( array('success' => false));

      $id = unserialize($request->id);
      $post_id = unserialize($request->post_id);
      $author_id = unserialize($request->author_id);
      $event_type = unserialize($request->event_type);
      $event_url = unserialize($request->event_url);
      $video_player = unserialize($request->video_player);

      for ($i=0; $i < 100; $i++) {
        if(!empty($id[$i])) $items[$i]['id'] = $id[$i]; else $items[$i]['id'] = '';
        if(!empty($post_id[$i])) $items[$i]['post_id'] = $post_id[$i]; else $items[$i]['post_id'] = '';
        if(!empty($author_id[$i])) $items[$i]['author_id'] = $author_id[$i]; else $items[$i]['author_id'] = '';
        if(!empty($event_type[$i])) $items[$i]['event_type'] = $event_type[$i]; else $items[$i]['event_type'] = '';
        if(!empty($event_url[$i])) $items[$i]['event_url'] = $event_url[$i]; else $items[$i]['event_url'] = '';
        if(!empty($video_player[$i])) $items[$i]['video_player'] = $video_player[$i]; else $items[$i]['video_player'] = '';
        $items[$i]['data'] = '';
      }

      $post_control = new GetPostInfo();
      $result = $post_control->vkGet($items);

      if ($result) {
        $result['post'] = json_encode($result['post']);
        $result['video'] = json_encode($result['video']);
        return response()->json(array('success' => 'yes', 'post' => $result['post'], 'video' => $result['video']) );
      } else return response()->json(array('success' => false));
    }

    public function dublikatUnset($items) {
      $for_count = count($items);
      $unset = array();
      for ($i=0; $i<$for_count; $i++) {
        if (in_array($i, $unset)) continue;
        if (isset($items[$i]) AND !empty($items[$i]['dublikat']) AND $items[$i]['dublikat'] != 'ch') {
          $dublikat = array_diff(explode(',', $items[$i]['dublikat']), [$items[$i]['id']]);
          foreach ($dublikat as $dub) {
            $unset[] = (array_search($dub, array_column($items->items(), 'id')));
          }
        }
      }
      foreach ($unset as $del) if (!empty($del)) unset($items[$del]);
      return ($items);
    }
}
