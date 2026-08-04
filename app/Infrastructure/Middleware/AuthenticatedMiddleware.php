<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        // return $request->expectsJson() ? null : route('auth.view.login');
        if (!Auth::guard('api')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session has expired. Please login again.'
                ], 401);
            }
            return redirect()
                ->route('auth.view.login')
                ->with('error', 'Silahkan login terlebih dahulu.');
        }

        return $next($request);
    }
}
