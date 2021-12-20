<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Fulltext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fulltext:rebuild {--c|connection}';

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
    public function handle() {
      $connection = $this->option('connection');

      $schema = \DB::connection($connection)->getDatabaseName();

      $result = \DB::select("SELECT index_name AS `index`, group_concat(column_name) AS `columns`, TABLE_NAME AS `table`
                             FROM information_Schema.STATISTICS
                             WHERE table_schema = ?
                             AND index_type = 'FULLTEXT'
                             GROUP BY `index`, `table`"
          , [$schema]
      );

      $bar = $this->output->createProgressBar(count($result));

      foreach ($result as $item) {

          \DB::unprepared("ALTER TABLE $item->table DROP INDEX $item->index");

          $columnNames = explode(',', $item->columns);
          foreach ($columnNames as &$name) {
              $name = '`' . $name . '`';
          }
          $columnNames = implode(',', $columnNames);

          \DB::unprepared("ALTER TABLE $item->table ADD FULLTEXT $item->index ($columnNames)");
          $bar->advance();
      }

      $bar->finish();

      $this->info(PHP_EOL . 'Fulltext indexes were rebuild successfully');
    }
}
