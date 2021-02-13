<?php

namespace creativeorange\craft\flare\base\middleware;

use craft\helpers\App;
use Facade\FlareClient\Report;

class AddEnvironmentInformation
{
    public function handle(Report $report, $next)
    {
        $report->group('env', [
            'craft_version' => \Craft::$app->getVersion(),
            'craft_locale' => \Craft::$app->getLocale()->id,
            'php_version' => phpversion(),
        ]);

        return $next($report);
    }
}
