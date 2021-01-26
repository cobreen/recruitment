<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cart;

class ApiCartController extends Controller
{
    public function get (Request $request) {
        return Cart::where('user_id', $request->user('api')->id)->with('product')->get();
    }
    
    public function add (Request $request) {
        $cart = Cart::firstOrNew([
            ['user_id', $request->user('api')->id],
            ['product_id', $request->input('id')],
        ]);
        if (!$cart->exists) {
            $cart->user_id = $request->user('api')->id;
            $cart->product_id = $request->input('id');
            $cart->ammount = 0;
        }
        $cart->ammount += 1;
        $cart->save();

        $cart->product;//product relation linking

        return $cart;
    }

    public function delete (Request $request) {
        Cart::where([
            ['user_id', $request->user('api')->id],
            ['product_id', $request->input('id')],
        ])->delete();
        return $this->get($request);
    }
}
