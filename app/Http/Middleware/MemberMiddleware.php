<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Guards the member front-end: the login must own a member record. */
class MemberMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->isMember()) {
            abort_unless($request->user()->isStaff(), 403);

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
