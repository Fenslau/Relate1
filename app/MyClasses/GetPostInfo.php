<?php
namespace App\MyClasses;

use \VK\Client\VKApiClient;

class GetPostInfo{
  public function vkGet($items) {
      if (empty(session('vkid'))) return FALSE;
      $vk = new VKApiClient();
      try {
        $post = $video = array();
        $video_ids = $posts = '';
        foreach ($items as $item) if (!empty($item['video_player'])) $video_ids .= $item['video_player'];

        $params = array(
          'videos'		      => $video_ids,
          'v' 			        => '5.107'
        );
        if(!empty($video_ids)) $videos = $vk->video()->get(session('token'), $params);


        foreach ($items as $item) if ($item['event_type'] == 'post' OR $item['event_type'] == 'share')
        $posts .= str_replace('https://vk.com/wall', '', $item['event_url']).',';
        $params = array(
          'posts'		       => $posts,
          'v' 			       => '5.107'
        );
        if(!empty($posts)) $posts = $vk->wall()->getById(session('token'), $params);
      } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
          if(empty($request) OR $request->ajax()) return FALSE;
          else return redirect()->route('stream');
      }
      $re = '/\[(.+)\|(.+)\]/U';
      $subst = '<a rel="nofollow" target="_blank" href="https://vk.com/$1">$2</a>';

      foreach ($items as &$item) {

        if (!empty($videos['items']) AND !empty($item['video_player'])) {
            $video_players = array_diff(explode(",", $item['video_player']), array('', NULL));
            $i=0;
            foreach ($video_players as $video_player) {
              preg_match_all("'_(.+?)_'", $video_player, $matches);

              $index_of_videos = array_search($matches[1][0], (array_column($videos['items'], 'id')));

              if ($index_of_videos !== FALSE) {
                if (!empty($videos['items'][$index_of_videos]['player'])) $video[$item['id']][$i]['player'] = $videos['items'][$index_of_videos]['player'];
                else $video[$item['id']][$i]['player'] = '';
                if (!empty($videos['items'][$index_of_videos]['title'])) $video[$item['id']][$i]['title'] = $videos['items'][$index_of_videos]['title'];
                else $video[$item['id']][$i]['title'] = '';
                if (!empty($videos['items'][$index_of_videos]['description'])) $video[$item['id']][$i]['description'] = preg_replace($re, $subst, $videos['items'][$index_of_videos]['description']);
                elseif (!empty($videos['items'][$index_of_videos]['content_restricted'])) $video[$item['id']][$i]['description'] = $videos['items'][$index_of_videos]['content_restricted_message'];
                else $video[$item['id']][$i]['description']  = '';
              } else {
                $video[$item['id']][$i]['title'] = '';
                $video[$item['id']][$i]['description'] = 'Это видео можно посмотреть только перейдя по ссылке поста';
                $video[$item['id']][$i]['player'] = '';
              }
              $i++;
            }
        }

        if (!empty($posts)) $count_of_posts = array_keys((array_column($posts, 'id')), $item['post_id']);
        if (!empty($count_of_posts) AND $count_of_posts !== FALSE) {
            $post_count = ' ';
            foreach ($count_of_posts as $count_of_post) if ($posts[$count_of_post]['from_id'] == $item['author_id'])
              $post_count = $count_of_post;
          if (is_numeric($post_count)) {
            $count_of_posts = $post_count;
            if (!empty($posts[$count_of_posts]['comments']['count']))
              $post[$item['id']]['comments'] = $posts[$count_of_posts]['comments']['count'];
            if (!empty($posts[$count_of_posts]['likes']['count']))
              $post[$item['id']]['likes'] = $posts[$count_of_posts]['likes']['count'];
            if (!empty($posts[$count_of_posts]['reposts']['count']))
              $post[$item['id']]['reposts'] = $posts[$count_of_posts]['reposts']['count'];
            if (!empty($posts[$count_of_posts]['views']['count']))
              $post[$item['id']]['views'] = $posts[$count_of_posts]['views']['count'];
           }
         }
    }
    $result['items'] = $items;
    $result['post'] = $post;
    $result['video'] = $video;
    return ($result);
  }

  public function authorName($items) {
    foreach ($items as &$item) {
      $item['original_author_id'] = $item['author_id'];
      if ($item['author_id'] > 0) $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/id'.$item['author_id'].'"';
      else $item['author_id'] = '<a rel="nofollow" target="_blank" href="https://vk.com/public'.-$item['author_id'].'"';
      if (!empty($item['name'])) {
        if (empty($request->apply_filter) OR $request->apply_filter == 'Показать записи')
          $item['author_id'] .= ' data-toggle="tooltip" title="Автор"';
        $item['author_id'] .= '>'.$item['name'].'</a>';
      }
      else $item['author_id'] .= '>Автор</a>';

      if (!empty($item['sex']) AND $item['sex'] == 1) $item['sex'] = 'жен';
      if (!empty($item['sex']) AND $item['sex'] == 2) $item['sex'] = 'муж';

      $re = '/\[(.+)\|(.+)\]/U';
      $subst = '<a rel="nofollow" target="_blank" href="https://vk.com/$1">$2</a>';
      if(isset($item['data'])) $item['data'] = preg_replace($re, $subst, $item['data']);
    }
    return ($items);
  }


}
?>
