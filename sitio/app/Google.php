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
    private static $json_config = '{"web":{"client_id":"349982058915-lqccda1kbqmdstrn6nm50b0qdhk8pr2q.apps.googleusercontent.com","project_id":"aplicacionbase","auth_uri":"https://accounts.google.com/o/oauth2/auth","token_uri":"https://accounts.google.com/o/oauth2/token","auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs","client_secret":"-bFJBmY-EhckJDplREV33vU9","redirect_uris":["http://micrositio.com/installplugincallbackgtm/","http://micrositio.com/installplugincallbackga/"],"javascript_origins":["http://micrositio.com"]}}';
    private static $TRIGGER_ID_ALL_PAGES = "2147479553";

    public static function gtmClient() {
        $google_client = new \Google_Client();
        $google_client->setAuthConfig( json_decode(self::$json_config,true) );
        $google_client->setApplicationName(self::$applicationName);
        $google_client->setClientId(self::$clientId);
        $google_client->setClientSecret(self::$clientSecret);
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].self::$redirectGTM);
        $google_client->setIncludeGrantedScopes(true);
        $google_client->setAccessType('offline');
        $google_client->setApprovalPrompt("force");
        $google_client->addScope(
            array(
                \Google_Service_TagManager::TAGMANAGER_READONLY,
                \Google_Service_TagManager::TAGMANAGER_MANAGE_ACCOUNTS,
                \Google_Service_TagManager::TAGMANAGER_PUBLISH,
                \Google_Service_TagManager::TAGMANAGER_EDIT_CONTAINERS
            )
        );

        return $google_client;
    }

    public static function gaClient() {
        $google_client = new \Google_Client();
        $google_client->setAuthConfig( json_decode(self::$json_config,true) );
        $google_client->setApplicationName(self::$applicationName);
        $google_client->setClientId(self::$clientId);
        $google_client->setClientSecret(self::$clientSecret);
        $google_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].self::$redirectGA);
        $google_client->setIncludeGrantedScopes(true);
        $google_client->setAccessType('offline');
        $google_client->setApprovalPrompt("force");
        $google_client->addScope(
            array(
                \Google_Service_Analytics::ANALYTICS_READONLY,
                \Google_Service_Analytics::ANALYTICS,
                \Google_Service_Analytics::ANALYTICS_MANAGE_USERS
            )
        );

        return $google_client;
    }

    public static function autorizacionCode(\Google_Client $client,$idcliente, $field, $code = null) {
        if($code) {
            $code = json_decode($code,true);
            $client->setAccessToken($code);
        }
        $res = $client->isAccessTokenExpired();
        $token = $client->getAccessToken();

        if ($client->isAccessTokenExpired() ) {
            if($client->getRefreshToken())
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            else {
                $my_code = \Session::get('gtm_code');
                $client->fetchAccessTokenWithAuthCode($my_code);
            }


            $code = json_encode($client->getAccessToken());
            DB::update('update tasks set '.$field.' = ? where idcliente = ?', [$code,$idcliente]);

        }
        return $client;
    }

    public static function getAccountGTM(\Google_Client $client) {
        $TGMclient = new \Google_Service_TagManager($client);
        $listAccount = $TGMclient->accounts->listAccounts();
        $result = array();
        foreach($listAccount->account as $val) {
            $res = array();
            $res['account'] = $val->name;
            $res['accountId'] = $val->accountId;
            $res['path'] = $val->path;

            $containers = $TGMclient->accounts_containers->listAccountsContainers($val->path);
            $containersA = array();
            foreach($containers->container as $container) {
                $cont = array();
                $cont['containerId'] = $container->containerId;
                $cont['containerName'] = $container->name;
                $cont['containerPath'] = $container->path;
                $cont['publicId'] = $container->publicId;
                $cont['tagManagerUrl'] = $container->tagManagerUrl;
                $containersA[] = $cont;
            }
            $res['containers'] = $containersA;
            $result[] = $res;
        }
        return $result;
    }


    public static function getContainerGTM(\Google_Client $client, $gtmaccount) {
        $TGMclient = new \Google_Service_TagManager($client);
        $listAccount = $TGMclient->accounts->listAccounts();
        foreach($listAccount->account as $val) {
            $containers = $TGMclient->accounts_containers->listAccountsContainers($val->path);
            foreach($containers->container as $container) {
                if($gtmaccount == $container->publicId) {
                    $cont = array();
                    $cont['containerId'] = $container->containerId;
                    $cont['containerName'] = $container->name;
                    $cont['containerPath'] = $container->path;
                    $cont['publicId'] = $container->publicId;
                    $cont['accountId'] = $container->accountId;
                    return $cont;
                }
            }
        }
        return null;
    }

    public static function getGTMGoogleAnalyticsTag(\Google_Client $client, $trackingid, $GTMAccount){

        $tag = new \Google_Service_TagManager_Tag();
        $tag->setPath($GTMAccount->containerPath);
        $tag->setAccountId($GTMAccount->accountId);
        $tag->setContainerId($GTMAccount->containerPath);
        $tag->setWorkspaceId(1);
        $tag->setTagId(null);
        $tag->setName("Universal Analytics Tag");
        $tag->setType('ua');
        $tag->setFiringTriggerId(array(self::$TRIGGER_ID_ALL_PAGES));
        $tag->setLiveOnly(false);
        $tag->setFingerprint('1463140767472');
        $tag->setTagFiringOption('oncePerEvent');

        $parameters = array(
            array(
                'type'=>'boolean',
                'key'=>'doubleClick',
                'value'=>'true'
            ),
            array(
                'type'=>'boolean',
                'key'=>'setTrackerName',
                'value'=>'false'
            ),
            array(
                'type'=>'boolean',
                'key'=>'useDebugVersion',
                'value'=>'false'
            ),
            array(
                'type'=>'boolean',
                'key'=>'useHashAutoLink',
                'value'=>'false'
            ),
            array(
                'type'=>'template',
                'key'=>'trackType',
                'value'=>'TRACK_PAGEVIEW'
            ),
            array(
                'type'=>'boolean',
                'key'=>'decorateFormsAutoLink',
                'value'=>'false'
            ),
            array(
                'type'=>'boolean',
                'key'=>'enableLinkId',
                'value'=>'true'
            ),
            array(
                'type'=>'boolean',
                'key'=>'enableEcommerce',
                'value'=>'true'
            ),
            array(
                'type'=>'template',
                'key'=>'trackingId',
                'value'=>$trackingid
            ),
            array(
                'type'=>'boolean',
                'key'=>'useEcommerceDataLayer',
                'value'=>'true'
            )
        );

        $tag->setParameter($parameters);

        $service = new \Google_Service_TagManager($client);
        return $service->accounts_containers_workspaces_tags->create($GTMAccount->containerPath.'/workspaces/1', $tag);
        //return $service->accounts_containers_tags->create($GTMAccount->containerPath , $GTMAccount->containerId,$tag);

    }
}