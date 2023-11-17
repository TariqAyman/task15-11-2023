<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user() && auth()->user()->user_type == 'admin') {
            return $next($request);
        }

        return abort(Response::HTTP_FORBIDDEN,'YOU SHOULD BE ADMIN TO ACCESS THIS PAGE');
    }
}
