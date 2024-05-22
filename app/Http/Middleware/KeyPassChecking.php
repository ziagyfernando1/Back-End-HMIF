<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KeyPassChecking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->headers;
        $keyPass = $headers->get('key_pass');
        if($keyPass != "punyaHMIF"){
            return response()->json("Key Pass Failed", 400);
        };
        return $next($request);
    }
}
