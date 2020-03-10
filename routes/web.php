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

use App\Message;
use App\Events\MessageSent;


Auth::routes();
Route::group(['middleware' => ['auth']], function () {
Route::get('/', function () {
    return view('welcome');
});

Route::get('/getAll', function () {
    $newMessage = [];
    $messages = Message::take(200)->get();
    foreach ($messages as $message) {
        $newMessage[] = $message['content'] . ' - ' . $message->user->name;
    }
    return $newMessage;
});

Route::post('/post', function () {

    $message = new Message();
    $content = request('message');
    $message->content = $content;
    $message->user_id = \Auth::user()->id;
    $message->save();
    event(new MessageSent($content, \Auth::user()->name));
    $details['email'] = \Auth::user()->email;
    dispatch(new App\Jobs\SendEmailJob($details));
    return $content;
});
});
Route::get('/home', 'HomeController@index')->name('home');
