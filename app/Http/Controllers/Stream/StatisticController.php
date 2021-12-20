<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\GetProgress;
use \App\MyClasses\num;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\Authors;
use App\Http\Controllers\Stream\PostController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatisticController extends Controller
{
    public function get($project_name, Request $request) {
      if (empty($project_name)) return redirect()->route('stream');
      define("region_quantity", "30");
      define("mos", "Москва город");
      define("spb", "Санкт-Петербург город");
      $vkid = session('vkid');

      $progress = new GetProgress($vkid, 'stream', 'Собирается статистика по авторам-группам, мужчинам и женщинам за последние 12 дней', 6, 1);
      $period_stat = $this->period($project_name, $request, 'no-ajax');

      $progress->step();

      $request = (object)unserialize($request->query_string);
      $posts = new PostController;

      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);

      $progress->message('Собирается статистика по типу и полу авторов, количеству подписчиков');
      $stat = $items->select(DB::raw("
      sum(stream_data.author_id < 0) as groups,
    	sum(sex = 1) as female,
    	sum(sex = 2) as male,
    	sum(if(stream_data.author_id > 0, members_count = 0, 0)) as follow_0,
    	sum(if(stream_data.author_id > 0, members_count BETWEEN 1 AND 50, 0)) as follow_500,
    	sum(if(stream_data.author_id > 0, members_count BETWEEN 51 AND 100, 0)) as follow_501_1000,
    	sum(if(stream_data.author_id > 0, members_count BETWEEN 101 AND 500, 0)) as follow_1001_5000,
    	sum(if(stream_data.author_id > 0, members_count BETWEEN 501 AND 1000, 0)) as follow_5001_10000,
    	sum(if(stream_data.author_id > 0, members_count BETWEEN 1001 AND 3000, 0)) as follow_10001_30000,
    	sum(if(stream_data.author_id > 0, members_count > 3000, 0)) as follow_30001_,

    	sum(if(stream_data.author_id < 0, members_count BETWEEN 1 AND 500, 0)) as group_follow_500,
    	sum(if(stream_data.author_id < 0, members_count BETWEEN 501 AND 1000, 0)) as group_follow_501_1000,
    	sum(if(stream_data.author_id < 0, members_count BETWEEN 1001 AND 5000, 0)) as group_follow_1001_5000,
    	sum(if(stream_data.author_id < 0, members_count BETWEEN 5001 AND 10000, 0)) as group_follow_5001_10000,
    	sum(if(stream_data.author_id < 0, members_count BETWEEN 10001 AND 30000, 0)) as group_follow_10001_30000,
    	sum(if(stream_data.author_id < 0, members_count > 30000, 0)) as group_follow_30001_
      "))->first()->toArray();

      $progress->step();


      $progress->message('Собирается статистика по всем возрастам');
      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
      $full_age = $items->select(DB::raw("age, COUNT(stream_data.author_id) AS cnt"))->where('age', '>=', 0)->groupBy('age')->orderBy('age')->get()->toArray();
      $ages['_0']=$ages['_18']=$ages['18_24']=$ages['25_34']=$ages['35_44']=$ages['45_54']=$ages['55_']=0;
      foreach ($full_age as $age) {
        if ($age['age'] == 0) $ages['_0'] += $age['cnt'];
      	if ($age['age'] > 0 AND $age['age'] < 18) $ages['_18'] += $age['cnt'];
      	if ($age['age'] > 17 AND $age['age'] < 25) $ages['18_24'] += $age['cnt'];
      	if ($age['age'] > 24 AND $age['age'] < 35) $ages['25_34'] += $age['cnt'];
      	if ($age['age'] > 34 AND $age['age'] < 45) $ages['35_44'] += $age['cnt'];
      	if ($age['age'] > 44 AND $age['age'] < 55) $ages['45_54'] += $age['cnt'];
      	if ($age['age'] > 54) $ages['55_'] += $age['cnt'];
      }

      $progress->step();


      $progress->message('Собирается статистика распределения авторов по городам');
      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
      $region_score = $items->leftJoin('russias', 'authors.city_id', '=', 'russias.city_id')->select(DB::raw("region, (Count(authors.city_id)) as region_score"))->whereNotNull('region')->groupBy('region')->orderBy('region_score', 'desc')->get()->toArray();

      $region_count = count($region_score);
    	$other_regions = 0;
    	if ($region_count > region_quantity) for ($i=region_quantity; $i<$region_count; $i++) $other_regions += $region_score[$i]["region_score"];

    	$sum_parsed = array();
    	for ($i=0; $i<min($region_count, region_quantity); $i++) {
    		if (!empty($region_score[$i]["region"])) $sum_parsed[] = 'SUM(region = "'.$region_score[$i]["region"].'") AS "'.$region_score[$i]["region"].'"';
    	}
      if (count($sum_parsed)) {
    		$sum_russia = implode (', ', $sum_parsed);
    	} else 	$sum_russia = 'NULL';

      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
      $country_score = $items->leftJoin('russias', 'authors.city_id', '=', 'russias.city_id')->select(DB::raw("country, (count(authors.country)) as country_score"))->where('country', '!=', 'Россия')->where('country', '!=', '')->groupBy('country')->orderBy('country_score', 'desc')->get()->toArray();

      $country_count = count($country_score);
    	$other_countries = 0;
      if ($country_count > region_quantity) for ($i=region_quantity; $i<$country_count; $i++) $other_countries += $country_score[$i]["country_score"];

      $sum_parsed = array();
    	for ($i=0; $i<min($country_count, region_quantity); $i++) {
    		if (!empty($country_score[$i]["country"])) $sum_parsed[] = 'SUM(country = "'.$country_score[$i]["country"].'") AS "'.$country_score[$i]["country"].'"';
    	}
    	if (count($sum_parsed)) {
    		$sum_foreign = implode (', ', $sum_parsed);
    	} else 	$sum_foreign = 'NULL';

      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
    	if (!empty($sum_russia) OR !empty($sum_foreign)) {
    		$city_score = $items->leftJoin('russias', 'authors.city_id', '=', 'russias.city_id')->select('city', DB::raw("$sum_russia, $sum_foreign"))->groupBy('city')->get()->toArray();
    	}
    	else $city_score = array();
      $country_list = array_column($country_score, 'country');

      $foreign_new_score = $new_score = array();
      foreach ($city_score as $city_title) {
      	foreach ($city_title as $key=>$value) if (is_numeric($value) AND $value > 0) {
      		$region = $key;
      		$kolvo = $value;
          if (in_array($region, $country_list)) $foreign_new_score[$region][$city_title['city']] = $kolvo;
          if (!in_array($region, $country_list)) $new_score[$region][$city_title['city']] = $kolvo;
      	}
      }

      $forein_city_score = array();
      	for ($i=0; $i<min($country_count, region_quantity); $i++) {
      		if (!empty($foreign_new_score[$country_score[$i]['country']])) $city_score_tmp = $foreign_new_score[$country_score[$i]['country']]; else $city_score_tmp = array();
      			arsort($city_score_tmp);
      			$j=0;
      			foreach ($city_score_tmp as $key=>$value) {
      				$forein_city_score[$i][$j]['city']=$key;
      				$forein_city_score[$i][$j]['forein_city_score']=$value;
      				$j++;
      			}
      			$forein_city_score[$i]['forein_city_count'] = count($city_score_tmp);
      	}
      $russian_city_score = array();
      	for ($i=0; $i<min($region_count, region_quantity); $i++) {
      		if (!empty($new_score[$region_score[$i]['region']])) $city_score_tmp = $new_score[$region_score[$i]['region']]; else $city_score_tmp = array();
      		arsort($city_score_tmp);
      		$j=0;
      		foreach ($city_score_tmp as $key=>$value) {
      			$russian_city_score[$i][$j]['city']=$key;
      			$russian_city_score[$i][$j]['city_score']=$value;
      			$j++;
      		}
      		$russian_city_score[$i]['city_count'] = count($city_score_tmp);
      	}
      $city_score = $russian_city_score;

      $progress->step();


      $progress->message('Собирается статистика распределения авторов по странам мира');
      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
      $country_score1 = $items->leftJoin('countries', 'authors.country', '=', 'countries.title')->select('authors.country', 'alpha2', DB::raw("count(authors.country) as country_score"))->whereNotNull('alpha2')->groupBy('authors.country')->orderBy('country_score', 'desc')->pluck('country_score', 'alpha2')->toArray();

      $progress->step();


      $progress->message('Считываются данные для облака слов и тегов');
      $weight_cloud = $weight_tag = array();
      if (Schema::hasTable('clouds_'.$vkid.$project_name) AND empty($request->rule) AND empty($request->stat) AND empty($request->flag) AND empty($request->trash) AND empty($request->user_link))
      $weight_cloud = DB::table('clouds_'.$vkid.$project_name)->where('weight', '>', 0)->orderBy('weight', 'desc')->take(50)->get()->toArray();
      if (Schema::hasTable('tags_'.$vkid.$project_name) AND empty($request->rule) AND empty($request->stat) AND empty($request->flag) AND empty($request->trash) AND empty($request->user_link))
      $weight_tag = DB::table('tags_'.$vkid.$project_name)->where('weight', '>', 0)->orderBy('weight', 'desc')->take(50)->get()->toArray();

      $progress->step();


      $progress->message('Собирается статистика по активным авторам');
      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
    	$author_score = $items->select('stream_data.author_id', 'name', 'city', 'members_count', DB::raw("count(stream_data.author_id) AS author_score"))->groupBy('stream_data.author_id')->having('author_score', '>', 2)->orderBy('author_score', 'desc')->take(1000)->get()->toArray();
    	$active_author = count($author_score);

      $items = $posts->getPost($vkid, $project_name, $request, NULL, 1);
      $author_walls_all = $items->select('stream_data.author_id', 'event_type', 'event_url')->whereIn('stream_data.author_id', array_column($author_score, 'author_id'))->get()->toArray();

      $new_author_walls_all = array();
      foreach ($author_walls_all as $author_walls) {
        $new_author_walls_all[$author_walls['author_id']][] = array('event_type'=>$author_walls['event_type'], 'event_url'=>$author_walls['event_url']);
      }
      	for ($i=0; $i < $active_author; $i++) {
      		if ($author_score[$i]['members_count'] == -1) $author_score[$i]['members_count'] = '';
      		if ($author_score[$i]['author_score'] < 3) continue;
      			$author_walls = $new_author_walls_all[$author_score[$i]['author_id']];
      			$author_walls_topics = $author_walls_comments = $author_walls_posts = array();
      				foreach ($author_walls as $author_wall) {
      					if (($author_wall['event_type'] == 'post' OR $author_wall['event_type'] == 'share') AND preg_match("/wall([^_]*)/", $author_wall['event_url'], $matches)) $author_walls_posts[] = $matches[1];
      					if ($author_wall['event_type'] == 'topic_post' AND preg_match("/topic([^_]*)/", $author_wall['event_url'], $matches)) $author_walls_topics[] = $matches[1];
      					if (($author_wall['event_type'] == 'comment' OR $author_wall['event_type'] == 'reply') AND preg_match("/wall([^_]*)/", $author_wall['event_url'], $matches)) $author_walls_comments[] = $matches[1];
      				}

      			$author_score[$i]['author_id_post'] = count($author_walls_posts);
      			$author_score[$i]['author_id_post_unique'] = count(array_unique($author_walls_posts));

      			$author_score[$i]['author_id_topic'] = count($author_walls_topics);
      			$author_score[$i]['author_id_topic_unique'] = count(array_unique($author_walls_topics));

      			$author_score[$i]['author_id_comment'] = count($author_walls_comments);
      			$author_score[$i]['author_id_comment_unique'] = count(array_unique($author_walls_comments));
          }

      $progress->step();


      $info['project_name'] = $project_name;
      if (!empty($request->rule)) $info['rule'] = $request->rule;
      elseif (!empty($request->user_link)) $info['rule'] = $request->user_link;
      elseif (!empty($request->flag)) $info['rule'] = 'Избранное';
      elseif (!empty($request->trash)) $info['rule'] = 'Корзина';

      $ignore_list = array_diff(explode(',', implode(',', Projects::where('vkid', $vkid)->where('project_name', $project_name)->whereNotNull('ignore_authors')->pluck('ignore_authors')->toArray())), ['']);
      $ignore_list = Authors::whereIn('author_id', $ignore_list)->pluck('name', 'author_id');


        $returnHTML = view('streaming.statistic', ['request' => $request, 'info' => $info, 'stat' => $stat, 'ages' => $ages, 'full_age' => $full_age, 'region_count' => $region_count, 'mos' => mos, 'spb' => spb, 'region_quantity' => region_quantity, 'region_score' => $region_score, 'other_regions' => $other_regions, 'city_score' => $city_score, 'country_score' => $country_score, 'forein_city_score' => $forein_city_score, 'country_count' => $country_count, 'country_score1' => $country_score1, 'weight_cloud' => $weight_cloud, 'weight_tag' => $weight_tag, 'author_scores' => $author_score, 'ignore_list' => $ignore_list, 'period_stat' => $period_stat])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }

    public function delIgnore ($project_name, Request $request) {
      $ignore_list = array_diff(explode(',', implode(',', Projects::where('vkid', $request->vkid)->where('project_name', $project_name)->whereNotNull('ignore_authors')->pluck('ignore_authors')->toArray())), ['', $request->id]);
      $name = Authors::where('author_id', $request->id)->first()->name;

      Projects::where('vkid', $request->vkid)->where('project_name', $project_name)->whereNull('rule')->update(['ignore_authors' => implode(',', $ignore_list)]);

      return response()->json(array('success' => 'Автор <b>'.$name.'</b> удалён из игнор-листа. Список авторов в игнор-листе обновится после <a class="cursor-pointer text-link" onclick="location.reload();">перезагрузки</a> страницы'));
    }


    public function addIgnore ($project_name, Request $request) {
      $ignore_list = array_diff(explode(',', implode(',', Projects::where('vkid', $request->vkid)->where('project_name', $project_name)->whereNotNull('ignore_authors')->pluck('ignore_authors')->toArray())), ['']);
      $author_id = array_diff(explode(',', $request->author_id), ['']);

      $name = Authors::whereIn('author_id', $author_id)->pluck('name')->toArray();

      $rules = Projects::select(DB::raw("CONCAT (vkid, '', rule) AS rule_tag"))->where('vkid', session('vkid'))->where('project_name', $project_name)->whereNotNull('rule')->pluck('rule_tag')->toArray();
      StreamData::whereIn('user', $rules)->whereIn('author_id', $author_id)->update(['check_trash' => 1]);
      if ($request->ignore) {
        $ignore_list = array_unique(array_merge($ignore_list, $author_id));
        Projects::where('vkid', $request->vkid)->where('project_name', $project_name)->whereNull('rule')->update(['ignore_authors' => implode(',', $ignore_list)]);
        return response()->json(array('success' => 'Автор'.(count($name)>1?"ы":"").' <b>'.implode(', ', $name).'</b> добавлен'.(count($name)>1?"ы":"").' в игнор-лист. Список авторов в игнор-листе обновится после <a class="cursor-pointer text-link" onclick="location.reload();">перезагрузки</a> страницы'));
      }
      return response()->json(array('success' => 'Автор'.(count($name)>1?"ы":"").' <b>'.implode(', ', $name).'</b> удален'.(count($name)>1?"ы":"").' из базы данных вместе с '.(count($name)>1?"их":"его").' записями. Но посты от '.(count($name)>1?"них":"него").' всё ещё будут приходить в дальнейшем'));
    }


    public function period($project_name, Request $request, $ajax=NULL) {
      $period_quantity = 12;
      if (!empty($request->mode)) $mode = $request->mode;
      else $mode = 'day';
      $request_query = (object)unserialize($request->query_string);
      $posts = new PostController;
      $items = $posts->getPost(session('vkid'), $project_name, $request_query, NULL, 1);

      if ($mode == 'day') {
          $timestamp = date('U')-(60*60*24*($period_quantity+1));
          $day = $items->select(DB::raw("
          	date_format(from_unixtime(action_time), '%d.%m, %a' ) as date,
          	sum(sex=1) AS females,
          	sum(sex=2) AS males,
          	sum(stream_data.author_id < 0) AS groups,
          	SUM(stream_data.author_id is not null) as _all"))
            ->where('action_time', '>', $timestamp)->groupBy(DB::raw("day(from_unixtime(action_time))"))->orderBy(DB::raw("DATE(from_unixtime(action_time))"), 'desc')->take($period_quantity)->get()->toArray();

          $authors = array();
          	for ($i=0; $i < count($day); $i++) {
          		$authors[$i]['all']['values'] = $day[$i]['_all'];
          		$authors[$i]['all']['dates'] = $day[$i]['date'];
          		$authors[$i]['females']['values'] = $day[$i]['females'];
          		$authors[$i]['males']['values'] = $day[$i]['males'];
          		$authors[$i]['groups']['values'] = $day[$i]['groups'];
          	}

          $authors = array_reverse ($authors);
          $male = $female = $group = array();
          foreach ($authors as $item)	{
          	if (!empty($item['males']['values'])) $male[]=$item['males']['values']; else $male[]=0;
          	if (!empty($item['females']['values'])) $female[]=$item['females']['values']; else $female[]=0;
          	if (!empty($item['groups']['values'])) $group[]=$item['groups']['values']; else $group[]=0;
          	if (!empty($item['all']['values'])) $alls[]=$item['all']['values']; else $alls[]=0;
          	$date[]="'".$item['all']['dates']."'";
          }
          $male = @implode (', ', @$male);
          $female = @implode (', ', @$female);
          $group = @implode (', ', @$group);
          $alls = @implode (', ', @$alls);
          $date = @implode (', ', @$date);
          $last = num::declension(count($day), array('день', 'дня', 'дней'));
      }

      if ($mode == 'week') {
          $timestamp = date('U')-(60*60*24*150);
          $week = $items->select(DB::raw("
          	DATE_FORMAT(date_sub(from_unixtime(action_time), interval (weekday(from_unixtime(action_time))) day),'%d.%m') as wbeg,
          	DATE_FORMAT(date_add(from_unixtime(action_time), interval (6-weekday(from_unixtime(action_time))) day),'%d.%m') as wend,
          	sum(sex=1) AS females,
          	sum(sex=2) AS males,
          	sum(stream_data.author_id < 0) AS groups,
          	SUM(stream_data.author_id is not null) as _all"))
          	->where('action_time', '>', $timestamp)->groupBy(DB::raw("week(from_unixtime(action_time),1)"))->orderBy(db::raw("DATE(from_unixtime(action_time))"), 'desc')->take($period_quantity)->get()->toArray();

          $authors = array();
          	for ($i=0; $i < count($week); $i++) {
          		$authors[$i]['all']['values'] = $week[$i]['_all'];
          		$authors[$i]['all']['dates'] = $week[$i]['wbeg'].' - '.$week[$i]['wend'];
          		$authors[$i]['females']['values'] = $week[$i]['females'];
          		$authors[$i]['males']['values'] = $week[$i]['males'];
          		$authors[$i]['groups']['values'] = $week[$i]['groups'];
          	}
          $authors = array_reverse ($authors);
          $male = $female = $group = array();
          foreach ($authors as $item)	{
          	if (!empty($item['males']['values'])) $male[]=$item['males']['values']; else $male[]=0;
          	if (!empty($item['females']['values'])) $female[]=$item['females']['values']; else $female[]=0;
          	if (!empty($item['groups']['values'])) $group[]=$item['groups']['values']; else $group[]=0;
          	if (!empty($item['all']['values'])) $alls[]=$item['all']['values']; else $alls[]=0;
          	$date[]="'".$item['all']['dates']."'";
          }
          $male = @implode (', ', @$male);
          $female = @implode (', ', @$female);
          $group = @implode (', ', @$group);
          $alls = @implode (', ', @$alls);
          $date = @implode (', ', @$date);
          $last = num::declension(count($week), array('неделя', 'недели', 'недель'));
      }

      if ($mode == 'month') {
          $timestamp = date('U')-(60*60*24*31*$period_quantity);
          $month = $items->select(DB::raw("
          	date_format( from_unixtime(action_time), '%b.%y' ) as month1,
          	sum(sex=1) AS females,
          	sum(sex=2) AS males,
          	sum(stream_data.author_id < 0) AS groups,
          	SUM(stream_data.author_id is not null) as _all"))
          	->where('action_time', '>', $timestamp)->groupBy('month1')->orderBy(DB::raw("DATE(from_unixtime(action_time))"), 'desc')->take($period_quantity)->get()->toArray();

          $authors = array();
          	for ($i=0; $i < count($month); $i++) {
          		$authors[$i]['all']['values'] = $month[$i]['_all'];
          		$authors[$i]['all']['dates'] = $month[$i]['month1'];
          		$authors[$i]['females']['values'] = $month[$i]['females'];
          		$authors[$i]['males']['values'] = $month[$i]['males'];
          		$authors[$i]['groups']['values'] = $month[$i]['groups'];
          	}
          $authors = array_reverse ($authors);
          $male = $female = $group = array();
          foreach ($authors as $item)	{
          	if (!empty($item['males']['values'])) $male[]=$item['males']['values']; else $male[]=0;
          	if (!empty($item['females']['values'])) $female[]=$item['females']['values']; else $female[]=0;
          	if (!empty($item['groups']['values'])) $group[]=$item['groups']['values']; else $group[]=0;
          	if (!empty($item['all']['values'])) $alls[]=$item['all']['values']; else $alls[]=0;
          	$date[]="'".$item['all']['dates']."'";
          }
          $male = @implode (', ', @$male);
          $female = @implode (', ', @$female);
          $group = @implode (', ', @$group);
          $alls = @implode (', ', @$alls);
          $date = @implode (', ', @$date);
          $last = num::declension(count($month), array('месяц', 'месяца', 'месяцев'));
      }


      $period_stat = array();
      $period_stat['last'] = $last;
      $period_stat['date'] = $date;
      $period_stat['male'] = $male;
      $period_stat['female'] = $female;
      $period_stat['group'] = $group;
      $period_stat['alls'] = $alls;

      if(empty($ajax)) {
        $returnHTML = view('streaming.period-stat', ['period_stat' => $period_stat])->render();
        return response()->json( array('success' => true, 'html'=>$returnHTML) );
      } else return $period_stat;
    }
}
