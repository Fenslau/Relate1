<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \VK\Client\VKApiClient;

class CommentsController extends Controller
{
    public function get(Request $request) {
      if ($request->ajax() AND empty(session('vkid'))) return response()->json(array('success' => false));
      $info = $items = array();
      $event_url = str_replace('https://vk.com/wall', '', $request->event_url);
  		$event_url = explode('_', $event_url);
  		$post_id = $event_url[1];
  		$wall_id = $event_url[0];
      $returnHTML = $this->getVKComments($wall_id, $post_id);

      return response()->json( array('success' => true, 'html'=>$returnHTML) );
    }

    public function getVKComments($author_id, $post_id, $comment_id = NULL) {
      $vk = new VKApiClient();
      $items = array();
      $params = array(
				'owner_id'		 => $author_id,
				'post_id'    	 => $post_id,
				'comment_id'	 => $comment_id,
				'count'   		 => 100,
				'extended'   	 => 1,
				'lang'			   => 'ru',
				'sort'   		   => 'ASC',
				'v' 			     => '5.107'
			);
retrywall:
    try {
      $comments = $vk->wall()->getComments(session('token'), $params);
    } catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
      goto retrywall;
    }
      if (!empty($comments['items'])) {
    		foreach ($comments['items'] as $comment) {
    			$comment_id = $comment['id'];
    			if (isset($comment['deleted'])) {
    				$from_id = 0;
    				$data = 'Комментарий удалён пользователем или руководителем страницы';
    			} else {
    				$from_id = $comment['from_id'];
    				$data = $comment['text'];
    			}
    			if (isset($comment['parents_stack'][0])) $parent_comment_id = $comment['parents_stack'][0]; else $parent_comment_id = 0;
    			$action_time = $comment['date'];
          $video_players = array();
    			$html = $sticker = $doc = $note = $audio = $video_player = $linkr = $photo = '';

    			if (isset($comment['attachments'])) foreach ($comment['attachments'] as $att) {

    				switch($att['type']) {
      				case 'note': $note = $att['note']['text']; break;

      				case 'link': $linkr = $linkr.$att['link']['url'].','; break;
      				case 'doc': $doc = $doc.$att['doc']['url'].','; break;
      				case 'sticker':
      					if (isset($att['sticker']['images']))
      						foreach ($att['sticker']['images'] as $stick) if ($stick['width'] == 128) $sticker = $sticker.$stick['url'].'9GZVNyidgk';
      					break;

      				case 'posted_photo': $photo = $photo.$att['photo']['photo_604'].'9GZVNyidgk'; break;

      				case 'photo': foreach ($att['photo']['sizes'] as $size)
      					if ($size['type'] == 'r')	$photo = $photo.$size['url'].'9GZVNyidgk'; break;
      				case 'video': $video_player = $video_player.$att['video']['owner_id'].'_'.$att['video']['id'].'_'.$att['video']['access_key'].','; break;
      				case 'audio': $audio = $audio.$att['audio']['artist'].' — '.$att['audio']['title'].'9GZVNyidgk'; break;
    				}
    			}
    			$name = 'Удалено';
    			foreach ($comments['profiles'] as $profile) {
    				if ($profile['id'] == $from_id) {
    					$name = $profile['first_name'].' '.$profile['last_name'];
    				}
    			}
    			foreach ($comments['groups'] as $group) {
    				if ($group['id'] == -$from_id) {
    					$name = $group['name'];
    				}
    			}


    			$re = '/\[(.+)\|(.+)\]/U';
    			$subst = '<a rel="nofollow" target="_blank" href="https://vk.com/$1">$2</a>';

    			$data = preg_replace($re, $subst, $data);
    			if ($note != '') {
    			   $note = preg_replace($re, $subst, $note);
    			}
          if (!empty($video_player)) {
    					$params = array(
      					'videos' => $video_player,
      					'v' => '5.103'
      				);
  retry:      try {
                $player = $vk->video()->get(session('token'), $params);
              } catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
                goto retry;
              }
              if (!empty($player) AND empty($player['items'])) $video_players[] = 'FALSE';
              foreach ($player['items'] as $video_player1)
              if (!empty($video_player1['player'])) $video_players[] = $video_player1['player'];
              else $video_players[] = 'FALSE';
          }

    			if (isset ($comment['thread']['count']) AND $comment['thread']['count'] > 0) $html = $this->getVKComments($author_id, $post_id, $comment['id']);

          $item['from_id'] = $from_id; $item['name'] = $name; $item['note'] = $note; $item['data'] = $data; $item['sticker'] = $sticker; $item['photo'] = $photo; $item['audio'] = $audio;
          $item['video_players'] = $video_players; $item['linkr'] = $linkr; $item['doc'] = $doc; $item['action_time'] = $action_time; $item['parent_comment_id'] = $parent_comment_id;
          $item['html'] = $html;
          $items[] = $item;
        }
    	}
      $returnHTML = view('layouts.comments-ajax', ['items' => $items])->render();
      return ($returnHTML);
    }
}
