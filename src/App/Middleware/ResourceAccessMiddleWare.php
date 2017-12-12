<?php

namespace App\Middleware;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceAccessMiddleWare
{
    public function __invoke(Request $request, Application $app)
    {
        $oauthRequest = \OAuth2\Request::createFromGlobals();
        /** @var \OAuth2\Server $server */
        $server = $app['oauth_server'];
        if (!$server->verifyResourceRequest($oauthRequest)) {
            $server->getResponse()->send();
            die;
        }
        $tokenData = $server->getAccessTokenData($oauthRequest);
        if (!$tokenData['user_id']) {
            $response = new Response('Access denied', 403);
            $response->send();
            die;
        }
    }
}