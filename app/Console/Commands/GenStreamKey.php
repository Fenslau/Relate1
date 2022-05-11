<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stream\StreamKey;
use \VK\Client\VKApiClient;

class GenStreamKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:key';

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
      $key = new StreamKey();
      $vk = new VKApiClient();
      $streamkey = $vk->streaming()->getServerUrl(env('ACCESS_TOKEN'),
      array(
        'v' 			=> '5.131'
      ));
      $streamkey['streamkey'] = $streamkey['key'];
      unset($streamkey['key']);
      if (!empty($streamkey['endpoint']) AND !empty($streamkey['streamkey'])) {
        $key->updateOrCreate(['id' => 1], $streamkey);
      }
    }
}
