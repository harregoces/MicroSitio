<?php
/**
 * Created by PhpStorm.
 * User: Hernan Arregoces
 * Date: 07/10/2017
 * Time: 7:44 PM
 */

namespace App;

use Illuminate\Support\Facades\DB;

class Google {

    private static $applicationName  = 'Web client OAuth';
    private static $clientId = '349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com';
    private static $clientSecret = '-bFJBmY-EhckJDplREV33vU9';
    private static $redirectGTM = '/installplugincallbackgtm/';
    private static $redirectGA = '/installplugincallbackga/';

    public static function gtmClient() {
        $google_client = new \Google_Client();
        $google_client->setApplicationName(self::$applicationName);
        $google_client->setClientId(self::$clientId);
        $google_client->setClientSecret(self::$clientSecret);
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].self::$redirectGTM);
        $google_client->addScope(
            array(
                \Google_Service_TagManager::TAGMANAGER_READONLY,
                \Google_Service_TagManager::TAGMANAGER_MANAGE_ACCOUNTS,
                \Google_Service_TagManager::TAGMANAGER_PUBLISH,
                \Google_Service_TagManager::TAGMANAGER_EDIT_CONTAINERS
            )
        );
        $google_client->setIncludeGrantedScopes(true);
        $google_client->setAccessType('offline');
        return $google_client;
    }

    public static function gaClient() {
        $google_client = new \Google_Client();
        $google_client->setApplicationName(self::$applicationName);
        $google_client->setClientId(self::$clientId);
        $google_client->setClientSecret(self::$clientSecret);
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].self::$redirectGA);
        $google_client->addScope(
            array(
                \Google_Service_Analytics::ANALYTICS_READONLY,
                \Google_Service_Analytics::ANALYTICS,
                \Google_Service_Analytics::ANALYTICS_MANAGE_USERS
            )
        );
        $google_client->setIncludeGrantedScopes(true);
        $google_client->setAccessType('offline');
        return $google_client;
    }

    public static function autorizacionCode(\Google_Client $client,$idcliente, $field, $code = null) {
        if($code) {
            $code = json_decode($code,true);
            $client->setAccessToken($code);
        }

        if ($client->isAccessTokenExpired()) {
            if(!$client->getRefreshToken()) {

                $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                \Session::set('session_url',$actual_link);
                $auth_url = $client->createAuthUrl();
                header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            }
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $code = json_encode($client->getAccessToken());
            DB::update('update tasks set '.$field.' = ? where idcliente = ?', [$code,$idcliente]);
        }
        return $client;
    }

    public static function getAccountGTM(\Google_Client $client) {
        $TGMclient = new \Google_Service_TagManager($client);
        $listAccount = $TGMclient->accounts->listAccounts();
        //var_dump($listAccount);
        foreach($listAccount->account as $val) {
            //var_dump($val->accountId);
            $res = $TGMclient->accounts_containers->listAccountsContainers($val->accountId);
            var_dump($res);
        }
        exit;
    }
}