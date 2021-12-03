<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use App\Models\Stream\Russia;
use App\Models\Stream\Authors;
use App\Models\Stream\StreamData;
use \App\MyClasses\GetAge;
use Illuminate\Support\Facades\DB;

class AuthorGet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Authors:Get';

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
        $authors = new Authors();
        $posts = new StreamData();
        $vk = new VKApiClient();

        $limit_users = 500;
        do {
          $user_ids = $posts->whereNotIn('author_id', DB::table('authors')->where('author_id', '>', 0)->pluck('author_id'))->where('author_id', '>', 0)->distinct()->pluck('author_id')->take($limit_users)->toArray();
          $count_of_users = count($user_ids);
          $user_ids = implode(',', $user_ids);

          if (strlen($user_ids)) {
        		$params = array(
        				'user_ids'  => $user_ids,
        				'fields'    => 'sex,bdate,city,country,followers_count',
        				'lang'   		=> 'ru',
        				'v'         => '5.103'
        		);
        		$profiles = $vk->users()->get(env('ACCESS_TOKEN'), $params);
            $data_500 = $data = array();
          	if (!empty($profiles)) foreach ($profiles as $user) {
              $data['author_id'] = $data['name'] = $data['country'] = $data['city'] = $data['city_id'] = $data['members_count'] = $data['sex'] = $data['age'] = NULL;
          		$age = NULL;
          		$data['author_id'] = $user['id'];
          		$data['name'] = $user['first_name'].' '.$user['last_name'];
          		if (!empty($user['country']['title'])) $data['country'] = $user['country']['title'];
              if (!empty($user['city']['title'])) $data['city'] = $user['city']['title'];
              if (!empty($user['city']['id'])) $data['city_id'] = $user['city']['id'];

          		if (isset($user['followers_count'])) $data['members_count'] = $user['followers_count']; else $data['members_count'] = -1;
          		if (isset($user['sex'])) $data['sex'] = $user['sex']; else $data['sex'] = 0;
          		if (isset($user['bdate']) AND count(explode(".", $user['bdate'])) == 3)	{
          			$age = explode(".", $user['bdate']);
          			$data['age'] = GetAge::age($age[2], $age[1], $age[0]);
          		} else $data['age'] = 0;

              $data_500[] = $data;
            }

            if (!empty($data_500)) $authors->upsert($data_500, ['author_id'], ['author_id', 'name', 'country', 'city', 'city_id', 'members_count', 'sex', 'age']);
        	}
        }	while ($count_of_users == $limit_users);

        $limit_groups = 500;
        do {
          $group_ids = $posts->whereNotIn('author_id', DB::table('authors')->where('author_id', '<', 0)->pluck('author_id'))->where('author_id', '<', 0)->distinct()->pluck('author_id')->take($limit_groups)->toArray();
          foreach ($group_ids as &$group) $group = abs($group);

          $count_of_groups = count($group_ids);
          $group_ids = implode(',', $group_ids);

          	if (strlen($group_ids)) {
          			$params = array(
          				'group_ids' => $group_ids,
          				'fields'    => 'city,country,members_count',
          				'lang'   		=> 'ru',
          				'v'         => '5.103'
          			);

          		$profiles = $vk->groups()->getById(env('ACCESS_TOKEN'), $params);
              $data_500 = $data = array();
          	  if (!empty($profiles)) foreach ($profiles as $group) {
                $data['author_id'] = $data['name'] = $data['country'] = $data['city'] = $data['city_id'] = $data['members_count'] = $data['sex'] = $data['age'] = NULL;
            		$data['author_id'] = -$group['id'];
            		$data['name'] = $group['name'];
                if (!empty($group['country']['title'])) $data['country'] = $group['country']['title'];
                if (!empty($group['city']['title'])) $data['city'] = $group['city']['title'];
                if (!empty($group['city']['id'])) $data['city_id'] = $group['city']['id'];
            		if (isset($group['members_count'])) $data['members_count'] = $group['members_count']; else $data['members_count'] = -1;

                $data_500[] = $data;
              }

              if (!empty($data_500)) $authors->upsert($data_500, ['author_id'], ['author_id', 'name', 'country', 'city', 'city_id', 'members_count']);
          	}
        }	while ($count_of_groups == $limit_groups);
    }
}
