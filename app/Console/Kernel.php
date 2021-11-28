<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ParseGroups::class,
        Commands\Top1000::class,
        Commands\Top1000date::class,
        Commands\HelperHour::class,
        Commands\Stream::class,
        Commands\City::class,
        Commands\AuthorGet::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      //  $schedule->command('Stream:get')->everyMinute()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Stream.log');

        $schedule->command('Parse:Groups')->monthlyOn(4, '15:00')->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/ParseGroups.log');

        $schedule->command('Top1000:get')->everyTenMinutes()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Top1000.log');
        $schedule->command('Top1000date:get')->everySixHours()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Top1000date.log');

        $schedule->command('Help:hour')->hourly()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/HelperHour.log');

        $schedule->command('City:database')->quarterly()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/CityDatabase.log');

        $schedule->command('model:prune')->daily()->appendOutputTo('storage/logs/ModelsPrune.log');



        $schedule->command('Authors:Get')->everyMinute()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Authors.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
