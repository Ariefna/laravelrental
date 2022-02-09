<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::get('role') == 'administrator') {
            // if (Session::get('role') == 'admin') {
                // echo Session::get('role');
            return $next($request);
            // }
        }
        else {
            return redirect()->back()->with('failed', 'Maaf, Anda Tidak Ada Akses Ke Halaman Tertentu');
            }
    return redirect('/login');
    }
}
