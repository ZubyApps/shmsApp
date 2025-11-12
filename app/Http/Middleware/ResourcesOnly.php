<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourcesOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $designation = $user->designation->access_level;
        
        if ($user && ($user->designation->designation === 'Pharmacy Tech' && $designation > 3) || $user->designation->access_level > 4){
            return $next($request);
        }
        return redirect()->route('login');
    }
}
