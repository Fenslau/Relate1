<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      setlocale(LC_ALL, 'ru_RU.UTF-8');
      DB::statement("SET lc_time_names = 'ru_RU'");
      Carbon::setLocale(config('app.locale'));
      Paginator::useBootstrap();

      Blade::directive('dec', function ($expression) {
        return "<?php echo number_format((float)$expression, 0, ',', ' '); ?>";
      });
    }
}
