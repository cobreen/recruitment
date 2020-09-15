<?php

namespace App\Http\Controllers\Api;

use App\Models\QueueState;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class QueueStateController extends Controller
{
    public function getAll ($token) {
        return response()->json(["products" => json_decode(QueueState::where("file_id", $token)->first()->data) ?? null]);
    }

    public function getProduct ($token, $product_id) {
        return response()->json(["product" => json_decode(QueueState::where("file_id", $token)->first()->data)[$product_id] ?? null]);
    }

    public function getProductAttribute ($token, $product_id, $attribute_name) {
        return response()->json([$attribute_name => json_decode(QueueState::where("file_id", $token)->first()->data)[$product_id]->{$attribute_name} ?? null]);
    }

    public function addProduct (Request $request, $token) {
        $state = QueueState::where("file_id", $token)->first();
        if (!$state) {
            return response()->json([
                "status" => "error",
                "message" => "Queue not found"
            ]);
        }
        $data = json_decode($state->data);

        $validator = Validator::make($request->input(), [
            "name" => ["required"],
            "price" => ["required", "numeric"],
            "category" => ["required"],
            "image" => ["required", "active_url"],
        ]);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json($validator->errors(), 422));
        }
        $data[] = [
            "name" => $request->input('name'),
            "price" => $request->input('price'),
            "category" => $request->input('category'),
            "image" => $request->input('image'),
        ];
        $state->data = json_encode($data);
        $state->save();
        return response()->json(["status" => "Success"]);
    }

    public function updateProductAttribute ($token, $product_id, $attribute_name, $value) {
        $validator = null;
        if ($attribute_name == "price") {
            $validator = Validator::make([
                "value" => $value
            ],
            [
                "value" => ["required", "numeric"]
            ]);
        } else if ($attribute_name == "image") {
            $validator = Validator::make([
                "value" => $value
            ],
            [
                "value" => ["required", "active_url"]
            ]);
        } else {
            $validator = Validator::make([
                "value" => $value
            ],
            [
                "value" => ["required"]
            ]);
        }
        if ($validator->fails() || !in_array($attribute_name, ["name", "price", "category", "image"])) {
            throw new HttpResponseException(response()->json([
                "status" => "error",
                "message" => "invalid data"
            ], 422));
        }
        $state = QueueState::where("file_id", $token)->first();
        if (!$state) {
            return response()->json([
                "status" => "error",
                "message" => "Queue not found"
            ]);
        }
        $data = json_decode($state->data);
        if (!isset($data[$product_id])) {
            return response()->json([
                "status" => "error",
                "message" => "Product not found"
            ]);
        }
        $data[$product_id]->{$attribute_name} = $value;
        $state->data = json_encode($data);
        $state->save();
        return response()->json(["status" => "Success"]);
    }
    
    public function dropProduct ($token, $product_id) {
        $state = QueueState::where("file_id", $token)->first();
        if (!$state) {
            return response()->json([
                "status" => "error",
                "message" => "Queue not found"
            ]);
        }
        $data = json_decode($state->data);
        if (!isset($data[$product_id])) {
            return response()->json([
                "status" => "error",
                "message" => "Product not found"
            ]);
        }
        unset($data[$product_id]);
        $state->data = json_encode($data);
        $state->save();
        return response()->json(["status" => "Success"]);
    }
}
