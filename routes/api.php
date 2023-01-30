<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('code', [AuthController::class, 'code']);

Route::prefix('projects')->group(function () {
    route::post('/', [ProjectController::class, 'store'])->name('store');
    route::get('/', [ProjectController::class, 'all']);
});

Route::prefix('leads')->group(function () {
    route::post('/', [LeadController::class, 'store'])->name('store');
    route::get('/', [LeadController::class, 'all'])->name('all');
    route::post('/follow-up', [LeadController::class, 'storeFollowUp'])->name('follow-up');
    route::post('/visit', [LeadController::class, 'storeVisit'])->name('visit');
    route::post('/already-visit', [LeadController::class, 'storeAlreadyVisit'])->name('already-visit');
    route::post('/reservation', [LeadController::class, 'storeReservation'])->name('reservation');
    route::post('/booking', [LeadController::class, 'storeBooking'])->name('booking');
    route::post('/sold', [LeadController::class, 'storeSold'])->name('sold');
});

Route::prefix('homes')->group(function () {
    route::get('/', [HomeController::class, 'all'])->name('all');
    route::post('/', [HomeController::class, 'store'])->name('store');
});


route::prefix('visits')->group(function () {
    route::get('/', [VisitController::class, 'all'])->name('all');
    route::post('/reschedule', [VisitController::class, 'reschedule'])->name('reschedule');
});
