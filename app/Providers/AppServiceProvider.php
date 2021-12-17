<?php

namespace App\Providers;

use File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(File::exists(base_path('vendor/ajifatur/faturhelper/src'))){
            foreach(glob(base_path('vendor/ajifatur/faturhelper/src').'/HelpersExt/*.php') as $filename){
                require_once $filename;
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
