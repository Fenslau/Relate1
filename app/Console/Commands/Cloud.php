<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\OldPosts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Cloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cloud:get';

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

    public function Cloud($del, $vkid, $project, $rules) {
      $limit = 2000;
      $posts = new StreamData();
      do {
        if ($del > 0) $posts = $posts->select('id', 'data')->whereIn('user', $rules)->where('check_trash', 0)->where('user_links', '!=', 'Доп.посты')->where('cloud', 0)->take($limit)->get()->toArray();
        else $posts = $posts->select('id', 'data')->whereIn('user', $rules)->where('check_trash', '>', 0)->where('user_links', '!=', 'Доп.посты')->where('cloud', 1)->take($limit)->get()->toArray();
        if (empty($posts)) continue;
        if (file_exists('public/temp/temp.txt')) unlink ('public/temp/temp.txt');
        if (file_exists('public/temp/temp_tags.txt')) unlink ('public/temp/temp_tags.txt');

        $ids = array();
        $tags_all = '';
        foreach ($posts as $post) {
            $regexp = '/<[^<>]+>/mi';
            $post['data'] = preg_replace($regexp, ' ', $post['data']);
            $regexp = '/(https?:\/\/)?([\w\.]+)\.([a-z]{2,6}\.?)(\/[\w\.?]*)*\/?/mi';
            $post['data'] = preg_replace($regexp, 'http', $post['data']).' ';
            $regexp = '/[\s]#[^\s.*,:;&?#!@][\S][^\s.*,:;&?#!]+/m';
            preg_match_all($regexp, $post['data'], $tags);
            if (!empty($tags[0])) {
            	foreach ($tags[0] as $only_tag)
            	if (strlen($only_tag) > 2) $tags_all .= $only_tag;
            }

            	file_put_contents('public/temp/temp.txt', $post['data'], FILE_APPEND | LOCK_EX);
            	$ids[] = $post['id'];
        }
        file_put_contents('public/temp/temp_tags.txt', $tags_all);

        if (file_exists('public/temp/output.txt')) unlink ('public/temp/output.txt');
        shell_exec('cd public/temp/ && '.env('MYSTEM').' temp.txt output.txt -l -d -n');

        if (file_exists('public/temp/sorted.txt')) unlink ('public/temp/sorted.txt');
        if (file_exists('public/temp/sorted_tags.txt')) unlink ('public/temp/sorted_tags.txt');
        $cmd = "sed 's/.*/\L&/;s/[[:punct:] ]\+/\\n/g' public/temp/output.txt | fgrep -vwf public/temp/stopwords-ru.txt | sed '/\S/!d' | sort | uniq -c | sort -k1,1rn >> public/temp/sorted.txt";
        shell_exec($cmd);

        $output = file ('public/temp/temp_tags.txt', FILE_IGNORE_NEW_LINES);

        if (!empty($output)) {
        	$output_all = array();
        	foreach ($output as $output_part) $output_all = array_merge($output_all, explode (' ', $output_part));
        	$output = array_count_values ($output_all);
        	foreach ($output as $key => $value) {
        		 $line = $value.' '.$key."\n";
        		 file_put_contents('public/temp/sorted_tags.txt', $line, FILE_APPEND | LOCK_EX);
        	}
        }

        $sorted = file ('public/temp/sorted.txt', FILE_IGNORE_NEW_LINES);
        if (file_exists('public/temp/sorted_tags.txt')) $sorted_tags = file ('public/temp/sorted_tags.txt', FILE_IGNORE_NEW_LINES);
        $weights_tags = $weights = array();

        if (!empty($sorted)) foreach ($sorted as $line) {
        	$item = explode(' ', trim($line));
          if (!empty($item[1])) {
          	if ($del > 0) {
              $weights[$item[1]] = $item[0];
            }
          	else {
              $weights[$item[1]] = -$item[0];
            }
          }
        }
        if (!empty($sorted_tags)) foreach ($sorted_tags as $line) {
        	$item = explode(' ', trim($line));
        	if (!empty($item[1])) {
            if ($del > 0) {
              $weights_tags[$item[1]] = $item[0];
            }
            else {
              $weights_tags[$item[1]] = -$item[0];
            }
        	}
        }
        $new_weights = array();
        if (!empty($weights)) {
          $old_weights = DB::table('clouds_'.$vkid.$project)->pluck('weight', 'name')->toArray();
          foreach ($weights as $key => $value)
          $old_weights[(string)$key] = (isset($old_weights[$key]) ? $old_weights[$key] : 0) + (int)$value;

          foreach ($old_weights as $key => $value) {
            $data['name'] = $key;
            $data['weight'] = $value;
            $new_weights[] = $data;
          }
          DB::table('clouds_'.$vkid.$project)->upsert($new_weights, ['name'], ['name', 'weight']);
        }
        $new_weights = array();
        if (!empty($weights_tags)) {
          $old_weights = DB::table('tags_'.$vkid.$project)->pluck('weight', 'name')->toArray();
          foreach ($weights_tags as $key => $value)
          $old_weights[$key] = (isset($old_weights[$key]) ? $old_weights[$key] : 0) + $value;

          foreach ($old_weights as $key => $value) {
            $data['name'] = $key;
            $data['weight'] = $value;
            $new_weights[] = $data;
          }
          DB::table('tags_'.$vkid.$project)->upsert($new_weights, ['name'], ['name', 'weight']);
        }



        	if ($del > 0) StreamData::whereIn('id', $ids)->update(['cloud' => 1]);
        	else {
        		StreamData::whereIn('id', $ids)->update(['cloud' => 0]);
        	}
      } while (count($posts) == $limit);

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $demands = OldPosts::distinct()->get()->toArray();
      foreach ($demands as $demand) {
        if (empty($demand['retry']) OR $demand['retry'] < date('U')) die;
      }
      $projects = Projects::pluck('vkid', 'project_name')->toArray();

      foreach ($projects as $project => $vkid) {
          $rules = Projects::select('re_cloud', DB::raw("CONCAT(vkid, '', rule) AS rule_tag"))->whereNull('old')->whereNotNull('rule')->where('project_name', $project)->get()->toArray();

          if (array_sum(array_column($rules, 're_cloud')) > 0) {
            StreamData::whereRaw("user IN (?)", array_column($rules, 'rule_tag'))->update(['cloud' => 0]);
            Projects::whereRaw("CONCAT(vkid, '', rule) IN (?)", array_column($rules, 'rule_tag'))->update(['re_cloud' => 0]);
            Schema::dropIfExists('clouds_'.$vkid.$project);
            Schema::dropIfExists('tags_'.$vkid.$project);
          }

          if (!empty(array_column($rules, 'rule_tag'))) {
            if (!Schema::hasTable('clouds_'.$vkid.$project)) Schema::create('clouds_'.$vkid.$project, function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->unique();
                $table->integer('weight')->nullable();
                $table->timestamps();
            });
            if (!Schema::hasTable('tags_'.$vkid.$project)) Schema::create('tags_'.$vkid.$project, function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->unique();
                $table->integer('weight')->nullable();
                $table->timestamps();
            });

            $this->Cloud (+1, $vkid, $project, array_column($rules, 'rule_tag'));
      			$this->Cloud (-1, $vkid, $project, array_column($rules, 'rule_tag'));
          }
      }
      if (file_exists('public/temp/temp.txt')) unlink ('public/temp/temp.txt');
      if (file_exists('public/temp/output.txt')) unlink ('public/temp/output.txt');
      if (file_exists('public/temp/sorted.txt')) unlink ('public/temp/sorted.txt');
      if (file_exists('public/temp/temp_tags.txt')) unlink ('public/temp/temp_tags.txt');
      if (file_exists('public/temp/output_tags.txt')) unlink ('public/temp/output_tags.txt');
      if (file_exists('public/temp/sorted_tags.txt')) unlink ('public/temp/sorted_tags.txt');
    }
}
