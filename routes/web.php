<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'App\Http\Controllers\MainController@main')
->name('home');

Route::post('/', 'App\Http\Controllers\MainController@search')
->name('search');

Route::post('/groupsearch', 'App\Http\Controllers\GroupSearchController@search')
->name('groupsearch');

Route::post('/auditoria', 'App\Http\Controllers\AuditoriaSearchController@search')
->name('auditoria');

Route::get('/download/{filename}', 'App\Http\Controllers\DownloadController@download')
->name('download');

Route::post('/progress', 'App\Http\Controllers\ProgressController@getProgress')
->name('progress');

Route::get('/stat', function () {
    return view('stat');
})->name('stat');

Route::post('/stat', 'App\Http\Controllers\VisitorsStatController@getStat')
->name('stat');

Route::get('/tarifs', function () {
    return view('tarifs');
})->name('tarifs');

Route::get('/groupsearch', function () {
    session()->flash('previous-route', Route::current()->getName());
    return view('groupsearch');
})->name('groupsearch');

Route::get('/auditoria', function () {
    session()->flash('previous-route', Route::current()->getName());
    return view('auditoria');
})->name('auditoria');

Route::get('/getusers', function () {
    session()->flash('previous-route', Route::current()->getName());
    return view('getusers');
})->name('getusers');

Route::post('/getusers', 'App\Http\Controllers\GetUsersController@main')
->name('getusers');

Route::get('/new-users', 'App\Http\Controllers\NewUsersController@main')
->name('new-users');

Route::post('/new-users', 'App\Http\Controllers\NewUsersController@add')
->name('new-users');

Route::post('/follow-group', 'App\Http\Controllers\NewUsersController@follow')
->name('follow-group');

Route::post('/del-follow-group', 'App\Http\Controllers\NewUsersController@del')
->name('del-follow-group');

Route::get('/stream', 'App\Http\Controllers\Stream\StreamController@main')
->name('stream');
Route::post('/stream/gen-key', 'App\Http\Controllers\Stream\StreamController@gen')
->name('stream-gen-key');
Route::post('/stream/stream-fake-vkid', 'App\Http\Controllers\Stream\StreamController@fakeVKID')
->name('stream-fake-vkid');

Route::post('/stream/link-add', 'App\Http\Controllers\Stream\UserlinkController@add')
->name('link-add');
Route::post('/stream/link-del', 'App\Http\Controllers\Stream\UserlinkController@del')
->name('link-del');

Route::post('/stream/rule-add', 'App\Http\Controllers\Stream\RuleController@add')
->name('rule-add');
Route::post('/stream/rule-edit', 'App\Http\Controllers\Stream\RuleController@edit')
->name('rule-edit');
Route::post('/stream/rule-del', 'App\Http\Controllers\Stream\RuleController@del')
->name('rule-del');

Route::post('/stream/add-project', 'App\Http\Controllers\Stream\ProjectController@add')
->name('stream-add-project');
Route::post('/stream/del-project', 'App\Http\Controllers\Stream\ProjectController@del')
->name('stream-del-project');

Route::get('/stream/{project_name}', 'App\Http\Controllers\Stream\PostController@main')
->name('post');

Route::post('/stream/save-file', 'App\Http\Controllers\Stream\SaveFileController@main')
->name('save-file');

Route::post('/stream/ruleErasePosts', 'App\Http\Controllers\Stream\StreamButtonsController@ruleErasePosts')
->name('ruleErasePosts');
Route::post('/stream/trashErase', 'App\Http\Controllers\Stream\StreamButtonsController@trashErase')
->name('trashErase');
Route::post('/stream/ruleDelete', 'App\Http\Controllers\Stream\StreamButtonsController@ruleDelete')
->name('ruleDelete');
Route::post('/stream/oldRuleDelete', 'App\Http\Controllers\Stream\StreamButtonsController@oldRuleDelete')
->name('oldRuleDelete');
Route::post('/stream/userLinksErasePosts', 'App\Http\Controllers\Stream\StreamButtonsController@userLinksErasePosts')
->name('userLinksErasePosts');
Route::post('/stream/userLinksDelete', 'App\Http\Controllers\Stream\StreamButtonsController@userLinksDelete')
->name('userLinksDelete');
Route::post('/stream/changePostLink', 'App\Http\Controllers\Stream\StreamButtonsController@changePostLink')
->name('changePostLink');
Route::post('/stream/flagErase', 'App\Http\Controllers\Stream\StreamButtonsController@flagErase')
->name('flagErase');
Route::post('/stream/erasePost', 'App\Http\Controllers\Stream\StreamButtonsController@erasePost')
->name('erasePost');
Route::post('/stream/flagPost', 'App\Http\Controllers\Stream\StreamButtonsController@flagPost')
->name('flagPost');

Route::post('/qiwi_request', 'App\Http\Controllers\QiwiRequestController@confirm')
->name('qiwi-request');

Route::post('/tarifs/choose', 'App\Http\Controllers\TarifController@choose')
->name('tarif-choose');

Route::get('/login', 'App\Http\Controllers\AuthController@authVK')
->name('auth-vk');

Route::get('/vk-auth-code', 'App\Http\Controllers\AuthController@authVKcode')
->name('auth-vk-code');

Route::get('/logout', 'App\Http\Controllers\AuthController@authVKdestroy')
->name('auth-vk-destroy');

Route::post('/opros', 'App\Http\Controllers\FeedbackController@send')->name('opros');
