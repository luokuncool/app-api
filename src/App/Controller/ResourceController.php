<?php

namespace App\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class ResourceController implements ControllerProviderInterface
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
        /** @var ControllerCollection $routing */
        $routing = $app['controllers_factory'];
        $routing
            ->match('/docList', [$this, 'docListAction'])
            ->before($app['resource_access_middleware']);
        return $routing;
    }

    public function docListAction()
    {
        return 'you have access!';
    }
}