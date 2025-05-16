<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

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
 

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/skills', [App\Http\Controllers\JobsController::class, 'getAllSkills']);
    Route::get('/companies', [App\Http\Controllers\JobsController::class, 'getAllCompanies']);
    Route::get('/jobtypes', [App\Http\Controllers\JobsController::class, 'getAllJobTypes']);
    Route::get('/jobcategories', [App\Http\Controllers\JobsController::class, 'getAllJobCategories']);
});

Route::middleware('auth:sanctum')->post('/skills', [App\Http\Controllers\JobsController::class, 'addSkill']);

Route::middleware('throttle:10,1')->post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
});