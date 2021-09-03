<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Top;
use App\Models\VkGroups;
use \VK\Client\VKApiClient;

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

      $top1000 = $top->findOrFail(1);
      $i_count=1;
      if(empty($top1000->current_group)) $top1000->current_group = 0;
      for ($j=$top1000->current_group; $j<413408; $j++) {
        $group_ids=$i_count;
        for ($i=($i_count+1); $i<=($i_count+499); $i++) {
          $group_ids .= ','.$i;
        }
        $i_count=500*($j+1);

        $vk = new VKApiClient();
        $access_token = env('ACCESS_TOKEN');
        $group_get = $vk->groups()->getById($access_token, array(
            'group_ids'		 => $group_ids,
            'fields'    	 => 'status,description,public_date_label,start_date,finish_date,contacts,site,verified,wall,city,market,members_count',
            'access_token' => env('access_token'),
            'lang'   		   => 'ru',
            'v' 			     => '5.95'
        ));

        for ($i=0; $i<=499; $i++) {
        $data=array();
        if (!isset($group_get[$i]['deactivated'])) {


          if (isset($group_get[$i]['members_count']) AND $group_get[$i]['members_count'] > 99) {
              $vk_group = New VkGroups();

              $data['group_id'] = $group_get[$i]['id'];
              $data['name'] = strip_tags($group_get[$i]['name']);
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
              $data['members_count'] = $group_get[$i]['members_count'];
              $data['type'] = $group_get[$i]['type'];

              $data['wall'] = $group_get[$i]['wall'];
              if (isset($group_get[$i]['site'])) $data['site'] = mb_substr($group_get[$i]['site'], 0, 128); else $data['site']='';

              $data['verified'] = $group_get[$i]['verified'];
              if (isset($group_get[$i]['market'])) $data['market'] = $group_get[$i]['market']['enabled'];

              $data['is_closed'] = $group_get[$i]['is_closed'];

              if (isset($group_get[$i]['public_date_label'])) $data['public_date_label'] = mb_substr($group_get[$i]['start_date'], 0, 32);

              if ($group_get[$i]['type'] == 'event' AND isset($group_get[$i]['start_date'])) $data['start_date'] = mb_substr($group_get[$i]['start_date'], 0, 32);

              if ($group_get[$i]['type'] == 'event' AND isset($group_get[$i]['finish_date'])) $data['finish_date'] = mb_substr($group_get[$i]['finish_date'], 0, 32);

              $vk_group->updateOrCreate(['group_id' => $group_get[$i]['id']], $data);
            }
          }
        }
      $top1000->current_group=$j;
      $top1000->save();
      }
      $top1000->current_group=0;
      $top1000->save();
    }
}
