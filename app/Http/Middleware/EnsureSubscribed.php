<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {if ($request->user() && ! $request->user()->subscribed('default')) {
        return response()->json(['message' => 'Előfizetés szükséges a funkció használatához.'], 402);
    }
    return $next($request);
    }
}
