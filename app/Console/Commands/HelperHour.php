<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Top;
use App\Models\VkGroups;
use \VK\Client\VKApiClient;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamData;
use App\Models\Stream\StreamKey;
use App\Models\Stream\Links;
use \App\MyClasses\VKUser;
use Illuminate\Support\Facades\DB;

class HelperHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Help:hour';

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
      $vk = new VkGroups();
      $top = new Top();
        $group_ids_all = $vk->orderBy('members_count', 'desc')->take(1000)->pluck('group_id')->toArray();

        $group_ids_all = implode(',', $group_ids_all);
        if ($top1000 = $top->find(1) AND !empty($group_ids_all)) {
          $top1000->top1000 = $group_ids_all;
          $top1000->save();
        }

        $projects = new Projects();
        $posts = new StreamData();
        $rules = $projects->select("*", DB::raw("CONCAT (vkid, '', rule) AS rule_tag"))->whereNotNull('rule')->pluck('rule_tag');
        foreach ($rules as $rule) {
          $count = $posts->select(DB::raw("COUNT(*) AS cnt, max(action_time) AS maxdate, min(action_time) AS mindate"))->where('user', $rule)->where('check_trash', 0)->where('user_links', '')->where (function ($query) {
            $query->where('dublikat', 'NOT LIKE', 'd%');
            $query->orWhereNotNull('dublikat');
          })->get()->toArray();
          $countries = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(DB::raw('country, COUNT(*) as cnt'))->where('user', $rule)->orderBy('cnt', 'desc')->groupBy('country')->pluck('country')->toArray();
          $countries = array_diff($countries, array('', NULL));
          $countries = implode(',', $countries);

          $cities = $posts->leftJoin('authors', 'stream_data.author_id', '=', 'authors.author_id')->select(DB::raw('city, COUNT(*) as cnt'))->where('user', $rule)->orderBy('cnt', 'desc')->groupBy('city')->pluck('city')->toArray();
          $cities = array_diff($cities, array('', NULL));
          $cities = implode(',', $cities);

          $projects->whereRaw("CONCAT (vkid, '', rule) = ?", $rule)->update(['maxdate' => $count[0]['maxdate'], 'mindate' => $count[0]['mindate'], 'count_stream_records' => $count[0]['cnt'], 'countries' => $countries, 'cities' => $cities]);
        }



        $vkids = $projects->distinct()->pluck('vkid')->toArray();
        foreach ($vkids as $vkid) {
        $limit = new VKUser($vkid);
        $limit = (array)$limit;
        	if (!empty($limit['date']) AND date('U') > strtotime($limit['date'])) {
        		$actual_rules = $projects->where('vkid', $vkid)->whereNotNull('rule')->whereNull('old')->pluck('rule')->toArray();
        		foreach ($actual_rules as $actual_rule) {
              $stream = new Streamkey();
              $stream = $stream->find(1);

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($ch, CURLOPT_POSTFIELDS, '{"tag":"'.$vkid.$actual_rule.'"}');
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
              $out = json_decode(curl_exec($ch), true);
              curl_close($ch);
              if (!empty($out['code']) AND $out['code'] == 200) echo 'Удалено правило '.$actual_rule.' пользователя '.$vkid."\n";;
        		}
        	}
        	if (!empty($limit['date']) AND date('U') > (strtotime($limit['date'])+3600*24*3)) {
        		$actual_projects = $projects->where('vkid', $vkid)->distinct()->pluck('project_name')->toArray();
        		foreach ($actual_projects as $project) {
                $links = new Links();
                $user_links = $links->where('vkid', $vkid)->where('project_name', $project)->pluck('id');
                $project_to_del = $projects->where('vkid', $vkid)->where('project_name', $project)->pluck('id');
                $projects->destroy($project_to_del);
                $links->destroy($user_links);
                DB::statement('DROP TABLE IF EXISTS clouds_'.session('vkid').$project_name.'');
                DB::statement('DROP TABLE IF EXISTS tags_'.session('vkid').$project_name.'');
                echo 'Удален проект '.$project.' пользователя '.$vkid."\n";;
        	   }
           }
        }
    }
}
