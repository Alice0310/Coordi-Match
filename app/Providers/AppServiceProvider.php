<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            // すべてのビューで未読通知数を共有
    View::composer('*', function ($view) {
        $count = 0;
        if (Auth::check()) {
            // Notification モデルを使って未読件数を取得
            $count = \App\Models\Notification::where('user_id', Auth::id())
                ->where('is_read', false) // read_at 方式なら ->whereNull('read_at')
                ->count();
        }
        $view->with('unreadCount', $count);
    });
    }
}
