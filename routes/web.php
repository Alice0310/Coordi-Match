<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StylistController;

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