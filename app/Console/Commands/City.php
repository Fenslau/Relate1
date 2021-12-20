<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \VK\Client\VKApiClient;
use App\Models\Stream\Russia;
use App\Models\Stream\Countries;

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
        $alpha2 = 'WF,TC,SH,NF,BV,JP,JM,GS,KR,ZA,ET,EE,ER,GQ,EC,LK,SE,CH,CL,CZ,ME,TD,CF,HR,TF,PF,GF,FR,FK,FI,PH,FJ,FM,FO,UY,UA,UZ,UG,TR,TM,TN,TV,TT,TO,TK,TG,TZ,TW,TH,TJ,SU,SL,SR,SD,SO,SB,SI,SK,SY,SG,CS,RS,PM,LC,KN,VC,SN,SC,MP,SJ,SZ,SA,ST,SM,WS,SV,US,RO,RW,RU,RE,CG,PR,PT,PL,PN,PE,PY,PG,PA,PS,PW,PK,HM,CK,CX,OM,AE,NO,NZ,NC,NU,NI,NL,AN,NG,NE,NP,NR,NA,MM,MS,MN,MC,MD,MZ,MX,MH,MQ,MA,MT,MV,ML,MY,MW,MK,MO,YT,MG,MR,MU,LU,LI,LT,LY,LB,LR,LS,LV,LA,KW,CU,CI,CR,KM,CO,CC,CN,KI,KG,CY,KE,QA,CA,CM,KH,KY,KZ,CV,KP,YE,IT,ES,IS,IE,IR,IQ,JO,ID,IN,IL,ZW,EH,ZM,EG,EU,DO,DM,DJ,CD,DK,GU,GE,GR,GL,GD,HK,HN,GI,DE,GW,GN,GT,GP,GH,GM,GY,HT,GA,VN,TL,VE,HU,GB,VA,VU,BT,BI,BF,BN,VG,IO,BR,BW,BA,BO,BG,BM,BJ,BE,BY,BZ,BH,BB,BD,BS,AF,AW,AM,AR,AG,AQ,AD,AO,AI,AS,VI,UM,DZ,AL,AX,AZ,AT,AU';

        $params = array(
          'need_all'   	=> 1,
          'count'    		=> 1000,
          'code'        => $alpha2,
          'lang'   		  => 'ru',
          'v' 			    => '5.131'
        );
        $countries = $vk->database()->getCountries(env('ACCESS_TOKEN'), $params);
        $alpha2 = explode(',', $alpha2);
        $i = 0;
        foreach ($countries['items'] as &$countrie) {
          if (!empty($countrie['title'])) $countrie['alpha2'] = $alpha2[$i];
          else unset($countries['items'][$i]);
          $i++;
        }

        Countries::upsert($countries['items'], ['id'], ['id', 'title', 'alpha2']);
        $this->line('База стран и их кодов обновлена');
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
              elseif ($city['title'] == 'Санкт-Петербург') $data['region'] = 'Санкт-Петербург город';
              else $data['region'] = 'неизвестно';
            $data_1000[] = $data;
          }
          if (!empty($data_1000)) $cities->upsert($data_1000, ['city_id'], ['city_id', 'title', 'region']);

          $offset += 1000;
          //echo $offset."\n";
        } while (count($result['items']));
        $this->line('База городов и регионов обновлена');
    }
}
