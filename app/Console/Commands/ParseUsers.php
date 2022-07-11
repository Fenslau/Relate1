<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Top;
use App\Models\TopUsers;
use \VK\Client\VKApiClient;

class ParseUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Parse:Users';

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

    public function write($items_) {
      foreach ($items_ as $items) {
        if(isset($items['followers_count']) AND $items['followers_count'] > 1000) {
          $item=array();
          if ($items['is_closed']) $items['is_closed'] = 1; else $items['is_closed'] = 0;

          if(isset($items['first_name'])) $item['first_name'] = $items['first_name'];
          if(isset($items['last_name'])) $item['last_name'] = $items['last_name'];
          if(isset($items['screen_name'])) $item['screen_name'] = $items['screen_name'];
          if(isset($items['about'])) $item['about'] = $items['about'];
          if(isset($items['activities'])) $item['activities'] = $items['activities'];
          if(isset($items['interests'])) $item['interests'] = $items['interests'];
          if(isset($items['sex'])) $item['sex'] = $items['sex'];
          if(isset($items['bdate'])) $item['bdate'] = $items['bdate'];
          if(isset($items['country']['title'])) $item['country'] = $items['country']['title'];
          if(isset($items['city']['title'])) $item['city'] = $items['city']['title'];

          if(isset($items['followers_count'])) $item['followers_count'] = $items['followers_count'];
          if(isset($items['twitter'])) $item['twitter'] = $items['twitter'];
          if(isset($items['livejournal'])) $item['livejournal'] = $items['livejournal'];
          if(isset($items['skype'])) $item['skype'] = $items['skype'];
          if(isset($items['occupation']['name'])) $item['occupation'] = $items['occupation']['name'];
          if(isset($items['relation'])) $item['status'] = $items['relation'];
          if(isset($items['verified'])) $item['verified'] = $items['verified'];
          if(isset($items['is_closed'])) $item['is_closed'] = $items['is_closed'];
          if(isset($items['can_post'])) $item['can_post'] = $items['can_post'];
          if(isset($items['can_see_all_posts'])) $item['can_see_all_posts'] = $items['can_see_all_posts'];
          if(isset($items['can_send_friend_request'])) $item['can_send_friend_request'] = $items['can_send_friend_request'];
          if(isset($items['can_write_private_message'])) $item['can_write_private_message'] = $items['can_write_private_message'];
          if(isset($items['photo_100'])) $item['photo_100'] = $items['photo_100'];
          TopUsers::updateOrCreate([
          'vkid' => $items['id']],
          $item);
        }
      }

    }

    public function Users($access_token, $country, $city) {
      $vk = new VKApiClient();
retry:
      try {
      $users_get = $vk->users()->search($access_token, array(
          'sort'		 => 0,
          'count'    => 1000,
          'fields'   => 'photo_100,screen_name,about,activities,interests,bdate,sex,country,city,connections,followers_count,occupation,relation,verified,can_post,can_see_all_posts,can_send_friend_request,can_write_private_message',
          'country'  => $country,
          'city'   	 => $city,
          'v' 			 => '5.101'
      ));
    } catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
                    //echo $exception->getMessage()."\n";
                    sleep(1);
                    goto retry;
    }
    catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
          echo $exception->getMessage()."\n";
          die;
    }
      return $users_get;
    }

    public function handle()
    {

      $access_token = Top::find(1)->token;
      $country = array("1","5","6","3","49","65","7","4","11","12","13","15","9","2","14","16","17","18");
  		$city = array("Москва", "Санкт-Петербург", "Новосибирск", "Екатеринбург", "Нижний Новгород", "Казань", "Челябинск", "Омск", "Самара", "Ростов-на-Дону", "Уфа", "Красноярск", "Воронеж", "Пермь", "Волгоград", "Краснодар", "Саратов", "Тюмень", "Тольятти", "Ижевск", "Барнаул", "Ульяновск", "Иркутск", "Хабаровск", "Ярославль", "Владивосток", "Махачкала", "Томск", "Оренбург", "Кемерово", "Новокузнецк", "Рязань", "Астрахань", "Набережные", "Пенза", "Киров", "Липецк", "Чебоксары", "Балашиха", "Калининград", "Тула", "Курск", "Севастополь", "Сочи", "Ставрополь", "Улан-Удэ", "Тверь", "Магнитогорск", "Иваново", "Брянск", "Белгород", "Сургут", "Владимир", "Нижний Тагил", "Чита", "Архангельск", "Симферополь", "Калуга", "Смоленск", "Волжский", "Якутск", "Саранск", "Череповец", "Курган", "Вологда", "Орёл", "Владикавказ", "Подольск", "Грозный", "Мурманск", "Тамбов", "Петрозаводск", "Стерлитамак", "Нижневартовск", "Кострома", "Новороссийск", "Йошкар-Ола", "Химки", "Таганрог", "Комсомольск-на-Амуре", "Сыктывкар", "Нижнекамск", "Нальчик", "Шахты", "Дзержинск", "Орск", "Братск", "Благовещенск", "Энгельс", "Ангарск", "Королёв", "Великий", "Старый Оскол", "Мытищи", "Псков", "Люберцы", "Южно-Сахалинск", "Бийск", "Прокопьевск", "Армавир", "Балаково", "Рыбинск", "Абакан", "Северодвинск", "Петропавловск", "Норильск", "Уссурийск", "Волгодонск", "Красногорск", "Сызрань", "Новочеркасск", "Каменск", "Златоуст", "Электросталь", "Альметьевск", "Салават", "Миасс", "Керчь", "Копейск", "Находка", "Пятигорск", "Хасавюрт", "Рубцовск", "Березники", "Коломна", "Майкоп", "Одинцово", "Ковров", "Домодедово", "Нефтекамск", "Кисловодск", "Нефтеюганск", "Батайск", "Новочебоксарск", "Серпухов", "Щёлково", "Дербент", "Новомосковск", "Черкесск", "Первоуральск", "Раменское", "Назрань", "Каспийск", "Обнинск", "Орехово-Зуево", "Кызыл", "Новый Уренгой", "Невинномысск", "Димитровград", "Октябрьский", "Долгопрудный", "Ессентуки", "Камышин", "Муром", "Жуковский", "Евпатория", "Новошахтинск", "Северск", "Реутов", "Пушкино", "Артем", "Ноябрьск", "Ачинск", "Бердск", "Арзамас", "Елец", "Элиста", "Ногинск", "Сергиев Посад", "Новокуйбышевск", "Железногорск");
      foreach ($country as $country_name) {
        $users = $this->Users($access_token, $country_name, null);

        if (!empty($users['items']))
        $this->write($users['items']);
      }
      $vk = new VKApiClient();
      foreach ($city as $city_name) {

      			$params = array(
      				'country_id'    => 1,
      				'q'		          => $city_name,
      				'count'         => '1',
      				'access_token'  => $access_token,
      				'v'             => '5.101'
      			);

retry1:		try {
            $city_n = $vk->database()->getCities($access_token, $params);
          } catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
                //echo $exception->getMessage()."\n";
                sleep(1);
                goto retry1;
          }

      		if (isset ($city_n['items'][0]['id'])) $users = $this->Users($access_token, '1', $city_n['items'][0]['id']);

          if (!empty($users['items']))
          $this->write($users['items']);
    	}
    }
}
