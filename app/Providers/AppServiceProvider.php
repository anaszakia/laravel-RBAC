<?php

namespace App\Providers;

use App\Services\MinioService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan MinioService sebagai singleton
        $this->app->singleton(MinioService::class, function ($app) {
            return new MinioService();
        });

        // Alias agar bisa dipanggil dengan 'minio'
        $this->app->alias(MinioService::class, 'minio');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register S3 driver support untuk Flysystem
        // Ini memastikan AWS S3Client tersedia saat filesystem manager diakses
        \Illuminate\Support\Facades\Storage::resolved(function ($storage) {
            // S3 driver otomatis diload oleh League\Flysystem\AwsS3V3
        });
    }
}
