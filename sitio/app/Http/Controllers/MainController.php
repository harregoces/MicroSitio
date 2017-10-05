<?php

namespace App\Http\Controllers;

use App\User;
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
        $google_client = new \Google_Client();
        $google_client->setApplicationName('Web client OAuth');
        $google_client->setClientId('349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com');
        $google_client->setClientSecret('-bFJBmY-EhckJDplREV33vU9');
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/installplugincallbackgtm/');
        $google_client->addScope(\Google_Service_TagManager::TAGMANAGER_READONLY);
        $google_client->setAccessType('offline');
        $auth_url = $google_client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }

    public function installPluginga(Request $request)
    {
        $google_client = new \Google_Client();
        $google_client->setApplicationName('Web client OAuth');
        $google_client->setClientId('349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com');
        $google_client->setClientSecret('-bFJBmY-EhckJDplREV33vU9');
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/installplugincallbackga/');
        $google_client->addScope(\Google_Service_Analytics::ANALYTICS_READONLY);
        $google_client->setAccessType('offline');
        $auth_url = $google_client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;
    }

    public function callbackPlugingtm(Request $request) {
        $code = $_GET['code'];
        $idcliente = $request->cookie('idcliente');
        $gtm_code = $code;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,gtm_code) values (?, ?)', [ $idcliente, $gtm_code]);
        } else {
            DB::update('update tasks set gtm_code = ? where idcliente = ?', [$gtm_code,$idcliente]);
        }

        return \Redirect::to('/idcliente/'.$idcliente);
    }

    public function callbackPluginga(Request $request) {
        $code = $_GET['code'];
        $idcliente = $request->cookie('idcliente');
        $ga_code = $code;


        $client = new \Google_Client();
        $client->setApplicationName('Web client OAuth');
        $client->setClientId('349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com');
        $client->setClientSecret('-bFJBmY-EhckJDplREV33vU9');
        $client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/installplugincallbackga/');
        $client->setAccessType('offline');
        $ga_code = json_encode($client->fetchAccessTokenWithAuthCode($ga_code)) ;

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task) {
            DB::insert('insert into tasks (idcliente,ga_code) values (?, ?)', [$idcliente, $ga_code]);
        } else {
            DB::update('update tasks set ga_code = ? where idcliente = ?', [$ga_code,$idcliente]);
        }

        return \Redirect::to('/idcliente/'.$idcliente);
    }



}