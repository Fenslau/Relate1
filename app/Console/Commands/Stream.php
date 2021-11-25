<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \WebSocket\Client;
use \App\MyClasses\Cartesian;
use \App\Models\Stream\StreamKey;
use \App\Models\Stream\StreamData;
use \App\Models\Stream\Projects;
use Illuminate\Support\Facades\DB;

class Stream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Stream:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//  $keyword = 'rtt';
//  $arr[1] = 'Rt??|fff??|rtt??';
//
// echo in_array($keyword, explode('|', str_replace('?', '', strtolower($arr[1]))));
// die;
        $counter = 1;
        main_cycle:

        $streamkey = new StreamKey();
        $info = $streamkey->find(1);
        		$endpoint = $info->endpoint;
        		$key = $info->streamkey;
      	$option = array (
        		'timeout' => 160
        );
        $url = "wss://$endpoint/stream?key=$key";

        $client = new Client($url, $option);
        //	$client->setLogger(new EchoLog());

        try {
        cycle:
          $projects = new Projects();
        	$ignore = $projects->whereNotNull('ignore_authors')->get()->toArray();
        	$info=$client->receive();
        	$info=json_decode ($info, true);

        	if (!empty($info['error']))	print_r ($info);
        	if ($info['code'] == 100) {

        //	file_put_contents('stream.str', serialize($info), FILE_APPEND | LOCK_EX);
        	foreach ($info['event']['tags'] as $tag) {
        		$usertag = $tag;
        		foreach ($ignore as $ignore_project) {
        			if (strpos($ignore_project['ignore_authors'], (string)$info['event']['author']['id']) !== FALSE) {
        				$rules = $projects->where('vkid', $ignore_project['vkid'])->where('project_name', $ignore_project['project_name'])->whereNotNull('rule')->pluck('rule')->toArray();
        				if (in_array(str_replace($ignore_project['vkid'], '', $tag), $rules)) {
        					echo 'Заигнорен автор: '.$info['event']['author']['id'].' с постом: '.$info['event']['event_url']
        					.'проект: '.$ignore_project['project_name'].' правило: '.str_replace($ignore_project['vkid'], '', $tag)."\n";
        					goto cycle;
        				}
        			}
        		}
        		$text = $data = $info['event']['text'];


        $check_trash = 0;
        $user_links = '';
        $array_of_keywords=array();
        $vk_rule = $projects->where(DB::raw('concat(vkid, "", rule)'), $usertag)->first()->toArray();
        if (!empty($vk_rule['mode1'])) $array_of_keywords = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode1']))), array('', NULL, 0));
        if (!empty($vk_rule['mode2'])) {
        	$array_of_keywords = array_merge($array_of_keywords, array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode2']))), array('', NULL, 0)));
        }
        if (!empty($vk_rule['mode3'])) $array_of_keywords = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode3']))), array('', NULL, 0));
        $new_array_of_keywords_ = array();
        $count_of_keywords = count($array_of_keywords);
        $tmp_arr=$array_of_keywords;

        //print_r ($array_of_keywords);
        foreach ($array_of_keywords as $array_of_keyword) {
        $new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
        foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
        }
        $array_of_keywords = $new_array_of_keywords_;

        file_put_contents ('public/temp/stream.txt', $text, LOCK_EX);
        shell_exec('cd public/temp/ && '.env('MYSTEM').' stream.txt streamoutput.txt -nc');
        $value=explode("\n", file_get_contents('public/temp/streamoutput.txt'));


        $text='';
        $search = ['\r', '\n', '\xAB', '\xBB', '\xA0', '\xB7'];
        $replace = ['', '', '«', '»', ' ', '-'];
        $word_number=0;
        $close=$position=array();

        foreach ($value as $word) {
        		$arr=preg_split('/\{|\}(, *)?/', $word, -1);
        		if (isset($arr[0])) {
        			$arr[0] = str_replace($search, $replace, $arr[0]);
        			if (strpos($arr[0], '\_') !== FALSE) $arr[0] = str_replace('\_', '_', $arr[0]);
        				else $arr[0] = str_replace('_', ' ', $arr[0]);
        			$arr[0] = json_decode('"'.$arr[0].'"');

              foreach ($array_of_keywords as $keyword_)
        			foreach (explode('|', str_replace('?', '', mb_strtolower($keyword_))) as $keyword) {
        				if (!empty($arr[1]) AND !empty($keyword) AND in_array($keyword, explode('|', str_replace('?', '', mb_strtolower($arr[1]))))
        					AND (strpos($arr[0], '<mark>') === FALSE) ) {
        					$arr[0]='<mark>'.$arr[0].'</mark>';
        					$position[$keyword][] = ($word_number);
        				}
              }
        			$text .= $arr[0];
        		}

        	if (isset($arr[1])) {
        		$word_number++;
        		$lemma = str_replace('?', '', $arr[1]);
        		if ($lemma == 'br' OR
        		$lemma == 'href' OR
        		$lemma == '⠀' OR
        		$lemma == 'blank' OR
        		$lemma == 'www' OR
        		$lemma == 'target' OR
        		$lemma == 'rel' OR
        		$lemma == 'nofollow' OR
        		$lemma == 'quot') $word_number -= 1;
        		if ($lemma == 'http' OR $lemma == 'https') $word_number -= 2;
        	} else {
        		$matches=0;
        		preg_match_all('/\d+\D*\d*\s/m', html_entity_decode($arr[0]), $matches);
        		if (!empty($matches[0])) {
        			$word_number += 1;

        		}
        	}
        }
        $cartesian = new Cartesian();
        if (!empty($vk_rule['mode1']) AND empty($vk_rule['mode2'])) $close = $cartesian->PosNClose($position, $vk_rule['words1']);
        if (!empty($vk_rule['mode1']) AND !empty($vk_rule['mode2'])) {
        $mode2_all = $close_flag = array();
        	$array_of_keywords_1 = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode1']))), array('', NULL, 0));
        	$new_array_of_keywords_ = array();
        	foreach ($array_of_keywords_1 as $array_of_keyword) {
        	$new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
        	foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
        	}
        	$array_of_keywords_1 = $new_array_of_keywords_;
        	$position_1 = $position_2 = array();
        	foreach ($position as $key=>$word)
        		if (in_array($key, $new_array_of_keywords_)) $position_1[$key] = $word;
        		else $position_2[$key] = $word;

        	$close['word1'] = $cartesian->PosNClose($position_1, $vk_rule['words1']);
        	$close['word2'] = $cartesian->Pos2Close($position_2, $vk_rule['words2']);

        	if (!empty($close['word1']) AND !empty($close['word2'])) {
        		foreach ($close['word1'] as $group_of_mode2) {
        			foreach ($close['word2'] as $helpwords_of_mode2) {
        				$mode2_all = array();
        				$mode2_all[]=$group_of_mode2;
        				$mode2_all[]=$helpwords_of_mode2;
        				$close_flag[] = $cartesian->Pos2Close($mode2_all, $vk_rule['words2']);
        			}
        		}
        		if (!empty($close_flag)) {
        			$close = array();
        			foreach ($close_flag as $cl_flag)
        				if (is_array($cl_flag)) $close = array_merge($close, $cl_flag);
        			if (is_array($close)) $close = array_unique($close, SORT_REGULAR);
        			if (empty($close)) $close = FALSE;
        		}
        		else $close = FALSE;
        	} else $close = FALSE;
        	if ($close !== FALSE) {
        		$pairs = '';

        		preg_match_all('/[^\+ ]\S+\s?\+\s?\S+[^\+ ]/', $vk_rule['mode2_edit'], $pairs, PREG_SET_ORDER, 0);

        		if (!empty($pairs)) {
        			foreach ($pairs as $pair) {
        				$position_pair=array();
        				$array_of_keywords_1 = array_diff(explode("\n", str_replace('?', '', mb_strtolower(shell_exec('cd public/temp/ && echo "'.addslashes($pair[0]).'" | '.env('MYSTEM').' -ln')))), array('', NULL, 0));
        				$new_array_of_keywords_ = array();
        				foreach ($array_of_keywords_1 as $array_of_keyword) {
        					$new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
        					foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
        				}
        				$array_of_keywords_1 = $new_array_of_keywords_;
        				foreach ($position as $key=>$word)
        					if (in_array($key, $new_array_of_keywords_)) $position_pair[$key] = $word;

        				$pair_close = $cartesian->PosNClose($position_pair, 1);
        				if ($pair_close === FALSE) $close=FALSE;
        			}
        		}
        	}
        }
        if (!empty($vk_rule['mode3'])) $close = $cartesian->Pos2Close($position, $vk_rule['words3']);

        $diff = $tmp_array = $pos_array=array();
        foreach ($position as $pkey=>$pvalue) {
        	$pos_array[]=$pkey;
        }
        foreach ($tmp_arr as $pkey=>$pvalue) {
        	$difference = FALSE;
        	foreach (explode('|', str_replace('?', '', mb_strtolower($pvalue))) as $pvalue_word) {
        		if (in_array ($pvalue_word, $pos_array)) $difference = TRUE;
        	}
        	if ($difference === FALSE) $diff[] = $pvalue;
        }

        if ($close === FALSE) $check_trash=1;
        if (!empty($diff)) {
        	$user_links = 'Доп.посты';
        	$check_trash=0;
        }
        $data = $text;



        		$event_type = $info['event']['event_type'];
        		$event_url = $info['event']['event_url'];
        		if (isset($info['event']['event_id']['post_id'])) $post_id = $info['event']['event_id']['post_id']; else $post_id = 0;
        		if (isset($info['event']['event_id']['comment_id'])) $post_id = $info['event']['event_id']['comment_id'];
        		if (isset($info['event']['event_id']['topic_id'])) $post_id = $info['event']['event_id']['topic_id'];
        		if (isset($info['event']['event_id']['shared_post_id'])) $shared_post_id = $info['event']['event_id']['shared_post_id']; else $shared_post_id = 0;
        		$action_time = date("U");
        		$doc = $note = $audio = $video_player = $linkr = $photo = '';
        		if (isset($info['event']['attachments'])) foreach ($info['event']['attachments'] as $att) {

        			switch($att['type']) {
        			case 'note': $note = $att['note']['text']; break;
        			case 'link': $linkr = $linkr.$att['link']['url'].','; break;
        			case 'doc': $doc = $doc.$att['doc']['url'].','; break;
        			case 'photo': $photo = $photo.$att['photo']['photo_604'].','; break;
        			case 'video': $video_player = $video_player.$att['video']['owner_id'].'_'.$att['video']['id'].'_'.$att['video']['access_key'].','; break;
        			case 'audio': $audio = $audio.$att['audio']['artist'].' — '.$att['audio']['title'].'9GZVNyidgk';
        			}
        		}
        		if (!empty($info['event']['action'])) $action = $info['event']['action']; else $action = '';
        		if (isset($info['event']['geo']['place']['country'])) $geo_place_country = $info['event']['geo']['place']['country']; else $geo_place_country = '';
        		if (isset($info['event']['geo']['place']['city'])) $geo_place_city = $info['event']['geo']['place']['city']; else $geo_place_city = '';
        		if (isset($info['event']['geo']['place']['icon'])) $geo_place_icon = $info['event']['geo']['place']['icon']; else $geo_place_icon = '';
        		if (isset($info['event']['geo']['place']['title'])) $geo_place_title = $info['event']['geo']['place']['title']; else $geo_place_title = '';
        		if (isset($info['event']['geo']['type'])) $geo_type = $info['event']['geo']['type']; else $geo_type = '';

        		if (isset($info['event']['author']['shared_post_author_id'])) $shared_post_author_id = $info['event']['author']['shared_post_author_id'];
        			else $shared_post_author_id = 0;

        		$author_id = $info['event']['author']['id'];
        		$platform = '';
        		switch(@$info['event']['author']['platform']) {
        		case '1': $platform = 'мобильная версия сайта'; break;
        		case '2': $platform = 'iPhone'; break;
        		case '3': $platform = 'iPad'; break;
        		case '4': $platform = 'Android'; break;
        		case '5': $platform = 'Windows Phone'; break;
        		case '6': $platform = 'Windows 8'; break;
        		case '7': $platform = 'полная версия сайта'; break;
        		case '8': $platform = 'сторонние приложения'; break;
        		default: if (isset($info['event']['author']['platform'])) $platform = $info['event']['author']['platform']; break;
        		}


          $streamdata = new StreamData();
          $streamdata->post_id = $post_id;
          $streamdata->shared_post_id = $shared_post_id;
          $streamdata->user = $usertag;
          $streamdata->data = $data;
          $streamdata->check_trash = $check_trash;
          $streamdata->user_links = $user_links;
          $streamdata->event_type = $event_type;
          $streamdata->event_url = $event_url;
          $streamdata->action_time = $action_time;
          $streamdata->video_player = $video_player;
          $streamdata->photo = $photo;
          $streamdata->link = $linkr;
          $streamdata->audio = $audio;
          $streamdata->doc = $doc;
          $streamdata->note = $note;
          $streamdata->geo_place_country = $geo_place_country;
          $streamdata->geo_place_city = $geo_place_city;
          $streamdata->geo_place_icon = $geo_place_icon;
          $streamdata->geo_place_title = $geo_place_title;
          $streamdata->author_id = $author_id;
          $streamdata->shared_post_author_id = $shared_post_author_id;
          $streamdata->platform = $platform;

        	$streamdata->save();
        	}} else {
        		print_r ($info);
        		goto cycle;
        	}
        	echo $counter.' '.strlen($data)."\n";
        	$counter++;
        goto cycle;
        } catch (\WebSocket\ConnectionException $e) {
            echo "\n".$e;
        		goto main_cycle;
        }
    }
}
