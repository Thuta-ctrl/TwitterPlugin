#Twitter Api Package

#How to use

```
<?php

use Thutayarmoe\Tweets\TweetService;

Route::get('/', function (TweetService $service) {
    $users = [
        [
            'id'=> 'id',
            'token'=> 'token'
        ],
    ];

    $tweets = $service->get_tweets($users);
    return $tweets;
});
```
