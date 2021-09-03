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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Parse:Groups')->monthlyOn(4, '15:00')->withoutOverlapping()->runInBackground()->sendOutputTo('storage/logs/ParseGroups.log');

        $schedule->command('Top1000:get')->everyFiveMinutes()->withoutOverlapping()->runInBackground()->sendOutputTo('storage/logs/Top1000.log');
        $schedule->command('Top1000date:get')->everySixHours()->withoutOverlapping()->runInBackground()->sendOutputTo('storage/logs/Top1000date.log');

        $schedule->command('Help:hour')->hourly()->withoutOverlapping()->runInBackground()->sendOutputTo('storage/logs/HelperHour.log');
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
