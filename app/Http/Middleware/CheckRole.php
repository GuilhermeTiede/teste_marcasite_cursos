<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!Auth::check()) {
            // Redireciona para login ou outra página se o usuário não estiver autenticado
            return redirect()->route('login');
        }

        // Log para verificar qual usuário está sendo processado
        \Log::info('Middleware CheckRole chamado para o usuário: ' . Auth::user()->email);

        if (Auth::user()->role !== $role) {
            // Redireciona ou retorna uma resposta de erro se o papel do usuário não for o esperado
            return redirect('/')->with('error', 'Você não tem permissão para acessar essa página.');
        }

        return $next($request);
    }
}
