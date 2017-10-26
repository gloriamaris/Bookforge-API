<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/users',  [
	'uses' => 'Auth\UserController@signup'
])->middleware('validateClientRequest');  

Route::post('/authorization',  [
	'uses' => 'Auth\UserController@login'
])->middleware('validateClientRequest'); 

Route::post('/subscriptions', [
	'uses' => 'SubscriptionController@createSubscription'
]); 

Route::put('/subscriptions', [
	'uses' => 'SubscriptionController@upgradeSubscription'
]); 

Route::get('/subscriptions', [
	'uses' => 'SubscriptionController@getInvoice'
]); 

Route::delete('/subscriptions', [
	'uses' => 'SubscriptionController@cancelSubscription'
]); 

Route::post('webhook/stripe', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');