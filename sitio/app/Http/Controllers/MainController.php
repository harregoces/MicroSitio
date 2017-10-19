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
    public function home(Request $request)
    {
        return view('home');
    }

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

    public function installPluginga2(Request $request)
    {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $account = $request->get('account');
        $property = $request->get('property');
        $view = $request->get('view');

        DB::update('update tasks set ga_account = ? , ga_property = ? , ga_view = ? where idcliente = ?', [$account,$property,$view,$idcliente]);

        //create tag
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        Google::getGTMGoogleAnalyticsTag($client, $property, json_decode($task->gtmaccount),$task->workspaceid);

        return \Redirect::to('/merchantid/'.$idcliente);
    }

    public function installPluginga(Request $request)
    {
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

    public function test(Request $request,$idcliente) {
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);

        $account = Google::getGaAccounts($client);
        return view('installPluginga')->with('listAccount',$account);
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

        //get the trackings id or account
        $account = Google::getGaAccounts($client);
        return view('installPluginga')->with('listAccount',$account);
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
        $workspaceid = $request->get('workspace');
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);

        //get the account
        $account = Google::getContainerGTM($client, $gtmaccount);
        DB::update('update tasks set gtmaccount = ? , workspaceid = ? where idcliente = ?', [json_encode($account),$workspaceid, $idcliente]);

        $redirect = \Redirect::to('/merchantid/'.$idcliente);
        return $redirect;
    }

    public function getWorkspace(Request $request,$gtmaccount) {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->gtm_code);
        $account = (object) Google::getContainerGTM($client, $gtmaccount);
        $w = Google::getWorkspaceList($client,$account);
        return response()->json($w,200);
    }

    public function getProperty(Request $request,$account) {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->ga_code);
        $listProperty = Google::getPropertyGA($client, $account);
        return response()->json($listProperty,200);
    }

    public function getView(Request $request,$account, $property) {
        $idcliente = \Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->ga_code);
        $listView = Google::getViewGA($client, $account,$property);
        return response()->json($listView,200);
    }

}