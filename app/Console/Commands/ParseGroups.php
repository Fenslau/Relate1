<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Top;
use App\Models\VkGroups;
use \VK\Client\VKApiClient;
use \VK\Exceptions\VKClientException;

class ParseGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Parse:Groups';

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

      $top = New Top();

      $top1000 = $top->find(1);
      $i_count=1;
      if(empty($top1000->current_group)) $top1000->current_group = 0;
      for ($j=$top1000->current_group; $j<env('GROUP_COUNT'); $j++) {
        $group_ids=$i_count;
        for ($i=($i_count+1); $i<=($i_count+499); $i++) {
          $group_ids .= ','.$i;
        }
        $i_count=500*($j+1);

        $vk = new VKApiClient();
retry:  $access_token = env('ACCESS_TOKEN');
        try {
          $group_get = $vk->groups()->getById($access_token, array(
              'group_ids'		 => $group_ids,
              'fields'    	 => 'status,description,public_date_label,start_date,finish_date,contacts,site,verified,wall,city,market,members_count',
              'lang'   		   => 'ru',
              'v' 			     => '5.95'
          ));
        } catch (\VK\TransportClient\TransportRequestException $exception) {
            echo $exception->getMessage()."\n";
            sleep(60);
            goto retry;
        }
        catch (\VK\Exceptions\VKClientException $exception) {
            echo $exception->getMessage()."\n";
            sleep(60);
            goto retry;
        }
        $data_500 = array();
		    $vk_group = New VkGroups();
        for ($i=0; $i<=499; $i++) {
        $data=array();
        if (!isset($group_get[$i]['deactivated'])) {


          if (isset($group_get[$i]['members_count']) AND $group_get[$i]['members_count'] > 99) {

              $data['group_id'] = $group_get[$i]['id'];
              if (isset($group_get[$i]['name'])) $data['name'] = strip_tags($group_get[$i]['name']); else $data['name'] = '';
              $tags = '';
              $tag_desc=$tag_status=0;
              if (isset($group_get[$i]['description'])) {
                preg_match_all('/#[^\s.*,:;&?#!@][\S][^\s.*,:;&?#!@]+/', $group_get[$i]['description'], $tag_desc);
              }
              if (isset($group_get[$i]['status'])) {
                preg_match_all('/#[^\s.*,:;&?#!@][\S][^\s.*,:;&?#!@]+/', $group_get[$i]['status'], $tag_status);
              }
              if (($tag_desc[0])) $tags .= implode(' ', $tag_desc[0]);
              if (($tag_status[0])) $tags .= implode(' ', $tag_status[0]);
              $data['tags']=$tags;

              if (isset($group_get[$i]['city'])) $data['city'] = mb_substr($group_get[$i]['city']['title'], 0, 64); else $data['city']='';
              $data['contacts'] = '';
              if (isset($group_get[$i]['contacts'])) {
                foreach ($group_get[$i]['contacts'] as $contact) {
                  if (isset($contact['user_id'])) $data['contacts'] .= 'https://vk.com/id' . $contact['user_id'] . ' ';
                  if (isset($contact['desc'])) $data['contacts'] .= $contact['desc'] . ' ';
                  if (isset($contact['phone'])) $data['contacts'] .= $contact['phone'] . ' ';
                  if (isset($contact['mail'])) $data['contacts'] .= $contact['mail'] . ' ';
                  $data['contacts'] .= "\n";
                }
              }
              if (isset($group_get[$i]['members_count'])) $data['members_count'] = $group_get[$i]['members_count']; else $data['members_count'] - '';
              if (isset($group_get[$i]['type'])) $data['type'] = $group_get[$i]['type']; else $data['type'] = 'group';

              $data['wall'] = $group_get[$i]['wall'];
              if (isset($group_get[$i]['site'])) $data['site'] = mb_substr($group_get[$i]['site'], 0, 128); else $data['site']='';

              if (isset($group_get[$i]['verified'])) $data['verified'] = $group_get[$i]['verified']; else $data['verified'] = 0;
              if (isset($group_get[$i]['market'])) $data['market'] = $group_get[$i]['market']['enabled']; else $data['market'] = 0;

              if (isset($group_get[$i]['is_closed'])) $data['is_closed'] = $group_get[$i]['is_closed']; else $data['is_closed'] = 1;

              if (isset($group_get[$i]['public_date_label'])) $data['public_date_label'] = mb_substr($group_get[$i]['start_date'], 0, 32);
			  else $data['public_date_label'] = '';

              if ($group_get[$i]['type'] == 'event' AND isset($group_get[$i]['start_date'])) $data['start_date'] = mb_substr($group_get[$i]['start_date'], 0, 32);
			  else $data['start_date'] = '';

              if ($group_get[$i]['type'] == 'event' AND isset($group_get[$i]['finish_date'])) $data['finish_date'] = mb_substr($group_get[$i]['finish_date'], 0, 32);
			  else $data['finish_date'] = '';

              $data_500[] = $data;
            }
          }
        }
        $vk_group->upsert($data_500, ['group_id'], ['group_id', 'name', 'city', 'members_count', 'type', 'wall', 'site', 'verified', 'market', 'is_closed', 'contacts', 'public_date_label', 'start_date', 'finish_date', 'tags']);
        $top1000->current_group=$j;
        $top1000->save();
      }
      $top1000->current_group=0;
      $top1000->save();
    }
}
