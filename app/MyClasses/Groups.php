<?php
namespace App\MyClasses;

use \VK\Client\VKApiClient;
use \XLSXWriter;
use \SimpleXLSX;

use \App\MyClasses\GetProgress;
use App\Models\Progress;
use App\Models\Toppost;
use App\Models\IgnoredGroups;

class Groups {

  public function __construct() {
  $this->groups=array();
  }

  public function read($filename) {

    try {
      $xlsx = new SimpleXLSX($filename);
    }
    catch (\Exception $exception) {
      //unlink($filename);
      return FALSE;
    }
    if ($xlsx->success() AND count( $xlsx->rows()) > 1 ) {
      $iter = true;
      $items=array();
      foreach($xlsx->rows() as $item_) {
        if($iter) $iter=false; else {

          $item['num'] = $item_[0];
          $item['id'] = $item_[1];
          $item['name'] = $item_[2];
          if (is_numeric($item_[3])) $item['members_count'] = $item_[3]; else $item['members_count']='';
          if (is_numeric($item_[4])) $item['grouth'] = $item_[4]; else $item['grouth']='';

          if (is_numeric($item_[5])) $item['reach'] = $item_[5]; else $item['reach']='';
          $item['reach_to_sub'] = $item_[6];
          if (is_numeric($item_[7])) $item['reach_subscribers'] = $item_[7]; else $item['reach_subscribers']='';
          $item['sub_to_reach'] = $item_[8];
          if (is_numeric($item_[9])) $item['female'] = $item_[9]; else $item['female']='';
          $item['female_proc'] = $item_[10];
          if (is_numeric($item_[11])) $item['male'] = $item_[11]; else $item['male']='';
          $item['male_proc'] = $item_[12];
          if (is_numeric($item_[13])) $item['visitors'] = $item_[13]; else $item['visitors']='';
          $item['views_to_visit'] = $item_[14];
          if (is_numeric($item_[15])) $item['over_18'] = $item_[15]; else $item['over_18']='';
          $item['over_18_procent'] = $item_[16];
          $item['cities'] = $item_[17];
          if (is_numeric($item_[18])) $item['cities_count'] = $item_[18]; else $item['cities_count']='';
          $item['max_visitors'] = $item_[19];
          $item['can_post'] = $item_[20];
          $item['wall'] = $item_[21];
          $item['is_closed'] = $item_[22];
          $item['type'] = $item_[23];
          $item['date'] = $item_[24];
          $item['photo_50'] = $item_[25];
          if (isset($item_[26])) $item['comments'] = $item_[26];
          if (isset($item_[27])) $item['views'] = $item_[27];
          if (isset($item_[28])) $item['likes'] = $item_[28];
          if (isset($item_[29])) $item['reposts'] = $item_[29];
          if (isset($item_[30])) $item['reactions'] = $item_[30];
          $this->groups[] = $item;
        }
      }
    } else return FALSE;
  }
  public function write($filename, $rand = NULL) {
	$progress = new GetProgress(session('vkid'), 'simple_search'.$rand, 'Записывается файл Excel', count($this->groups), 1);
    $writer = new XLSXWriter();
														$header = array(
														  '№'=>'integer',
														  'id группы'=>'integer',
														  'Название'=>'string',
														  'Подписчики'=>'integer',
														  'Прирост'=>'integer',
														  'Охват'=>'integer',
														  '(отношение полного охвата к охвату подписчиков)'=>'string',
														  'Охват подписч.'=>'integer',
														  '(% от полного охвата)'=>'string',
														  'Жен'=>'integer',
														  '(% от посетителей)'=>'string',
														  'Муж'=>'integer',
														  '(% от посетителей) '=>'string',
														  'Посетители'=>'integer',
														  '(кол-во просмотров на посетителя)'=>'string',
														  'Старше 18 лет'=>'integer',
														  '(% от посетителей )'=>'string',
														  'Из города'=>'string',
														  'количество'=>'integer',
														  '( % от посетителей)'=>'string',
														  'Стена'=>'string',
														  'стена'=>'string',
														  'Тип'=>'string',
														  'сообщества'=>'string',
														  'Дата'=>'string',
														  'Аватарка'=>'string',
														);
                            if (isset($this->groups[0]['comments'])) {
                              $header['Комментарии'] = 'integer';
                              $header['Просмотры'] = 'integer';
                              $header['Лайки'] = 'integer';
                              $header['Репосты'] = 'integer';
                              $header['Реакции'] = 'integer';
                            }
														$writer->writeSheetHeader('Sheet1', $header );

      foreach($this->groups as $row) {
        $item['num'] = $row['num'];
        $item['id'] = $row['id'];
        $item['name'] = $row['name'];
        $item['members_count'] = $row['members_count'];
        $item['grouth'] = $row['grouth'];
        $item['reach'] = $row['reach'];
        $item['reach_to_sub'] = $row['reach_to_sub'];
        $item['reach_subscribers'] = $row['reach_subscribers'];
        $item['sub_to_reach'] = $row['sub_to_reach'];
        $item['female'] = $row['female'];
        $item['female_proc'] = $row['female_proc'];
        $item['male'] = $row['male'];
        $item['male_proc'] = $row['male_proc'];
        $item['visitors'] = $row['visitors'];
        $item['views_to_visit'] = $row['views_to_visit'];
        $item['over_18'] = $row['over_18'];
        $item['over_18_procent'] = $row['over_18_procent'];
        $item['cities'] = $row['cities'];
        $item['cities_count'] = $row['cities_count'];
        $item['max_visitors'] = $row['max_visitors'];
        $item['wall'] = $row['wall'];
        $item['can_post'] = $row['can_post'];
        $item['is_closed'] = $row['is_closed'];
        $item['type'] = $row['type'];
        if (!empty($row['date'])) $item['date'] = $row['date']; else $item['date'] = '';
        $item['photo_50'] = $row['photo_50'];
          if (isset($row['comments'])) $item['comments'] = $row['comments'];
          if (isset($row['views'])) $item['views'] = $row['views'];
          if (isset($row['likes'])) $item['likes'] = $row['likes'];
          if (isset($row['reposts'])) $item['reposts'] = $row['reposts'];
          if (isset($row['reactions'])) $item['reactions'] = $row['reactions'];
        $writer->writeSheetRow('Sheet1', $item);
      }
      $writer->writeToFile($filename);
  }



