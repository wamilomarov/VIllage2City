<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 18-Dec-17
 * Time: 03:09
 */

namespace App\Http\Middleware;


use Closure;

class Vendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->getTable() != 'staff_members')
        {
            return response()->json(['status' => 401, 'data' => $request->user()]);
        }
        return $next($request);
    }
}