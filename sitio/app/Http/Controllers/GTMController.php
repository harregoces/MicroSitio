<?php

namespace App\Http\Controllers;

use App\User;
use App\Google;
use App\Http\Controllers\Controller;
use App\task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GTMController extends Controller
{

    public function getGtmAccount(Request $request, $idcliente) {

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        if(!$task)
            return \Redirect::to('/merchantid/'.$idcliente);

        return response(json_encode($task->gtmaccount), 200)
            ->header('Content-Type', 'application/json');
    }
}