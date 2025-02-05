<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (auth()->user()->tokenCan('server:admin')) {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'Bạn không có quyền truy cập!'
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Vui lòng đăng nhập!'
            ]);
        }
    }
}
