<?php

namespace creativeorange\craft\flare\base\middleware;

use creativeorange\craft\flare\Flare;
use creativeorange\yii\flare\QueryStorage;
use Facade\FlareClient\Report;

use craft\helpers\App;
use yii\di\Container;

class AddQueries
{
    public function handle(Report $report, $next)
    {
        ray(QueryStorage::class . " read");

        $queries = (new Container())->has(QueryStorage::class);

        return $next($report);
    }
}

