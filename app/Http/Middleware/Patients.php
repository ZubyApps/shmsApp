<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Patients
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user && $user->designation->designation !== 'Doctor' && $user->designation->designation !== 'Nurse' && $user->designation->designation !== 'Bill Officer' && $user->designation->designation !== 'HMO Officer' && $user->designation->designation !== 'Records Clerk' && $user->designation->access_level < 5){
            return redirect()->route('login');
        }
        return $next($request);
    }
}
