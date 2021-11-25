<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use \App\MyClasses\MyRules;

class RulesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
      // View::composer(['streaming.posts'], function($view) {
      //       $view->with(['cut' => MyRules::getCut(), 'projects' => MyRules::getProjects(), 'rules' => MyRules::getRules(), 'old_rules' => MyRules::getOldRules(), 'links' => MyRules::getLinks()]);
      // });
    }
}
