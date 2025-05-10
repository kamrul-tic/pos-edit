<?php

namespace App\Http\Middleware;

use App\Traits\ENVFilePutContent;
use Closure;
use Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CheckInstall
{
    use ENVFilePutContent;

    public function handle($request, Closure $next)
    {
        if(empty(env('LANDLORD_DB'))) {
            return redirect()->route('saas-install-step-1');
        }

        return $next($request);
    }
}
