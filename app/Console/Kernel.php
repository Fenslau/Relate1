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
        Commands\Dublikat::class,
        Commands\Cloud::class,
        Commands\File::class,
        Commands\Fulltext::class,
        Commands\OldPost::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Stream:get')->everyMinute()->withoutOverlapping(365*24*60)->runInBackground()->appendOutputTo('storage/logs/Stream.log');

        $schedule->command('Parse:Groups')->monthlyOn(10, '06:00')->withoutOverlapping(32*24*60)->runInBackground()->appendOutputTo('storage/logs/ParseGroups.log');

        $schedule->command('Top1000date:get')->everySixHours()->withoutOverlapping()->appendOutputTo('storage/logs/Top1000date.log');
        $schedule->command('Top1000:get')->everyTenMinutes()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Top1000.log');


        $schedule->command('Help:hour')->hourly()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/HelperHour.log');

        $schedule->command('City:database')->quarterly()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/CityDatabase.log');
        $schedule->command('fulltext:rebuild')->quarterly()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Fulltext.log');

        $schedule->command('model:prune')->daily()->runInBackground()->appendOutputTo('storage/logs/ModelsPrune.log');



        $schedule->command('Authors:Get')->everyMinute()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/Authors.log');

        $schedule->command('File:get')->everyMinute()->withoutOverlapping()->runInBackground()->appendOutputTo('storage/logs/FileXLS.log');

        $schedule->command('old:post')->everyMinute()->withoutOverlapping()->appendOutputTo('storage/logs/OldPosts.log');

        $schedule->command('Cloud:get')->hourly()->withoutOverlapping()->appendOutputTo('storage/logs/Cloud.log');

        $schedule->command('Dublikat:find')->everyMinute()->withoutOverlapping(365*24*60)->runInBackground()->appendOutputTo('storage/logs/Dublikats.log');

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
