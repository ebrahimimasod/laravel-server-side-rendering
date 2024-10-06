<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SsrController
{
    public function handleSsrRequest(Request $request, $page)
    {
        // داده‌های درخواست که باید رندر شوند
        $pageData = [
            'page' => $page,
            'props' => $request->all(),
        ];

        // کلید کش براساس URL و داده‌های درخواست
        $cacheKey = 'ssr_' . md5($request->fullUrl() . json_encode($pageData));

        // بررسی کش
        $html = Cache::get($cacheKey);

        if (!$html) {
            try {
                // اجرای فایل جاوااسکریپت با ورودی داده‌های صفحه
                $process = new Process(['node', base_path('path/to/ssr-handler.js'), json_encode($pageData)]);
                $process->run();

                if (!$process->isSuccessful()) {
                    throw new \Exception('SSR failed: ' . $process->getErrorOutput());
                }

                // خروجی HTML رندر شده
                $html = $process->getOutput();

                // ذخیره در کش برای 10 دقیقه
                Cache::put($cacheKey, $html, now()->addMinutes(10));

            } catch (\Exception $e) {
                // ثبت لاگ خطا برای بررسی‌های آینده
                Log::error('SSR failed: ' . $e->getMessage());

                // Fallback به CSR
                return view('app'); // فایل ویویی که برای CSR با Vite استفاده می‌شود
            }
        }

        // بازگرداندن خروجی HTML در صورت موفقیت SSR
        return response($html);
    }
}
