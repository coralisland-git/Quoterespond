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

Route::get('/view/{folder?}/{file?}/{param?}', function($folder = '', $file = '', $param = '') {
	$view = $folder.(empty($file) ? '' : '.'.$file);
	if (empty($view)) {
		$controller = app()->make('\App\Http\Controllers\PagesController');
		$view = $controller->callAction('defaultPage', []);
	}
	return view($view);
})->middleware('auth.views');

Route::group(['prefix' => 'api/v1', 'middleware' => ['messages', 'timezone']], function() {
	//Route::any('{sunit}/{sid?}/{smethod?}', 'ApiController@run')->middleware('messages');
	Route::post('gmail/login', 'GmailController@login');
	Route::post('gmail/storeTokenFile', 'GmailController@storeTokenFile');
	Route::get('gmail/scanTokenDirectory', 'GmailController@scanTokenDirectory');
	Route::get('gmail/checkMailboxesByTokenFiles', 'GmailController@checkMailboxesByTokenFiles');

	Route::post('auth/support', 'AuthController@support');
	Route::post('auth/signup', 'AuthController@signup');
	Route::post('auth/signin', 'AuthController@signin');
	Route::post('auth/recovery', 'AuthController@recovery');
	Route::get('auth/signout', 'AuthController@signout');
	Route::get('auth/info', 'AuthController@info');

	Route::get('pages/menu', 'PagesController@menu');

	Route::post('users/add-mailbox/', 'UsersController@addMailbox');
	Route::post('users/password', 'UsersController@password');
	Route::get('users', 'UsersController@all');
	Route::get('users/live', 'UsersController@getLiveUsers');
	Route::get('users/canceled', 'UsersController@getCanceledUsers');
	Route::put('users/access/{id}', 'UsersController@allowAccess');
	Route::put('users', 'UsersController@create');
	Route::post('users/{id}', 'UsersController@update');
	Route::delete('users/{id}', 'UsersController@remove');
	Route::get('users/{id}/magic', 'UsersController@magic');

	Route::get('clients/all', 'ClientsController@all');

	Route::get('plans', 'PlansController@all');
	Route::put('plans/save', 'PlansController@savePlan');
	Route::post('plans/notifications', 'PlansController@planNotifications');
	Route::post('plans/subscribe', 'PlansController@subscribe');
	Route::put('plans/subscribe', 'PlansController@updateCard');
	Route::get('plans/get', 'PlansController@getPlanInfo');
	Route::post('plans/unsubscribe/{user?}', 'PlansController@cancelSubscription');
	Route::post('plans/reactivate/{user?}', 'PlansController@reactivatePlan');
	Route::post('plans/assign/{user}', 'PlansController@assignPlanToUser');
	Route::post('plans/{plan_id}', 'PlansController@updatePlan');
	Route::delete('plans/{plan_id}', 'PlansController@remove');

	Route::get('settings/{user_id}', 'SettingsController@getSettings');
	Route::put('settings/save/{user_id}', 'SettingsController@saveSettings');
	Route::put('settings/activate/{user_id}', 'SettingsController@activate');

	Route::get('mailboxes/', 'MailboxController@all');
	Route::put('mailboxes/add', 'MailboxController@add');
	Route::delete('mailboxes/{id}/{email}', 'MailboxController@delete');
});

Route::get('signup/', function($type = false) {
	return view('signup');
});

Route::get('support', function() {
	return view('support');
});

Route::get('recovery', function() {
	return view('recovery');
});

Route::get('ha-job/{user}/{client?}', 'HomeadvisorController@page');

Route::get('general/{message}/bit.ly/{bitly}', 'UsersController@magicGeneral');

Route::get('magic/{client}/bit.ly/{bitly}', 'HomeadvisorController@magic');
Route::get('magic/inbox/{user}/{client}/{dialog}/{action}', 'UsersController@magicInbox');
Route::get('magic/referral/{hash}', 'UsersController@magicReferral');

Route::any('de83020eb8e0b2b1840734bb34a00f0f/get_fb_token', 'UsersController@facebookToken');
Route::any('save_fb_reviews', 'UsersController@facebookReviews');

Route::any('de83020eb8e0b2b1840734bb34a00f0f/get_google_place', 'UsersController@googlePlaceId');
Route::any('save_google_reviews', 'UsersController@googleReviews');

Route::get('seances/{code}', 'AnswersController@text');
Route::get('seances/{id}/{value}', 'AnswersController@email');

Route::any('home-advisor/{code?}/{fake?}', 'HomeadvisorController@lead');
Route::any('craft-jack/{code?}/{fake?}', 'HomeadvisorController@craftJacklead');

Route::any('company/push', 'UsersController@push');
Route::any('review/push/{review}', 'SeancesController@push');
Route::any('message/push/{text}', 'MessagesController@push');
Route::any('dialog/push/{dialog}', 'DialogsController@push');
Route::any('general/push/{message}', 'HomeadvisorController@push');
Route::any('appointment/push/{appointment}', 'AppointmentController@push');

Route::any('inbox/dialog/{dialog}', 'DialogsController@inbox');
Route::any('inbox/message/{message}', 'MessagesController@inbox');
Route::any('inbox/general/{message}', 'HomeadvisorController@inbox');

Route::any('leads/convert', 'HomeadvisorController@convert');

Route::post('stripe/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');
Route::post('gmail/receive', 'HomeadvisorController@getGmailLetters');

Route::any('push', 'TrumpiaController@pushReceiver');
Route::any('inbox', 'TrumpiaController@inboxReceiver');
//Route::any('lc/inbox', 'TrumpiaController@inboxReceiver');

Route::any('craftjack', 'HomeadvisorController@craftJack');
Route::any('dropcowboy', 'HomeadvisorController@dropCowboy');

Route::any('{catchall}', function() {
	return auth()->check() ? view('template') : view('signin');
})->where('catchall', '(.*)');