<?php

use App\Http\Controllers\PurchaseOrderController;
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

//All requests send through SANCTUM middleware
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/test', [PurchaseOrderController::class, 'purchaseOrderTotals']);
});


/*
 * sample way to create a TOKEN for SANCTUM.
 * change this to web route when you integrate this as an FEATURE for generating TOKENS for THE CARTONCLOUD CLIENTS

Route::get('/user', function (Request $request) {
    $user = \App\Models\User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
//    return $request->user();
});

//*/


