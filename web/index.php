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

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'      => 'app-server.local',
        'dbname'    => 'app-server',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8',
    ),
));

$app['oauth_server']               = function (\Silex\Application $app) {
    $storage  = new \App\Storage\Doctrine($app['db']);
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