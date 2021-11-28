<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\MyRules;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use \App\MyClasses\num;
use Carbon\Carbon;

class PostController extends Controller
{
    public function main($project_name, Request $request) {
      if(empty($project_name)) return redirect()->route('stream');
      $items = $info = array();
      $items = $this->getPost($project_name, $request);

      $info['project_name'] = $project_name;
      if (!empty($request->rule)) $info['rule'] = $request->rule;
      elseif (!empty($request->user_link)) $info['rule'] = $request->user_link;
      elseif (!empty($request->flag)) $info['rule'] = 'Избранное';
      elseif (!empty($request->trash)) $info['rule'] = 'Корзина';

      if ($items->total() > 0) $info['found'] = 'Всего нашлось <b>'.num::declension ($items->total(), array('</b> запись', '</b> записи', '</b> записей'));

      foreach ($items as &$item) {
        $item['action_time'] = Carbon::createFromFormat('U', $item['action_time'])->format('j M yг. H:i');

        if ($item['author_id'] > 0) $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/id'.$item['author_id'];
        else $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/public'.-$item['author_id'];
        if (!empty($item['name'])) $item['author_id'] .= '" data-toggle="tooltip" title="Автор">'.$item['name'].'</a>';
        else $item['author_id'] .= '">Автор</a>';

        $re = '/\[(.+)\|(.+)\]/U';
  			$subst = '<a rel="nofollow" target="_blank" href="https://vk.com/$1">$2</a>';
  			$item['data'] = preg_replace($re, $subst, $item['data']);

      }
      return view('streaming.main', ['cut' => MyRules::getCut($project_name), 'projects' => MyRules::getProjects(), 'rules' => MyRules::getRules($project_name), 'old_rules' => MyRules::getOldRules($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $items]);
    }

    public function getPost($project_name, Request $request) {

      $vkid = session('vkid');
      $posts = new StreamData();
      $projects = new Projects();
      $posts = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(['*', 'stream_data.id as id']);

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

      if (!empty($request->calendar_from)) $posts->where('action_time', '>=', $request->calendar_from);
      if (!empty($request->calendar_to)) $posts->where('action_time', '<=', $request->calendar_to);

    	if (!empty($request->city)) {
      		if (!empty($request->in_region)) {
            $russia = new Russia();
      			$region = $russia->whereIn('title', $city)->pluck('region');
      			$city = $russia->whereIn('title', $region)->pluck('title');
      		}
      $posts->whereIn('city', $request->city);
      }

      if (!empty($request->not_city)) {
      		if (!empty($request->in_region_not)) {
            $russia = new Russia();
      			$region = $russia->whereIn('title', $city)->pluck('region');
      			$not_city = $russia->whereIn('title', $region)->pluck('title');
      		}
      $posts->whereNotIn('city', $request->not_city);
      }

      if (!empty($request->country)) $posts->whereIn('country', $request->country);

      $type_parsed = array();
      	if (!empty($request->type)) {
      		foreach ($request->type as $rec_type) {
      		if ($rec_type == 'comment') $posts->where(function ($query) {
              $query->where('event_type', 'comment')
              ->orWhere('event_type', 'reply');
          });
      		else $posts->where('event_type', $rec_type);
      		}
      }

      if (!empty($request->man)) $posts-where('sex', 2);
      if (!empty($request->woman)) $posts-where('sex', 1);

      if ((!empty($request->man) OR !empty($request->woman)) AND ($request->min_age > 13 OR $request->max_age < 118))
      $posts->whereBetween('age', [$request->min_age, $request->max_age]);

      if (!empty($request->group)) $posts->where('author_id', '<', 0);

      if (!empty($request->user_link)) {
      	$posts = new StreamData();
      	$posts = $posts->whereIn('user', $all_rules)->where('user_links', $request->user_link);
      }

      if (empty($request->trash)) $posts->where('check_trash', 0);

      if (empty($request->stat)) {
      	//if (empty($request->flag)) $posts->where('check_flag', 0);
      	if (empty($request->user_link)) $posts->where('user_links', '');
      }
      else {
      	if (!empty($request->user_link) AND $request->user_link != 'Доп.посты') $posts->where('user_links', '!=', 'Доп.посты');
      	if (!empty($request->user_links)) {
      		if ($request->user_links == 'on') $posts->where('user_links', '!=', '');
      		if ($request->user_links == 'off') $posts->where('user_links', '');
      	}
      }

      if (!empty($request->trash)) {
      	$posts = new StreamData();
        $posts = $posts->whereIn('user', $all_rules)->where('check_trash', 1);
      }
      if (!empty($request->flag)) {
        $posts = new StreamData();
        $posts = $posts->whereIn('user', $all_rules)->where('check_flag', 1);
      }

      $items = $posts->orderBy('action_time', 'desc')->paginate(2)->withQueryString()->fragment('begin');

      return $items;
    }
}
