<?php

namespace App\Http\Middleware;

use Closure;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $forbidActions = ["POST","PUT","PATCH","DELETE"];

        if(in_array($request->method(),$forbidActions))
        {
            return response(["message"=>"Not allowed to modify data on demo","data"=>[]],401);
        }

        return $next($request);
    }
}
