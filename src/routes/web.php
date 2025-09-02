<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'store'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [ShopController::class, 'index'])->name('index');
Route::get('/search', [ShopController::class, 'search'])->name('search');
Route::get('/detail/{shop_id}', [ShopController::class, 'detail'])->name('detail');
Route::get('/detail/{shop_id}/times', [ShopController::class, 'getTimes']);

Route::patch('/like/{shop_id}', [UserController::class, 'like'])->name('like');
Route::post('/done', [UserController::class, 'reservation'])->name('reservation');
Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
Route::patch('/mypage/update/{reservation_id}', [UserController::class, 'update'])->name('mypage-update');
Route::get('/mypage/update/{reservation_id}/times', [UserController::class, 'getTimesForUpdate']);
Route::delete('/mypage/delete/{reservation_id}', [UserController::class, 'destroy'])->name('mypage-delete');
Route::get('/review/{reservation_id}', [UserController::class, 'create'])->name('review');
Route::post('/review/post/{reservation_id}', [UserController::class, 'store'])->name('review-post');
Route::get('/reservation/{reservation_id}/qr', [UserController::class, 'showQr'])->name('reservation-qr');

Route::get('/owner', [OwnerController::class, 'index'])->name('owner-index');
Route::get('/owner/show/{shop_id}', [OwnerController::class, 'show'])->name('owner-show');
Route::get('/owner/checkin/{checkin_token}', [OwnerController::class, 'checkin'])->name('checkin');