  public function get1000Groups($group_ids, $token, $rand = NULL) {
    if (empty($group_ids)) return FALSE;
	$progress = new GetProgress(session('vkid'), 'simple_search'.$rand, 'Собирается общая информация по группам', 1, 1);
  $group_ids1 = implode(',', array_slice($group_ids, 0, 500));
  $group_ids2 = implode(',', array_slice($group_ids, 500, 500));
  $vk = new VKApiClient();
    $params = array(
        'group_ids'		 => $group_ids1,
        'fields'    	 => 'members_count,can_post,links,wall',
        'lang'   		   => 'ru',
        'v' 			     => '5.95'
    );

retry1:
    try {
      $this->groups = $vk->groups()->getById($token, $params);
    } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
      echo $exception->getMessage()."\n";
      die;
    }
    catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
      //echo $exception->getMessage()."\n";
      sleep(1);
      goto retry1;
    }
    catch (\VK\Exceptions\VKClientException  $exception) {
      echo $exception->getMessage()."\n";
      die;
    }
    if (!empty($group_ids2)) {
      $params['group_ids'] = $group_ids2;

retry2:
      try {
      $this->groups = array_merge($this->groups, $vk->groups()->getById($token, $params));
      } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
        echo $exception->getMessage()."\n";
        die;
      }
      catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
        //echo $exception->getMessage()."\n";
        sleep(1);
        goto retry2;
      }
      catch (\VK\Exceptions\VKClientException  $exception) {
        echo $exception->getMessage()."\n";
        die;
      }
    }

    $k=1;
    foreach ($this->groups as &$item) {
      $item['num'] = $k++;
      if (empty($item['members_count'])) $item['members_count']='';
      if (empty($item['grouth'])) $item['grouth']='';
      if (empty($item['reach'])) $item['reach']='';
      if (empty($item['reach_to_sub'])) $item['reach_to_sub']='';
      if (empty($item['reach_subscribers'])) $item['reach_subscribers']='';
      if (empty($item['sub_to_reach'])) $item['sub_to_reach']='';
      if (empty($item['female'])) $item['female']='';
      if (empty($item['male'])) $item['male']='';
      if (empty($item['female_proc'])) $item['female_proc']='';
      if (empty($item['male_proc'])) $item['male_proc']='';
      if (empty($item['visitors'])) $item['visitors']='';
      if (empty($item['views_to_visit'])) $item['views_to_visit']='';
      if (empty($item['over_18'])) $item['over_18']='';
      if (empty($item['over_18_procent'])) $item['over_18_procent']='';
      if (empty($item['cities'])) $item['cities']='';
      if (empty($item['cities_count'])) $item['cities_count']='';
      if (empty($item['max_visitors'])) $item['max_visitors']='';
      if (!isset($item['wall'])) $item['wall'] = ' ';
      if ($item['wall'] == '0') $item['wall'] = ' ';
      if ($item['wall'] == '1') $item['wall'] = 'открытая';
      if ($item['wall'] == '2') $item['wall'] = 'только комменты';
      if ($item['wall'] == '3') $item['wall'] = ' ';

      if (!isset($item['can_post'])) $item['can_post'] = ' ';
      if ($item['can_post'] == '1') $item['can_post'] = 'посты и комменты';
      if ($item['can_post'] == '0') $item['can_post'] = ' ';


      if (!isset($item['type'])) $item['type'] = ' ';
      if ($item['type'] == 'group') $item['type'] = 'группа';
      if ($item['type'] == 'page') $item['type'] = 'страница';
      if ($item['type'] == 'event') $item['type'] = 'мероприятие';

      if (!isset($item['is_closed'])) $item['is_closed'] = ' ';
      if ($item['is_closed'] == '0') $item['is_closed'] = 'открытое';
      if ($item['is_closed'] == '1') $item['is_closed'] = 'закрытое';
      if ($item['is_closed'] == '2') $item['is_closed'] = 'частное';
    }
  }


  public function getStats($token, $rand = NULL) {
    $vk = new VKApiClient();
    $group_ids_all = array_column($this->groups, 'id');
	$progress = new GetProgress(session('vkid'), 'simple_search'.$rand, 'Собирается статистика по группам', count($group_ids_all), 25);
    for ($j=1; $j<=40; $j++) {
		$progress->step();
        $code_1 = 'return[';

        for ($i=0; $i<25; $i++) {
          $k=($j-1)*25+$i;
          if (!empty($group_ids_all[$k])) $code_1 = $code_1.'API.stats.get({"group_id":'.$group_ids_all[$k].',"v":5.95,"intervals_count":1}),';
          else break;
        }
        $code_1 .= '];';
        if ($code_1 == 'return[];') break;
retrys:
    try {
      $stat1 = $vk->getRequest()->post('execute', $token, array(
        'code' 			    => $code_1,
        'v' 			      => '5.95'
      ));
    } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
      echo $exception->getMessage()."\n";
      die;
    }
    catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
      sleep(1);
      goto retrys;
    }
    catch (\VK\Exceptions\VKClientException  $exception) {
      echo $exception->getMessage()."\n";
      die;
    }

      for ($i=0; $i<25; $i++) {
        $k=($j-1)*25+$i;

      if (!empty($this->groups[$k]['id']) AND !empty($stat1[$i])) {

          @$this->groups[$k]['grouth'] = $stat1[$i][0]['activity']['subscribed']-$stat1[$i][0]['activity']['unsubscribed'];
          if ($this->groups[$k]['grouth']==0) $this->groups[$k]['grouth'] = ' ';
          $this->groups[$k]['reach'] = $stat1[$i][0]['reach']['reach'];
          if ($stat1[$i][0]['reach']['reach_subscribers'] <> 0)
            $this->groups[$k]['reach_to_sub'] = round(($stat1[$i][0]['reach']['reach']/$stat1[$i][0]['reach']['reach_subscribers']), 2);
            else $this->groups[$k]['reach_to_sub']  = ' ';
          $this->groups[$k]['reach_subscribers'] = $stat1[$i][0]['reach']['reach_subscribers'];
          if ($this->groups[$k]['reach_to_sub'] <> 0 AND $this->groups[$k]['reach_to_sub'] != ' ') {
            $this->groups[$k]['sub_to_reach'] = round((1/$this->groups[$k]['reach_to_sub']*100), 2);
            $this->groups[$k]['sub_to_reach'] = $this->groups[$k]['sub_to_reach'].'%';
          } else $this->groups[$k]['sub_to_reach'] = ' ';
          if (!empty($stat1[$i][0]['visitors']['sex'][0]['count']))
            $this->groups[$k]['female'] = $stat1[$i][0]['visitors']['sex'][0]['count']; else $this->groups[$k]['female'] = '';
          if (!empty($stat1[$i][0]['visitors']['sex'][0]['count']) AND $stat1[$i][0]['visitors']['visitors'] <> 0) {
            $this->groups[$k]['female_proc'] = round(($stat1[$i][0]['visitors']['sex'][0]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
            $this->groups[$k]['female_proc'] = $this->groups[$k]['female_proc'].'%';
          } else $this->groups[$k]['female_proc'] = ' ';
          if (!empty($stat1[$i][0]['visitors']['sex'][1]['count']))
            $this->groups[$k]['male'] = $stat1[$i][0]['visitors']['sex'][1]['count']; else $this->groups[$k]['male'] = '';
          if (!empty($stat1[$i][0]['visitors']['sex'][1]['count']) AND $stat1[$i][0]['visitors']['visitors'] <> 0) {
            $this->groups[$k]['male_proc'] = round(($stat1[$i][0]['visitors']['sex'][1]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
            $this->groups[$k]['male_proc'] = $this->groups[$k]['male_proc'].'%';
          } else $this->groups[$k]['male_proc'] = ' ';
          $this->groups[$k]['visitors'] = $stat1[$i][0]['visitors']['visitors'];
          if ($stat1[$i][0]['visitors']['visitors'])
            $this->groups[$k]['views_to_visit'] = round(($stat1[$i][0]['visitors']['views']/$stat1[$i][0]['visitors']['visitors']), 2);
          else $this->groups[$k]['views_to_visit'] = ' ';
          $this->groups[$k]['over_18'] = $stat1[$i][0]['visitors']['visitors'] - @$stat1[$i][0]['visitors']['age'][0]['count'];

          if ($stat1[$i][0]['visitors']['visitors'] <> 0) {
            $this->groups[$k]['over_18_procent'] = round(($this->groups[$k]['over_18']/$stat1[$i][0]['visitors']['visitors']*100), 2);
            $this->groups[$k]['over_18_procent'] = $this->groups[$k]['over_18_procent'].'%';
          } else $this->groups[$k]['over_18_procent'] = ' ';
          if (!empty($stat1[$i][0]['visitors']['cities'][0]['name']))
            $this->groups[$k]['cities'] = $stat1[$i][0]['visitors']['cities'][0]['name'];
            else $this->groups[$k]['cities'] = '';
          if (!empty($stat1[$i][0]['visitors']['cities'][0]['count']))
            $this->groups[$k]['cities_count'] = $stat1[$i][0]['visitors']['cities'][0]['count'];
            else $this->groups[$k]['cities_count'] = '';
          if ($stat1[$i][0]['visitors']['visitors'] <> 0 AND isset($stat1[$i][0]['visitors']['cities'][0]['count'])) {
            $this->groups[$k]['max_visitors'] = round(($stat1[$i][0]['visitors']['cities'][0]['count']/$stat1[$i][0]['visitors']['visitors']*100), 2);
            $this->groups[$k]['max_visitors'] = ' ('.$this->groups[$k]['max_visitors'].'%)';
          } else $this->groups[$k]['max_visitors'] = ' ';
        }
      }
    }
  ex:
  }


  public function getLastPostDate($token, $rand = NULL, $toppost = NULL) {
    $vk = new VKApiClient();
    $posts = new Toppost();
    $data_post = array();
    $group_ids_all = array_column($this->groups, 'id');

	$progress = new GetProgress(session('vkid'), 'simple_search'.$rand, 'Собираются даты последних постов', count($group_ids_all), 25);
    $ignored = IgnoredGroups::pluck('group_id')->toArray();

    for ($j=1; $j<=40; $j++) {
		$progress->step();
          $code_2 = 'return[';

          for ($i=0; $i<25; $i++) {
            $k=($j-1)*25+$i;
            if (isset($group_ids_all[$k])) {
              if ($toppost) $code_2 = $code_2.'API.wall.get({"owner_id":-'.$group_ids_all[$k].',"v":5.95,"count":50}),';
              else $code_2 = $code_2.'API.wall.get({"owner_id":-'.$group_ids_all[$k].',"v":5.95,"count":2}),';
            }
            else break;
          }
          $code_2 .= '];';
          if ($code_2 == 'return[];') break;
retrys:
      try {
        $wall1 = $vk->getRequest()->post('execute', $token, array(
          'code' 			    => $code_2,
          'v' 			      => '5.95'
        ));
      } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
        echo $exception->getMessage()."\n";
        die;
      }
      catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
        sleep(1);
        goto retrys;
      }
      catch (\VK\Exceptions\VKClientException  $exception) {
        echo $exception->getMessage()."\n";
        continue;
      }

        for ($i=0; $i<25; $i++) {
          $k=($j-1)*25+$i;
            if (!empty($this->groups[$k]['id'])) {
              if (!empty($wall1[$i]['items'][1]['date'])) {
                  $this->groups[$k]['date'] = date('d.m.Y', max($wall1[$i]['items'][1]['date'], $wall1[$i]['items'][0]['date']));
               }
            }
            if ($toppost) {
              if (isset($wall1[$i]['items'])) {
                foreach ($wall1[$i]['items'] as $post) {
                  if(empty($post['is_pinned']) AND $post['marked_as_ads'] == 0 AND (date('U') - $post['date']) < 5*24*60*60 AND !in_array($post['from_id'], $ignored)) {
                      $data = array();
                      if (!empty($post['copy_history'])) {
                        $post['text'] = $post['copy_history'][0]['text'];
                        if (isset($post['copy_history'][0]['attachments'])) $post['attachments'] = $post['copy_history'][0]['attachments'];
                        $post['post_type'] = 'share';
                      }
                      $data['post_id'] = $post['id'];
                  		$data['data'] =  str_replace("\n", '<br />', $post['text']);
                  		$data['event_type'] = $post['post_type'];
                  		$data['action_time'] = $post['date'];
                  		$data['author_id'] = $post['from_id'];
                  			$event_url = '';
                  			if ($post['post_type'] == 'post' OR $post['post_type'] == 'share') $data['event_url'] = 'https://vk.com/wall'.$post['owner_id'].'_'.$post['id'];
                  			if ($post['post_type'] == 'reply') $data['event_url'] = 'https://vk.com/wall' .$post['owner_id'].'_'.$post['post_id'].'?reply='.@$post['parents_stack'][0];

                  		$data['video_player'] =	$data['photo'] = $data['link'] = $data['audio'] = $data['doc'] = $data['note'] = '';
                      $data['comments'] = $data['views'] = $data['likes'] = $data['reposts'] = 0;
                  		if (isset($post['attachments'])) foreach ($post['attachments'] as $att) {

                  			switch($att['type']) {
                    			case 'note': $data['note'] = $att['note']['text'];
                    			case 'link': $data['link'] = $data['link'].$att['link']['url'].','; break;
                    			case 'doc': $data['doc'] = $data['doc'].$att['doc']['url'].','; break;
                    			case 'photo': foreach ($att['photo']['sizes'] as $photosize) if ($photosize['type'] == 'x') $data['photo'] = $data['photo'].$photosize['url'].','; break;
                    			case 'video': if(isset($att['video']['access_key'])) $data['video_player'] = $data['video_player'].$att['video']['owner_id'].'_'.$att['video']['id'].'_'.$att['video']['access_key'].',';
                          else $data['video_player'] = $data['video_player'].$att['video']['owner_id'].'_'.$att['video']['id'].','; break;
                    			case 'audio': $data['audio'] = $data['audio'].'<div class="d-flex align-items-center justify-content-between">'.$att['audio']['artist'].' — '.$att['audio']['title'].'</div>'.'9GZVNyidgk';
                  			}
                  		}

                      if (isset($post['comments']['count'])) $data['comments'] = $post['comments']['count'];
                      if (isset($post['views']['count']) )$data['views'] = $post['views']['count'];
                      if (isset($post['likes']['count'])) $data['likes'] = $post['likes']['count'];
                      if (isset($post['reposts']['count'])) $data['reposts'] = $post['reposts']['count'];
                      $data_post[] = $data;
                  }

                }
              }
            }
          }
          if ($toppost) {
            $posts->upsert($data_post, ['author_id', 'post_id'], ['video_player', 'photo', 'link', 'audio', 'doc', 'data', 'comments', 'likes', 'views', 'reposts']);
            $data_post = array();
          }
      }
  ex:
  }

  public function getReactions() {
      $posts = new Toppost();
      $group_ids_all = array_column($this->groups, 'id');
      foreach ($group_ids_all as $group_id) {
        $comments = array_sum($posts->where('author_id', -$group_id)->pluck('comments')->toArray());
        $views = array_sum($posts->where('author_id', -$group_id)->pluck('views')->toArray());
        $likes = array_sum($posts->where('author_id', -$group_id)->pluck('likes')->toArray());
        $reposts = array_sum($posts->where('author_id', -$group_id)->pluck('reposts')->toArray());

          $index = array_search($group_id, array_column($this->groups, 'id'));
          $this->groups[$index]['comments'] = $comments;
          $this->groups[$index]['views'] = $views;
          $this->groups[$index]['likes'] = $likes;
          $this->groups[$index]['reposts'] = $reposts;
          $this->groups[$index]['reactions'] = $comments + $likes + $reposts;
      }
  }

}
?>
