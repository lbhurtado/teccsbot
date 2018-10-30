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
use App\Events\UserWasRecorded;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Route::get('/test', function () {

  $messenger = App\Messenger::where([
    'channel_id' => '650334894',
    'driver' => 'Telegram'
  ])->first();

  $user = App\User::withMobile('09178251991')->first();

  // $messenger->user()->associate($user);

  // $messenger->save();

  // $user->refresh();
  // dd($user);

  $user->verify('803407')->refresh();

  dd($user->isVerified());

  // sleep(1);
  // $user->challenge();
  // \App\Jobs\RequestOTP::dispatch($user);
  // sleep(1);


    // $user = \App\Operator::create(['mobile' => '09189262340', 'password' => bcrypt('1234')]);

    // dd($user);


    // event(new UserWasRecorded($user));
    // $user->generatePlacements();
        // event(new UserWasFlagged($user));
        // \App\Jobs\RequestOTP::dispatch($user);
    // \App\Jobs\VerifyOTP::dispatch($user, '182112');


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