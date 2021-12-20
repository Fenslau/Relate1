<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\StreamKey;
use App\Models\Stream\OldPosts;
use \VK\Client\VKApiClient;
use \App\MyClasses\VKUser;
use Illuminate\Support\Facades\DB;
use App\Models\Oplata;
use \App\MyClasses\StreamText;

class OldPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:post';

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
        $olds = new OldPosts();
        $vk = new VKApiClient();
        $streamkeys = Streamkey::find(1);
        $stream = new StreamData();
        $demands = $olds->all()->toArray();
        $ignore = Projects::whereNotNull('ignore_authors')->get()->toArray();
        foreach ($demands as $demand) {
          $user = new VKUser($demand['vkid']);
        	if ($demand['retry'] > date('U')) continue;
        	$arrContextOptions=array(
        		    "ssl"				 =>  array(
                "verify_peer"		     =>true,
                "verify_peer_name"   =>true,
            ));
        	$olds->where('user', $demand['user'])->update(['retry' => NULL]);
        	$rules = json_decode(file_get_contents("https://$streamkeys->endpoint/rules?key=$streamkeys->streamkey", false, stream_context_create($arrContextOptions)), true);
        	if (empty($rules)) die;
        	$rule_val = '';
        	if ($rules['code'] == 200 AND !empty($rules['rules']))
        		foreach ($rules['rules'] as $rule)
        			if ($rule['tag'] == $demand['user']) $rule_val = $rule['value'];

        	$end_time = $stream->select(DB::raw("min(action_time) AS mindate"))->where('user', $demand['user'])->first()->mindate;
        	if (empty($end_time)) $end_time = date('U');
        	$end_time--;
        	$start_from = '';

        do {
        retry:	$params = array(
        				'start_time' 	=> $demand['end_date'],
        				'end_time' 		=> $end_time,
        				'count' 		  => 200,
        				'start_from'	=> $start_from,
        				'q' 			    => $rule_val,
        				'v' 			    => '5.122'
        			);
        try	{
          $posts = $vk->newsfeed()->search($demand['token'], $params);

        } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
            $olds->where('user', $demand['user'])->update(['retry' => (date('U')+60*60*2)]);
            $this->line('Авторизация');
            die; break;
        }
        catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
            $this->line('Лимит короткий');
            sleep (1); goto retry; break;
        }
        catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
            $olds->where('user', $demand['user'])->update(['retry' => (date('U')+60*60*25)]);
            $this->line('Лимит длинный');
            die; break;
        }

        	if (!empty($posts['items'])) foreach ($posts['items'] as $post) {

        		$post_id = $post['id'];
        		$usertag = $demand['user'];
        		$text = $data = $post['text'];
        		$event_type = $post['post_type'];
        		$action_time = $post['date'];
        		$author_id = $post['from_id'];
        			$event_url = '';
        			if ($event_type == 'post') $event_url = 'https://vk.com/wall'.$post['owner_id'].'_'.$post['id'];
        			if ($event_type == 'reply') $event_url = 'https://vk.com/wall' .$post['owner_id'].'_'.$post['post_id'].'?reply='.@$post['parents_stack'][0];

        		foreach ($ignore as $ignore_project) {
        			if (strpos($ignore_project['ignore_authors'], (string)$author_id) !== FALSE) {
        				$rules = Projects::where('vkid', $ignore_project['vkid'])->where('project_name', $ignore_project['project_name'])->whereNotNull('rule')->pluck('rule')->toArray();
        				if (in_array(str_replace($ignore_project['vkid'], '', $demand['user']), $rules)) {
        					$this->line('Заигнорен автор: '.$author_id.' с постом: '.$event_url
        					.' проект: '.$ignore_project['project_name'].' правило: '.str_replace($ignore_project['vkid'], '', $demand['user']));
        					goto cycle;
        				}
        			}
        		}

        $text=StreamText::text($post['text'], $usertag);
        if ($text === FALSE) goto cycle;
        $data = $text['text'];
        $check_trash = $text['trash'];
        $user_links = $text['user_links'];

        		$video_player =	$photo = $linkr = $audio = $doc = $note = '';
        		if (isset($post['attachments'])) foreach ($post['attachments'] as $att) {

        			switch($att['type']) {
          			case 'note': $note = $att['note']['text'];
          			case 'link': $linkr = $linkr.$att['link']['url'].','; break;
          			case 'doc': $doc = $doc.$att['doc']['url'].','; break;
          			case 'photo': foreach ($att['photo']['sizes'] as $photosize) if ($photosize['type'] == 'q') $photo = $photo.$photosize['url'].','; break;
          			case 'video': $video_player = $video_player.$att['video']['owner_id'].'_'.$att['video']['id'].'_'.$att['video']['access_key'].','; break;
          			case 'audio': $audio = $audio.$att['audio']['artist'].' — '.$att['audio']['title'].'9GZVNyidgk';
        			}
        		}

        		if (!empty($post['geo']['place']['country'])) $geo_place_country = $post['geo']['place']['country']; else $geo_place_country = '';
        		if (!empty($post['geo']['place']['city'])) $geo_place_city = $post['geo']['place']['city']; else $geo_place_city = '';
        		if (!empty($post['geo']['place']['icon'])) $geo_place_icon = $post['geo']['place']['icon']; else $geo_place_icon = '';
        		if (!empty($post['geo']['place']['title'])) $geo_place_title = $post['geo']['place']['title']; else $geo_place_title = '';

            $streamdata = new StreamData();
            $streamdata->post_id = $post_id;
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
            $streamdata->platform = '';

          	$streamdata->save();
        cycle:
        	}

        		if (!empty($posts['next_from'])) $start_from = $posts['next_from'];	else $start_from = '';
        		if (isset($posts['items'])) {
              $user->old_post_fact += count($posts['items']);
              $user->save();
            }
        		if (isset($posts['items']) AND count($posts['items']) <= 2 OR $user->old_post_fact > $user->old_post_limit) {
        			$olds->where('user', $demand['user'])->delete();
        			$this->line(date('d.m.Y H:i').' Собраны прошлые посты для: '.$demand['user']);
        			die;
            }

        } while (!empty($start_from));
        $olds->where('user', $demand['user'])->update(['last_date' => $action_time]);
      }
    }
}
