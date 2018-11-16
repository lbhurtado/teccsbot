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

use App\{Messenger, Placement};
use App\Events\UserWasRecorded;
use App\Jobs\{RequestOTP, VerifyOTP, SendBotmanMessage, Broadcast};

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Route::get('/test', function () {

  // Bus::dispatch(new App\Jobs\SendUserAccceptedNotification(App\User::find(2), App\User::find(3)));
  
  // App\Jobs\SendUserAccceptedNotification::dispatch(App\User::find(2), App\User::find(3));

  // $messenger = App\Messenger::where([
  //   'channel_id' => '650334894',
  //   'driver' => 'Telegram'
  // ])->first();

  // $user = App\User::find(22);

  // dd($user->descendants);

  // $tree = App\User::get()->toTree();

  // dd($tree);

  // $x = App\User::scoped(['id' => 1])->with('descendants')->has('messengers')->get();

        // $messenger = Messenger::where([
        //     'driver' => 'Web',
        //     'channel_id' => '1541296827288',
        // ])->first();

        // Broadcast::dispatch($messenger->user, 'Yo Job');

  // $admin = App\User::find(29);
  // $users = App\User::defaultOrder()->descendantsAndSelf(29);
  // $users = App\User::defaultOrder()->descendantsOf($admin)->where('messenger','!=',null);

  // Notification::send($users, new App\Notifications\OnDemand('downline of Admin'));
  // dd($users);

  // $node = App\User::create([
  //     'mobile' => '09173011987',
  //     'type' => 'App\Admin',
  //     'children' => [
  //         [
  //             'mobile' => '09178251991',
  //             'type' => 'App\Operator',
  //             'children' => [
  //                 [ 
  //                   'mobile' => '09189362340', 
  //                   'type' => 'App\Staff',
  //                 ],
  //             ],
  //         ],
  //         [
  //             'mobile' => '09088882786',
  //             'type' => 'App\Operator',
  //             'children' => [
  //                 [ 
  //                   'mobile' => '09175180722', 
  //                   'type' => 'App\Worker',
  //                 ],
  //             ],
  //         ],
  //     ],
  // ]);

  // dd($node);

  // $nodes = App\User::get()->toTree();

  // $traverse = function ($categories, $prefix = '-') use (&$traverse) {
  //     foreach ($categories as $category) {
  //         echo PHP_EOL.$prefix.' '.$category->mobile;

  //         $traverse($category->children, $prefix.'-');
  //     }
  // };

  // $traverse($nodes);
 
  $api_key = 'DqG7x_2D6sNvwE5oL9kZB1zrDO3J4i8qRUBL';
  $project_id = 'PJf3e398e4fb9f4a07';

  $this->api = new Telerivet_API($api_key);
    $this->project = $this->api->initProjectById($project_id);

  $content = 'The quick brown fox jumps over the lazy dog.';
  $to_numbers = '639173011987';

  $retval = $this->project->sendMessages(compact('content', 'to_numbers'));

    dd('should not be here!');
});