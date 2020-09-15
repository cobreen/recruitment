<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class apiKeyValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (ApiKey::where("key", $request->input('key'))->first()) {
            return $next($request);
        }
        return response()->json([
            "status" => "error",
            "message" => "Invalid api key"
        ]);
    }
}
