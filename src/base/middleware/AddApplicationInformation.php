<?php

namespace creativeorange\craft\flare\base\middleware;

use creativeorange\craft\flare\Flare;
use Facade\FlareClient\Report;

use craft\helpers\App;

class AddApplicationInformation
{
    public function handle(Report $report, $next)
    {
        $report->group('App', Flare::$plugin->report->appInfo());

        return $next($report);
    }
}
