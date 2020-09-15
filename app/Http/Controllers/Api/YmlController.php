<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductsStoreRequest;
use App\Jobs\MakeYml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\QueueState;

class YmlController extends Controller
{
    public function SaveAndEnqueue(ProductsStoreRequest $request) {
        $id = uniqid();
        $queueState = new QueueState();
        $queueState->mode = 0;
        $queueState->data = json_encode($request->input('products'));
        $queueState->file_id = $id;
        $queueState->save();
        MakeYml::dispatch($queueState->id);
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
            $queueState = QueueState::where("file_id", $request->input("token"))->first();
            $message = "";
            switch ($queueState->mode) {
                case -1:
                    $message = "Paring failed. Trying again";
                break;
                case 0:
                    $message = "Paring queued";
                break;
                case 1:
                    $message = "Paring is in progress";
                break;
                case 2:
                    $message = "Parsing was completed, but file is missing";
                break;
            }
            return response()->json([
                "status" => "not ready",
                "message" => $message
            ]);
        }
    }
}
