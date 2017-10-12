<?php

namespace App\Http\Controllers;

use App\User;
use App\Google;
use App\Http\Controllers\Controller;
use App\task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function index(Request $request, $idcliente)
    {
        \Session::put('idcliente',$idcliente);
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        return view('welcome')->with('task',$task);
    }

    public function installPlugingtm(Request $request)
    {
        $client = Google::gtmClient();
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }

    public function installPluginga(Request $request)
    {
        return view('installPluginga');
    }

    public function installPluginga2(Request $request)
    {
        $idcliente = \Session::get('idcliente');
        $trackingid = $request->get('trackingid');
        \Session::put('trackingid',$trackingid);

        //insert in the trackingid
        DB::update('update tasks set trackingid = ? where idcliente = ?', [$trackingid,$idcliente]);


        $client = Google::gaClient();
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }


    public function callbackPlugingtm(Request $request) {
        $gtm_code = $_GET['code'];
        $idcliente = \Session::get('idcliente');
        $client = Google::gtmClient();
        \Session::put('gtm_code',$gtm_code);
        $gtm_code = json_encode($client->fetchAccessTokenWithAuthCode($gtm_code)) ;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,gtm_code) values (?, ?)', [ $idcliente, $gtm_code]);
        } else {
            DB::update('update tasks set gtm_code = ? where idcliente = ?', [$gtm_code,$idcliente]);
        }

        $redirect = \Redirect::to('/installplugingtm2');

        return $redirect;
    }

    public function testGa(Request $request) {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        //insert in the GTM
        $clientGTM = Google::gtmClient();
        $clientGTM = Google::autorizacionCode($clientGTM, $idcliente, 'gtm_code', $task->gtm_code);
        Google::getGTMGoogleAnalyticsTag($clientGTM, $task->trackingid, json_decode($task->gtmaccount));
        exit;
    }

    public function callbackPluginga(Request $request) {
        $ga_code = $_GET['code'];
        $idcliente = \Session::get('idcliente');
        $client = Google::gaClient();
        $ga_code = json_encode($client->fetchAccessTokenWithAuthCode($ga_code)) ;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,ga_code) values (?, ?)', [$idcliente, $ga_code]);
        } else {
            DB::update('update tasks set ga_code = ? where idcliente = ?', [$ga_code,$idcliente]);
        }

        //insert in the GTM
        $clientGTM = Google::gtmClient();
        $clientGTM = Google::autorizacionCode($clientGTM, $idcliente, 'gtm_code', $task->gtm_code);
        Google::getGTMGoogleAnalyticsTag($clientGTM, $task->trackingid, json_decode($task->gtmaccount));


        $redirect = \Redirect::to('/merchantid/'.$idcliente);
        $url = \Session::get('session_url');
        if($url) {
            $redirect = \Redirect::to($url);
        }

        return $redirect;
    }



    public function installPlugingtm2(Request $request)
    {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        $accounts = Google::getAccountGTM($client);
        return view('installplugingtm2')->with('accounts',$accounts);
    }

    public function installPlugingtm3(Request $request)
    {
        $gtmaccount = $request->get('gtmaccount');
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);

        //get the account
        $account = Google::getContainerGTM($client, $gtmaccount);
        DB::update('update tasks set gtmaccount = ? where idcliente = ?', [json_encode($account),$idcliente]);

        $redirect = \Redirect::to('/merchantid/'.$idcliente);
        return $redirect;
    }

}