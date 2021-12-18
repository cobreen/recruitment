<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class TweetController extends Controller
{
    public function createTweet(Request $request) {
        $this->validate($request, [
            'text'          =>'required|string|max:140',
        ]);

        $tweet = Tweet::create([
            'user_id'   => auth()->user()->id,
            'text'      => $request->text
        ]);

        return \Response::json(['status' => 'ok', 'id' => $tweet->id]);
    }

    public function userTweets($userId) {
        //should be paginated
        $query = Tweet::where('user_id', $userId)->with('user')->get();
        $map = $query->map(function($item){
            $data['id']         = $item->id;
            $data['text']       = $item->text;
            $data['user_image'] = $item->user->image_path;
            $data['created_at'] = $item->created_at;
            return $data;
        });
        return $map;
    }

    public function randomTweet() {
        $tweet = Tweet::inRandomOrder()->with('user')->first();
        return [
            'id'            => $tweet->id,
            'text'          => $tweet->text,
            'user_image'    => $tweet->user->image_path,
            'created_at'    => $tweet->created_at
        ];
    }
}
