<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateRental
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null) {
            $namePrefix = (string) config('rental-hub.route_name_prefix', 'rental.');
            $loginRoute = $namePrefix . 'login';

            return redirect()->guest(route($loginRoute));
        }

        return $next($request);
    }
}
