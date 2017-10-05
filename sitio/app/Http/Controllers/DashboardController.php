<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function selectType(Request $request, $idcliente, $type='basic') {

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task)
            return \Redirect::to('/idcliente/'.$idcliente);

        //$task->gtm_code = $this->autorizacionCode($task->gtm_code);
        $task->ga_code = $this->autorizacionCode($task->ga_code,$idcliente);

        return view($type)->with('task',$task);

    }

    public function autorizacionCode($code,$idcliente) {
        $code = json_decode($code,true);
        $client = new \Google_Client();
        $client->setApplicationName('Web client OAuth');
        $client->setClientId('349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com');
        $client->setClientSecret('-bFJBmY-EhckJDplREV33vU9');
        $client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/installplugincallbackga/');
        $client->setAccessType('offline');
        $client->setAccessToken($code);

        //var_dump($client->getRefreshToken());exit;

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            //save the access token when is refresh
            $code = json_encode($client->getAccessToken());
            DB::update('update tasks set ga_code = ? where idcliente = ?', [$code,$idcliente]);
        }
        return $client->getAccessToken()['access_token'];
    }

    private function getAccount() {
        $google_client = new \Google_Client();
        $client = new \Google_Service_TagManager($google_client);
        $client->accounts_containers_workspaces_tags->listAccountsContainersWorkspacesTags()
    }

}