<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Placement;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Route::get('/test', function () {

    $user = \App\Operator::find(4);
    // dd($user);
        // event(new UserWasFlagged($user));
        // \App\Jobs\RequestOTP::dispatch($user);
    \App\Jobs\VerifyOTP::dispatch($user, '182112');


    // $user->notify(new PhoneVerification('sms', true));

    // if (validate($mobile)) {

    //     dd(trans('registration.input.mobile'));

		// $code = 'operator';

  //       $attributes = [
  //           'mobile' => '09178251991',
  //           // 'name' => 'Test User',
  //       ];

  //       optional(Placement::activate($code, $attributes), function($model) {

  //           dd($model);
  //       });

    // }

    dd('should not be here!');
});