<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the admin panel. A route opens only when the user's role has a
 * permission that lists it; the dashboard opens for all staff.
 */
class AdminAccessMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $route = (string) $request->route()?->getName();

        if (! $user->isStaff()) {
            if ($route === 'dashboard' && $user->isMember()) {
                return redirect()->route('portal.home');
            }

            abort(403);
        }

        abort_unless($route === 'dashboard' || $user->canAccess($route), 403);

        return $next($request);
    }
}
