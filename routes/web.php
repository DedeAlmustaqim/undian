<?php

use App\Http\Controllers\DrawController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\SessionController;
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

Route::get('/', function() {
    return redirect()->route('sessions.index');
});

Route::resource('participants', ParticipantController::class);
Route::resource('sessions', SessionController::class);

// Untuk memulai undian
Route::post('draw/{session}', [DrawController::class, 'start'])->name('draw.start');

// Route untuk monitor display
Route::get('/display/{sessionId}', function ($sessionId) {
    return view('display', ['sessionId' => $sessionId]);
})->name('display');

Route::get('/control', [SessionController::class, 'control'])->name('control');
Route::post('/reroll/{session}', [DrawController::class, 'reroll'])->name('reroll');
