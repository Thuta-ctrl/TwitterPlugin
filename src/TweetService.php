<?php

namespace ThutaYarMoe\Tweets;
use Illuminate\Support\Facades\Http;

class TweetService {
    public function get_tweets($users) {
        $tweets = [];
        foreach ($users as $key => $value) {
            try {
                $response = Http::withHeaders([
                    'Content-type' => 'application/json',
                    'Authorization' => 'Bearer ' . $value['token']
                ])->get('https://api.twitter.com/2/users/'.$value['id'].'/tweets', [
                    'tweet.fields' => 'created_at,author_id,lang,source,public_metrics,context_annotations,entities',
                    'max_results' => 5,
                    'user.fields' => 'created_at,name,profile_image_url',
                    'expansions' => 'author_id,attachments.media_keys',
                    'media.fields' => 'preview_image_url,type,url'
                ]);
                array_push($tweets, $response->json());
            } catch (\Exception $e) {
                throw $e;
            }
        }
        $collection = [];
        foreach ($tweets as $value) {
            array_push($collection, $this->prepare_tweets($value));
        }

        $collection = array_merge(...$collection);

        $sorted = collect($collection)->sortByDesc('created_at');

        return $sorted->take(5)->values()->all();
    }

    public function prepare_tweets($tweets) {
        $main = $tweets['data'];
        foreach ($main as $key => $value) {
            $main[$key]['link'] = 'https://twitter.com/spiceworksmm/status/'.$value['id'];
            $main[$key]['user'] = $tweets['includes']['users'][0];
            $main[$key]['images'] = !empty($value['attachments']) ? $this->get_images($value['attachments']['media_keys'], $tweets['includes']['media']): null;
        }

        return $main;
    }

    public function get_images($keys, $images) {
        $img = [];
        foreach ($keys as $key => $value) {
          $image = $images[array_search($value, array_column($images, 'media_key'))];
          if ($image['type'] == 'photo') {
            array_push($img, $image['url']);
          } else if ($image['type'] == 'video') {
            array_push($img, $image['preview_image_url']);
          }
        }
        return $img;
    }
}
