<?php

namespace App\Controller;

use App\GrantType\MessageGrant;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Request;
use OAuth2\Server;
use OAuth2\Storage\Pdo;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;

class Oauth2Controller implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        /** @var ControllerCollection $route */
        $route = $app['controllers_factory'];
        $route->post('/token', [$this, 'tokenAction']);
        return $route;
    }

    public function tokenAction()
    {
        $dsn = 'mysql:dbname=app-server;host=app-server.local';
        $username = 'homestead';
        $password = 'secret';
        $storage = new Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        $server = new Server($storage);
        $server->addGrantType(new ClientCredentials($storage));
        $server->addGrantType(new RefreshToken($storage, [
            'always_issue_new_refresh_token' => true,
            'refresh_token_lifetime' => 2419200
        ]));
        $server->addGrantType(new MessageGrant($storage));
        $server->addGrantType(new UserCredentials($storage));
        $server->handleTokenRequest(Request::createFromGlobals())->send();
        return new Response();
    }
}