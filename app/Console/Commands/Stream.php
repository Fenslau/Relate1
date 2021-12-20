<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \WebSocket\Client;
use \App\MyClasses\StreamText;
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
        		'timeout' => 50
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
$time_start = microtime(true);
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

              $text=StreamText::text($info['event']['text'], $usertag);
              if ($text === FALSE) goto cycle;
              $data = $text['text'];
              $check_trash = $text['trash'];
              $user_links = $text['user_links'];


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
          	}
            $time_end = microtime(true);
            $time = $time_end - $time_start;
          } else {
        		print_r ($info);
        		goto cycle;
        	}
        	echo $counter.' '.round ($time, 3).' сек. '.strlen($data)."\n";
        	$counter++;
        goto cycle;
        } catch (\WebSocket\ConnectionException $e) {
            echo "\n".$e;
        		goto main_cycle;
        }
    }
}
