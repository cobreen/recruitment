<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductsStoreRequest;
use App\Jobs\MakeYml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class YmlController extends Controller
{
    public function SaveAndEnqueue(ProductsStoreRequest $request) {
        $id = uniqid();
        MakeYml::dispatch($request->input('products'), $id);
        return response()->json([
            "status" => "success",
            "token" => $id
        ]);
    }

    public function GiveResult(Request $request) {
        $filepath = 'ymls/' . $request->input("token") . '_parsed.yml';
        if (Storage::exists($filepath)) {
            return Storage::download($filepath, "Result.yml");
        } else {
            return response()->json([
                "status" => "not ready"
            ]);
        }
    }
}
