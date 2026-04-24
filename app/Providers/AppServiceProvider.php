<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomSqlServerConnector;

class AppServiceProvider extends ServiceProvider
{
    

    public function register()
    {
        $this->app->bind('db.connector.sqlsrv', CustomSqlServerConnector::class);
    }
    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
