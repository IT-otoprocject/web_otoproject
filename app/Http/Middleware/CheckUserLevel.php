<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle($request, Closure $next, $level)
     {
         if (auth()->user()->level !== $level) {
             return redirect('/'); // Arahkan ke halaman yang sesuai jika level tidak cocok
         }
     
         return $next($request);
     }
     


    // Bawaan asli -------------------------

    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
}
