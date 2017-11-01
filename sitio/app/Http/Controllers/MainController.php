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
        //\Session::put('idcliente',$idcliente);
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        return view('welcome')->with('task',$task)->with('idcliente',$idcliente);
    }

    public function uninstall(Request $request, $idcliente)
    {
        //\Session::put('idcliente',$idcliente);
        DB::table('tasks')->where('idcliente',$idcliente)->delete();
        header('Location: ' . filter_var("/merchantid/{$idcliente}", FILTER_SANITIZE_URL));
        exit;
    }

    public function installPlugingtm(Request $request, $idcliente)
    {
        $state = array("merchant_id"=>$idcliente, "returnurl"=>$request->returnurl);
        $client = Google::gtmClient(array("state"=> json_encode($state) ));
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }

    public function installPluginga2(Request $request)
    {
        $idcliente = $_REQUEST['merchant_id'];//\Session::get('idcliente');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $account = $request->get('account');
        $property = $request->get('property');
        $view = $request->get('view');

        //create tag
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        $tagId = Google::getGTMGoogleAnalyticsTag($client, $property, json_decode($task->gtmaccount),$task->workspaceid);

        DB::update('update tasks set ga_account = ? , ga_property = ? , ga_view = ? , uat_id = ? where idcliente = ?', [$account,$property,$view,$tagId,$idcliente]);

        Google::proccessCreationAllTagsElements($client, $property, json_decode($task->gtmaccount), $task->workspaceid);

        $returnurl = json_decode(urldecode($_REQUEST['returnurl']));
        if(isset($returnurl->returnurl)) $returnurl = $returnurl->returnurl.'/'.$returnurl->merchant_id."?message=".json_encode($tagId);
        else $returnurl = '/merchantid/'.$idcliente;

        return \Redirect::to($returnurl);
    }

    public function installPluginga(Request $request, $idcliente)
    {
        $state = array("merchant_id"=>$idcliente, "returnurl"=>$request->returnurl);
        $client = Google::gaClient(array("state"=> json_encode($state) ));

        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }


    public function callbackPlugingtm(Request $request) {

        $gtm_code = $_GET['code'];

        $client = Google::gtmClient();
        //\Session::put('gtm_code',$gtm_code);
        $gtm_code = json_encode($client->fetchAccessTokenWithAuthCode($gtm_code)) ;

        $state = json_decode(urldecode($_GET['state']));
        $returnurl = $state->returnurl;
        $idcliente = $state->merchant_id;

        if(!empty($gtm_code['error']))
        {
            $returnurl .= "?errorMessage={$gtm_code['error_description']}";
            header('Location: ' . filter_var($returnurl, FILTER_SANITIZE_URL));
            exit;
        }

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,gtm_code) values (?, ?)', [ $idcliente, $gtm_code]);
        } else {
            DB::update('update tasks set gtm_code = ? where idcliente = ?', [$gtm_code,$idcliente]);
        }

        $redirect = \Redirect::to("/installplugingtm2/?returnurl={$returnurl}&merchant_id={$idcliente}");

        return $redirect;
    }

    public function callbackPluginga(Request $request) {
        $ga_code = $_GET['code'];

        $client = Google::gaClient();
        $ga_code = json_encode($client->fetchAccessTokenWithAuthCode($ga_code)) ;

        $state = json_decode(urldecode($_GET['state']));
        $returnurl = $state->returnurl;
        $idcliente = $state->merchant_id;

        if(!empty($ga_code['error']))
        {
            $returnurl .= "?errorMessage={$ga_code['error_description']}";
            header('Location: ' . filter_var($returnurl, FILTER_SANITIZE_URL));
            exit;
        }
        
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        $ga_code = json_encode($ga_code);

        if(!$task) {
            DB::insert('insert into tasks (idcliente,ga_code) values (?, ?)', [$idcliente, $ga_code]);
        } else {
            DB::update('update tasks set ga_code = ? where idcliente = ?', [$ga_code,$idcliente]);
        }

        //get the trackings id or account
        $account = Google::getGaAccounts($client);

        return view('installPluginga')->with('listAccount',$account)->with('returnurl', $returnurl)->with('merchant_id',$idcliente);
    }

    public function installPlugingtm2(Request $request)
    {
        $returnurl = $_REQUEST['returnurl'];
        $idcliente = $_REQUEST['merchant_id'];

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        $accounts = Google::getAccountGTM($client);
        return view('installplugingtm2')->with('accounts',$accounts)->with('returnurl', $returnurl)->with('merchant_id',$idcliente);
    }

    public function installPlugingtm3(Request $request)
    {
        $gtmaccount = $request->get('gtmaccount');
        $workspaceid = $request->get('workspace');
        $idcliente = $request->get('merchant_id'); //\Session::get('idcliente');
        $returnurl = $request->get('returnurl'); //\Session::get('idcliente');

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);

        //get the account
        $account = Google::getContainerGTM($client, $gtmaccount);
        DB::update('update tasks set gtmaccount = ? , workspaceid = ? where idcliente = ?', [json_encode($account),$workspaceid, $idcliente]);

        $redirect = \Redirect::to($returnurl);
        return $redirect;
    }

    public function getWorkspace(Request $request,$gtmaccount) {
        //$idcliente = \Session::get('idcliente');
        $idcliente = $request->get('merchant_id');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->gtm_code);
        $account = (object) Google::getContainerGTM($client, $gtmaccount);
        $w = Google::getWorkspaceList($client,$account);
        return response()->json($w,200);
    }

    public function getProperty(Request $request,$account) {
        //$idcliente = \Session::get('idcliente');
        $idcliente = $request->get('merchant_id');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->ga_code);
        $listProperty = Google::getPropertyGA($client, $account);
        return response()->json($listProperty,200);
    }

    public function getView(Request $request,$account, $property) {
        $idcliente = $request->get('merchant_id');
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->ga_code);
        $listView = Google::getViewGA($client, $account,$property);
        return response()->json($listView,200);
    }






    public function test(Request $request,$idcliente) {
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();
        $client = Google::gaClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        Google::proccessCreationAllTagsElements($client, $task->ga_property, json_decode($task->gtmaccount), $task->workspaceid);

    }

}