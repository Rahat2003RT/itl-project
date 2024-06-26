<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Получаем текущего пользователя
        $user = Auth::user();

        // Проверяем, аутентифицирован ли пользователь
        if (!$user) {
            return redirect()->route('login');
        }

        // Проверяем, есть ли у пользователя одна из нужных ролей
        if (!in_array($user->role, $roles)) {
            // Возвращаем ошибку или перенаправляем на другую страницу, если роль не соответствует
            return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
