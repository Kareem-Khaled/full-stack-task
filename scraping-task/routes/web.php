<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\ScrapingController;

Route::get('/scrape', [ScrapingController::class, 'scrapeData'])->name('scrape');

Route::get('/data', [ScrapingController::class, 'getData'])->name('data');

Route::get('/', function () {
    return view('welcome');
});