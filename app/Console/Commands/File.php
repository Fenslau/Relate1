<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\FileXLS;
use \XLSXWriter;
use App\Http\Controllers\Stream\PostController;

class File extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'File:get';

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
        $file = new FileXLS();
        $offset = 1000;
        while (!empty ($demand = $file->whereNull('link')->first())) {
          $request = (object)unserialize($demand->get_params);
          $vkid = $demand->vkid;
          $project_name = $demand->project_name;

          $posts = new PostController;
          $count = $offset = 0;
        	$i = 1;
        	$writer = new XLSXWriter();
          if (empty($request->apply_filter) OR $request->apply_filter == 'Показать записи') {
        														$header = array(
        															'№'=>'string',
        															'Тип записи'=>'string',
        															'Ссылка'=>'string',
        															'Оригинал'=>'string',
        															'Время'=>'string',
        															'Автор'=>'string',
        															'Ссылка на автора'=>'string',
        															'Ссылка на автора оригинала'=>'string',
        															'Платформа'=>'string',
        															'Правило'=>'string',
        															'Текст'=>'string',
        														);
        														$writer->writeSheetHeader('Sheet1', $header );
            do {
                $items = $posts->getPost($vkid, $project_name, $request, $offset);
                foreach ($items as $post) {
              		$infox['number'] = $i++;
              		$infox['event_type']	= $post['event_type'];
              		$infox['event_url']	= $post['event_url'];
              		if ($post['shared_post_author_id'])
              			$infox['shared_post_url'] = 'https://vk.com/wall'.$post['shared_post_author_id'].'_'.$post['shared_post_id'];
              			else $infox['shared_post_url'] = '';
              		$infox['action_time']	= strftime ('%d %b %yг.  %R', $post['action_time']);
              		if (!empty($post['name'])) $infox['author'] = $post['name']; else $infox['author'] = 'Автор';
              		if ($post['author_id'] > 0) $infox['author_link'] = 'https://vk.com/id'.$post['author_id'];
              			else $infox['author_link'] = 'https://vk.com/public'.-$post['author_id'];

              		if (!empty($post['shared_post_author_id'])) {
              			if ($post['shared_post_author_id'] > 0) $infox['author_original_link'] = 'https://vk.com/id' .$post['shared_post_author_id'];
              			else $infox['author_original_link'] =  'https://vk.com/public'.-$post['shared_post_author_id'];
              		} else $infox['author_original_link'] = '';

              		$infox['platform']	= $post['platform'];
              		$infox['pravilo']	= str_replace($vkid, '', $post['user']);
              		$infox['data']	= $post['data'];
              			$count++;
              			$sheet=(intdiv(($count-1), 1000000)+1);
              			$writer->writeSheetRow('Sheet'.$sheet, $infox);
              	}

              $offset+=1000;
            } while (count($items) == $offset);
          } else {
                                    $header = array(
                                      '№'=>'string',
                                      'Автор'=>'string',
                                      'Ссылка'=>'string',
                                      'Страна'=>'string',
                                      'Город'=>'string',
                                      'Кол-во подписчиков'=>'string',
                                      'Пол'=>'string',
                                      'Возраст'=>'string',
                                      'Активность'=>'string',
                                    );
                                    $writer->writeSheetHeader('Sheet1', $header );
                    do {
                        $items = $posts->getPost($vkid, $project_name, $request, $offset);
                        foreach ($items as $author) {
                        $infox['number'] = $i++;
                        $infox['name'] = $author['name'];
                        $infox['link'] = 'https://vk.com/';
                        if ($author['author_id'] > 0) $infox['link'] .= 'id'.$author['author_id'];
                        else $infox['link'] .= 'public'.-$author['author_id'];
                        $infox['country'] = $author['country'];
                        $infox['city'] = $author['city'];
                        if (isset($author['members_count']) AND $author['members_count'] >= 0) $infox['members_count'] = $author['members_count']; else $infox['members_count'] = '';
                        $infox['sex'] = '';
                        if ($author['sex'] == 1) $infox['sex'] = 'жен';
                        if ($author['sex'] == 2) $infox['sex'] = 'муж';
                        if (!empty($author['age'])) $infox['age'] = $author['age']; else $infox['age'] = '';
                        $infox['cnt'] = $author['cnt'];

                        $count++;
                        $sheet=(intdiv(($count-1), 1000000)+1);
                        $writer->writeSheetRow('Sheet'.$sheet, $infox);
                        }
                        $offset+=1000;
                      } while (count($items) == $offset);

          }

        $filelink = $vkid.'_stream_'.rand();
        $writer->writeToFile('public/storage/stream/'.$filelink.'.xlsx');
        $demand->link = $filelink;
        $demand->save();
        echo date('d.m.Y H:i').' был создан файл для '.$vkid."\n";
        }

    }
}
