<?php

namespace creativeorange\craft\flare\services;

use Craft;
use craft\base\Component;
use creativeorange\craft\flare\base\middleware\AddApplicationInformation;
use creativeorange\craft\flare\base\middleware\AddEnvironmentInformation;
use creativeorange\craft\flare\base\middleware\AddPluginInformation;
use creativeorange\craft\flare\base\middleware\AddQueries;
use creativeorange\craft\flare\Flare;
use Facade\FlareClient\Flare as FlareClient;

class FlareService extends Component
{
    /**
     * @param $exception
     * @return bool|void
     */
    public function handleException($exception)
    {
        $flare = FlareClient::register(Flare::$plugin->getSettings()->getKey());
        $flare->stage(Craft::$app->env);

        if ( $user = Craft::$app->getUser()->getIdentity()x ) {
            $flare->group('user', [
                'id'    => $user->id,
                'name'  => $user->getName(),
                'email' => $user->email,
            ]);
        }

        $flare->registerMiddleware(new AddApplicationInformation);
        $flare->registerMiddleware(new AddEnvironmentInformation);
        $flare->registerMiddleware(new AddPluginInformation);
//        $flare->registerMiddleware(new AddQueries);

        $flare->handleException($exception);
    }

    public function isEnabled()
    {
        $settings = Flare::$plugin->getSettings();

        return $settings->enabled && !empty($settings->key);
    }
}
