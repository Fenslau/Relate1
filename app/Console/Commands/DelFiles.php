<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\MyClasses\VKUser;
use App\Models\Oplata;

class DelFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Del:Files';

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
      $path = public_path('storage');
      $files = \File::allFiles($path);
      $users = Oplata::where('date', '>', now()->subDays(7))->latest()->get()->toArray();
      foreach ($files as $file) {
        $file = str_replace($path, '', $file);
        $flag = FALSE;
        foreach (array_column($users, 'vkid') as $vkid) {
          if (strpos($file, $vkid) !== FALSE) $flag = TRUE;
        }
        if ($flag === FALSE) unlink($path.$file);
      }
    }
}
