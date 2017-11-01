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

    public function getMerchantAccountById(Request $request, $idcliente) {

        $task = DB::table('tasks')->where('idcliente',$idcliente)->first();

        $response = array();

        if(!$task){
            $response['code'] = "NOT_INSTALLED";
            $response['container'] = null;

            return response(json_encode($response), 200)
                ->header('Content-Type', 'application/json');
        }

        $response['code'] = "INSTALLED";
        $response['container'] = $task;
        return response(json_encode($response), 200)
            ->header('Content-Type', 'application/json');
    }
}