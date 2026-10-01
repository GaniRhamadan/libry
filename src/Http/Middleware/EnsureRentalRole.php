<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRentalRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('rental.login'));
        }

        $userRole = (string) ($user->rental_role ?? 'customer');

        if ($userRole !== $role) {
            abort(403, (string) __('rental-hub::rental.unauthorized_role_access'));
        }

        return $next($request);
    }
}
