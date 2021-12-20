<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use \App\MyClasses\MyRules;
use Illuminate\Support\Facades\DB;

class Dublikat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dublikat:find';

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
$time_start = microtime(true);
        $all_post_time = $time = $k = 0;
        $posts = new StreamData();
        $projects = new Projects();
        while (!empty ($post = $posts->whereNull('dublikat')->where('user_links', '!=', 'Доп.посты')->where('check_trash', 0)->orderBy('action_time', 'desc')->first())) {
            if (strlen($post->data) < 13) {
              $post->dublikat = 'ch';
              $post->save();
              continue;
            }

            $project = $projects->whereRaw("CONCAT_WS('', vkid, rule) =?", $post->user)->pluck('project_name')->first();

            $rules = Projects::select(DB::raw("CONCAT_WS('', vkid, rule) AS rule"))->where('project_name', $project)->whereNotNull('rule')->pluck('rule')->toArray();

          if (!empty($rules)) {

            $post_data = str_replace('*', '\*', substr($post->data, 0, 1024));

            $dublikaty = StreamData::selectRaw("id, data, MATCH (data) AGAINST(?) AS search_score", [$post_data])->whereIn('user', $rules)->where('user_links', '!=', 'Доп.посты')->where('check_trash', 0)->orderBy('search_score', 'desc')->orderBy('id', 'desc')->take(1000)->get()->toArray();

            $k++;
            $rel = $dublikaty[0]['search_score'];
            $dub_id = array();
            $i = 0;

              while ($i < 1000 AND $rel != 0 AND ($dublikaty[$i]['search_score']/$rel) > 0.2) {
                similar_text($post_data, substr($dublikaty[$i]['data'], 0, 1024), $dup_per);
                if ($dup_per > 90 AND $dublikaty[$i]['id'] != $post->id) {
                  $dub_id[] = $dublikaty[$i]['id'];
                }
                $i++;
                if (empty($dublikaty[$i]['search_score'])) break;
              }

              $prev_time = $time;
              $time = microtime(true)-$time_start;
              $post_time = $time - $prev_time;
              $all_post_time += $post_time;
              $average_time = $all_post_time/$k;
              echo ' '.round (($time), 0).'сек. '.$k.' '.$project.' '.' '.$post->id.' за '.round (($post_time), 0).' сек. Среднее время поста '.
              round (($average_time), 0).' сек'."\n";

            if (!empty($dub_id)) {
              $dub_id[] = $post->id;
              $posts->whereIn('id', $dub_id)->update(['dublikat' => implode(',', $dub_id)]);    
            }
            else $posts->where('id', $post->id)->update(['dublikat' => 'ch']);

          } else $posts->where('id', $post->id)->update(['dublikat' => 'ch']);
        }
    }
}
