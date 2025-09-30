<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StylistController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ClothesController;

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/register', function() {
    return view('auth.register');
});

Route::get('/login', function() {
    return view('auth.login');
});

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

//ログイン後トップページへ
Route::get('/index', [App\Http\Controllers\IndexController::class, 'index'])->name('index');

//ログアウト
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('index'); // ログアウト後にトップページへ
})->name('logout');

//プロフィール
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::post('/profile/update', [UserProfileController::class, 'update'])->name('user.profile.update');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    // スタイリスト登録ページ表示（既存情報も表示）
    Route::get('/become', [StylistController::class, 'become'])->name('become.stylist');

    // スタイリスト情報保存・更新（updateOrCreate）
    Route::post('/become', [StylistController::class, 'storeOrUpdate'])->name('stylists.store_or_update');
});

// スタイリスト一覧
Route::get('/stylist/list', [StylistController::class, 'list'])->name('stylist.list');
Route::get('/stylist/become', [StylistController::class, 'become'])->name('become.stylist');

// 詳細ページ
Route::get('/stylist/{id}', [StylistController::class, 'detail'])->name('stylist.detail');

Route::middleware('auth')->group(function () {
    // コメント関連
    Route::post('/stylists/{id}/comment', [CommentController::class, 'store'])
        ->name('stylist.comment');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // 取引関連
    Route::post('/trade/request/{id}', [TradeController::class, 'request'])
        ->name('trade.request');

    // 通知関連
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unreadCount');
});

// 取引ページ表示
Route::middleware('auth')->group(function () {
    Route::post('/trade/request/{id}', [TradeController::class, 'request'])
        ->name('trade.request');
    Route::get('/trades/{id}', [TradeController::class, 'show'])
        ->name('trades.show');
});

Route::middleware('auth')->group(function () {
    Route::post('/transactions/{id}/approve', [TradeController::class, 'approve'])
        ->name('transactions.approve');
});

// メッセージ機能
Route::middleware('auth')->group(function () {
    Route::post('/trades/{id}/message', [TradeController::class, 'sendMessage'])
        ->name('trades.sendMessage');
});

Route::post('/trades/{id}/message', [TradeController::class, 'sendMessage'])->name('trades.sendMessage');

// マイページ
Route::get('/mypage', function () {
    return view('mypage.index');
})->name('mypage.index');

Route::middleware('auth')->group(function () {
    Route::get('/clothes', [ClothesController::class, 'index'])->name('clothes.index');
    Route::post('/clothes', [ClothesController::class, 'store'])->name('clothes.store');
});

Route::get('/users/{id}/clothes', [App\Http\Controllers\ClothesController::class, 'showUserClothes'])
    ->name('clothes.user');

// 持ち服画像削除
Route::delete('/clothes/{id}', [ClothesController::class, 'destroy'])->name('clothes.destroy');

// 終了ボタン
Route::post('/trades/{id}/request-complete', [TradeController::class, 'requestComplete'])->name('trades.requestComplete');
Route::post('/trades/{id}/confirm-complete', [TradeController::class, 'confirmComplete'])->name('trades.confirmComplete');

// 終了通知（スタイリスト）
Route::post('/trades/{id}/complete-request', [TradeController::class, 'requestComplete'])
    ->name('trades.requestComplete');

Route::get('/stylists/{id}', [StylistController::class, 'show'])->name('stylists.show');