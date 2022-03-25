<?php

namespace ThutaYarMoe\Tweets;

use Illuminate\Support\ServiceProvider;

class TweetServiceProvider extends ServiceProvider {
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton(TweetService::class, function() {
            return new TweetService();
        });
    }
}
