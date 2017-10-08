<?php

namespace App\Http\Controllers;

use App\User;
use App\Google;
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

        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'ga_code', $task->ga_code);
        $token = $client->getAccessToken()['access_token'];

        return view($type)->with('task',$task)->with('token',$token);

    }

    public function test(Request $request, $idcliente) {
        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task)
            return \Redirect::to('/idcliente/'.$idcliente);

        $client = Google::gtmClient();
        $client = Google::autorizacionCode($client, $idcliente, 'gtm_code', $task->gtm_code);
        $listAccount = Google::getAccountGTM($client);
        var_dump($listAccount);
        foreach($listAccount as $key => $val) {
            dd($val);
        }
        exit;
    }

}