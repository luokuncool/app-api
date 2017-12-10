<?php
require __DIR__ . '/../vendor/autoload.php';
$app = new \Silex\Application();

$app->get('/', function(){
    echo sha1('admin888'), '<br/>', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', '<br>';
    var_export($_SERVER);
    return 'home page';
});
$app['debug'] = true;
$app->match('/token', function (){
    $dsn      = 'mysql:dbname=app-server;host=app-server.local';
    $username = 'homestead';
    $password = 'secret';
    $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
    $server = new OAuth2\Server($storage,['use_openid_connect' => true, 'allow_implicit' => true]);
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
    $server->addGrantType(new \App\GrantType\MessageGrant($storage));
    $server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send('json');
    return new \Symfony\Component\HttpFoundation\Response();
});

$app->mount('/oauth2', new \App\Controller\Oauth2Controller);

$app->run();