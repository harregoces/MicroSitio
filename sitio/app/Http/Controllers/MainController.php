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
        //check if the user has the GTM installed
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        $response = new \Illuminate\Http\Response(view('welcome')->with('task',$task));
        $response->withCookie(cookie('idcliente', $idcliente, 450000));
        return $response;
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
        $client = Google::gaClient();
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }

    public function callbackPlugingtm(Request $request) {
        $gtm_code = $_GET['code'];
        $idcliente = $request->cookie('idcliente');
        $client = Google::gtmClient();
        $gtm_code = json_encode($client->fetchAccessTokenWithAuthCode($gtm_code)) ;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,gtm_code) values (?, ?)', [ $idcliente, $gtm_code]);
        } else {
            DB::update('update tasks set gtm_code = ? where idcliente = ?', [$gtm_code,$idcliente]);
        }

        $redirect = \Redirect::to('/merchantid/'.$idcliente);
        $url = \Session::get('session_url');
        if($url) {
            $redirect = \Redirect::to($url);
        }

        return $redirect;
    }

    public function callbackPluginga(Request $request) {
        $ga_code = $_GET['code'];
        $idcliente = $request->cookie('idcliente');
        $client = Google::gaClient();
        $ga_code = json_encode($client->fetchAccessTokenWithAuthCode($ga_code)) ;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,ga_code) values (?, ?)', [$idcliente, $ga_code]);
        } else {
            DB::update('update tasks set ga_code = ? where idcliente = ?', [$ga_code,$idcliente]);
        }

        $redirect = \Redirect::to('/merchantid/'.$idcliente);
        $url = \Session::get('session_url');
        if($url) {
            $redirect = \Redirect::to($url);
        }

        return $redirect;
    }



}