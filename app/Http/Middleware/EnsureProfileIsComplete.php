<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Redirect users without a filled-in body profile to the profile form:
     * norms and recommendations cannot be computed without sex, age and height.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->profile) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}
