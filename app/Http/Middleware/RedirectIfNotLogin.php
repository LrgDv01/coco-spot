<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     if (Auth::check()) {

    //         if (!Auth::user()->isAdmin() || !Auth::user()->isFarmer() || !Auth::user()->isTechnician()) {
    //             // return response()->view('errors.no-access', [], 403);
    //             // If authenticated but no valid role
    //             return redirect()->route('welcome');
    //         }
    //         else {
    //             $user = Auth::user();
    //             if ($user->isAdmin()) {
    //                 return redirect()->route('admin.dashboard');
    //             } elseif ($user->isFarmer()) {
    //                 return redirect()->route('user.farm');
    //             } elseif ($user->isTechnician()) {
    //                 return redirect()->route('user.notifications');
    //             }
    //         }
    //     } 

    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin() || Auth::user()->isFarmer() || Auth::user()->isTechnician()) {
                // return response()->view('errors.no-access', [], 403);
                $user = Auth::user();
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->isFarmer()) {
                    return redirect()->route('user.farm');
                } elseif ($user->isTechnician()) {
                    return redirect()->route('user.notifications');
                }
            }
            else {
                return redirect()->route('welcome');
            }
        } 
        return $next($request);
    }
}
