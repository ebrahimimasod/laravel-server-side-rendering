<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Cache;

class PageController
{
    public function renderPage(Request $request, $page)
    {
        // آماده‌سازی داده‌های صفحه
        $pageData = [
            'title' => ucfirst($page), // عنوان صفحه
            'content' => "This is the content of the $page page.", // محتوای صفحه
            'props' => $request->all(), // سایر پارامترها
        ];



        // بررسی هدر `Accept` برای تشخیص اینکه آیا SSR لازم است یا CSR
        if (Str::contains($request->header('Accept'),'text/html')) {
            // اگر هدر درخواست 'text/html' باشد، درخواست SSR است

            // ایجاد کلید کش برای صفحه
            $cacheKey = 'ssr_' . md5($request->fullUrl() . json_encode($pageData));

            // بررسی اینکه آیا این صفحه در کش وجود دارد یا نه
            $html = Cache::get($cacheKey);

            if (!$html) {
                // اگر کش وجود نداشت، باید SSR اجرا شود
                try {
                    // اجرای SSR توسط Node.js
                    $process = new Process(['/usr/bin/nodejs', resource_path('/js/server.js'), json_encode($pageData)]);
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new \Exception('SSR failed: ' . $process->getErrorOutput());
                    }

                    // خروجی HTML رندر شده
                    $html = $process->getOutput();

                    // ذخیره در کش برای 10 دقیقه
                    Cache::put($cacheKey, $html, now()->addMinutes(10));

                } catch (\Exception $e) {
                   dd($e->getMessage());
                    // اگر SSR شکست خورد، به CSR بازگرد
                    return Inertia::render($page, $pageData);
                }
            }

            // بازگرداندن خروجی SSR
            return response($html);

        } else {
            // اگر درخواست از نوع 'application/json' یا مشابه باشد، به CSR نیاز داریم
            return Inertia::render($page, $pageData);
        }
    }
}
