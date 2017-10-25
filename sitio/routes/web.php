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
use App\Google;

//Route::get('/', 'MainController@home');

Route::get('/', function(){
    return view('error');
});


Route::get('/merchantid/{idcliente}', 'MainController@index');


Route::get('/installplugingtm/merchantid/{idcliente}/{returnurl?}','MainController@installPlugingtm');
Route::get('/installplugingtm2','MainController@installPlugingtm2');
Route::post('/installplugingtm3','MainController@installPlugingtm3');

//Route::get('/installpluginga','MainController@installPluginga');

Route::get('/installpluginga/merchantid/{idcliente}/{returnurl?}','MainController@installPluginga');

Route::post('/installpluginga2','MainController@installPluginga2');
Route::get('/installpluginga3','MainController@installPluginga3');


Route::get('/installplugincallbackgtm', 'MainController@callbackPlugingtm');
Route::get('/installplugincallbackga', 'MainController@callbackPluginga');




Route::get('/dashboard/merchantid/{idcliente}/', 'DashboardController@home');
Route::get('/dashboard/merchantid/{idcliente}/type/{type}', 'DashboardController@selectType');

Route::get('/dashboardiframes/merchantid/{idcliente}/', 'DashboardController@dashboardiframes');


Route::get('/test/merchantid/{idcliente}', 'MainController@test');
Route::get('/getWorspace/gtmaccount/{gtmaccount}', 'MainController@getWorkspace');

Route::get('/getProperty/account/{account}/{returnurl?}', 'MainController@getProperty');
Route::get('/getView/account/{account}/property/{property}', 'MainController@getView');



Route::get('/getgtmaccountbyid/{merchantid}', 'GTMController@getGtmAccount');

/*
Route::get('/getgtmaccountbyid/{merchantid}', function ($merchantid) {

    $arr = json_encode(array('Hello World','otro'));
    return response($arr, 200)
        ->header('Content-Type', 'application/json')
        ;
});
*/