<?php

namespace creativeorange\craft\flare;

use Craft;
use craft\base\Plugin;
use craft\events\ExceptionEvent;
use craft\events\PluginEvent;
use craft\helpers\UrlHelper;
use craft\services\Plugins;
use craft\web\ErrorHandler;
use creativeorange\craft\flare\models\Settings;
use creativeorange\craft\flare\services\FlareService;
use creativeorange\craft\flare\services\ReportService;
use yii\base\Event;

/**
 * Class Flare
 * @package creativeorange\craft\flare
 * @property FlareService $flare
 * @property ReportService $report
 * @method Settings getSettings()
 */
class Flare extends Plugin
{
    /**
     * @var self
     */
    static $plugin;

    public $hasCpSettings = true;

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'flare' => FlareService::class,
            'report' => ReportService::class
        ]);

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this && !Craft::$app->getRequest()->isConsoleRequest) {
                    Craft::$app->response->redirect(UrlHelper::cpUrl('settings/plugins/flare'))->send();
                }
            }
        );


        Event::on(
            ErrorHandler::class,
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                Flare::getInstance()->flare->handleException($event->exception, $this->getSettings()->ignoredExceptions ?? []);
            }
        );

    }

    protected function createSettingsModel()
    {
        return new Settings();
    }


    /**
     * @inheritdoc
     */
    protected function settingsHtml (): string
    {
        return Craft::$app->view->renderTemplate(
            'flare/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
