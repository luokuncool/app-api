<?php

namespace App\Controller;

use OAuth2\Request;
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

    public function tokenAction(Application $app)
    {
        $app['oauth_server']->handleTokenRequest(Request::createFromGlobals())->send();
        return new Response();
    }
}