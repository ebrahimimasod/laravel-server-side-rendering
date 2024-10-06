<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectSsrMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // بررسی هدر Accept برای تشخیص درخواست SSR یا CSR
        $acceptHeader = $request->header('Accept');

        // اگر هدر شامل 'text/html' باشد، یعنی درخواست از نوع SSR است (درخواست اول)
        if (str_contains($acceptHeader, 'text/html')) {
            // اجرای SSR و ریدایرکت به کنترلر SSR
            return redirect()->route('ssr.page', ['page' => $request->path()]);
        }

        // در غیر این صورت، CSR توسط Inertia.js مدیریت خواهد شد
        return $next($request);
    }
}
