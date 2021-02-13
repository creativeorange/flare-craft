<?php

namespace creativeorange\craft\flare\base\middleware;

use Craft;
use Facade\FlareClient\Report;

class AddPluginInformation
{
    public function handle(Report $report, $next)
    {
        $report->group('env', [
            'craft_version' => \Craft::$app->getVersion(),
            'craft_locale' => \Craft::$app->getLocale()->id,
            'php_version' => phpversion(),
        ]);

        $all = Craft::$app->getPlugins()->getAllPluginInfo();

        $report->group('Installed & Enabled plugins', $this->filterInstalledAndEnabled($all));
        $report->group('Disabled plugins', $this->filterInstalledAndDisabled($all));
        $report->group('Not installed plugins', $this->filterNotInstalled($all));

        return $next($report);
    }

    public function filterInstalledAndEnabled($all)
    {
        return $this->decorate(array_filter($all, function ($plugin) {
            return $plugin['isInstalled'] && $plugin['isEnabled'];
        }));
    }

    public function decorate($data)
    {
        return array_combine(
            array_map(function ($plugin) {
                return $plugin['name'];
            }, $data),
            array_map(function ($plugin) {
                return $plugin['version'];
            }, $data)
        );
    }

    public function filterInstalledAndDisabled($all)
    {
        return $this->decorate(array_filter($all, function ($plugin) {
            return $plugin['isInstalled'] && !$plugin['isEnabled'];
        }));
    }

    public function filterNotInstalled($all)
    {
        return $this->decorate(array_filter($all, function ($plugin) {
            return !$plugin['isInstalled'];
        }));
    }
}
