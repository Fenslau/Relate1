<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use App\Models\Stream\Russia;

class City extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'City:database';

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
        $vk = new VKApiClient();
        $cities = new Russia();
        $offset = 0;

        do {
          $params = array(
            'need_all'   	=> 1,
            'count'    		=> 1000,
            'country_id'  => 1,
            'offset'    	=> $offset,
            'lang'   		  => 'ru',
            'v' 			    => '5.103'
          );
      		$result = $vk->database()->getCities(env('ACCESS_TOKEN'), $params);

          $data = $data_1000 = array();
          foreach ($result['items'] as $city) {
            $data['city_id'] = $city['id'];
            if (!empty($city['title'])) $data['title'] = $city['title']; else $data['title'] = '';
            if (!empty($city['region'])) $data['region'] = $city['region'];
              elseif ($city['title'] == 'Москва') $data['region'] = 'Москва город';
              elseif ($city['title'] == 'Москва') $data['region'] = 'Санкт-Петербург город';
              else $data['region'] = 'неизвестно';
            $data_1000[] = $data;
          }
          if (!empty($data_1000)) $cities->upsert($data_1000, ['city_id'], ['city_id', 'title', 'region']);

          $offset += 1000;
          //echo $offset."\n";
        } while (count($result['items']));
        echo 'База городов и регионов обновлена';
    }
}
