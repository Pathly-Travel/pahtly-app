<?php

namespace Support\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class IdentifyApplicationContext
{
    public function handle(Request $request, Closure $next)
    {
        $application = Arr::get(explode('.', $request->getHost()), 0);
        if($application === 'crm') {
            Config::set('app.context', 'crm');
        } elseif($application === 'portal') {
            Config::set('app.context', 'portal');
        }

        return $next($request);
    }
}
