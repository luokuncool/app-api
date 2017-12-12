<?php

use App\GrantType\MessageGrant;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Server;

require __DIR__ . '/../vendor/autoload.php';
$app = new \Silex\Application();

$app->get(
    '/',
    function () {
        return 'home page';
    }
);
$app['debug']                      = true;
$app['oauth_server']               = function () {
    $dsn      = 'mysql:dbname=app-server;host=app-server.local';
    $username = 'root';
    $password = 'root';
    $storage  = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
    $server   = new Server($storage);
    $server->addGrantType(new ClientCredentials($storage));
    $server->addGrantType(
        new RefreshToken(
            $storage, [
                'always_issue_new_refresh_token' => true,
                'refresh_token_lifetime'         => 2419200
            ]
        )
    );
    $server->addGrantType(new MessageGrant($storage));
    $server->addGrantType(new UserCredentials($storage));
    return $server;
};
$app['resource_access_middleware'] = function () {
    return new \App\Middleware\ResourceAccessMiddleWare;
};
$app->mount('/oauth2', new \App\Controller\Oauth2Controller);
$app->mount('/resource', new \App\Controller\ResourceController);
$app->run();