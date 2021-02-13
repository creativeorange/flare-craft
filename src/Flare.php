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
            ErrorHandler::className(),
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                $settings = $this->getSettings();

                if (is_array($settings->ingoredExceptions)) {
                    foreach ($settings->ingoredExceptions as $config) {
                        if (isset($config['class'])) {
                            if (is_callable($config['class'])) {
                                $result = $config['class']($event->exception);
                                if (!$result) {
                                    return;
                                }
                            } else {
                                if ($event->exception instanceof $config['class']) {
                                    return;
                                }
                            }
                        }
                    }
                }

                Flare::getInstance()->flare->handleException($event->exception);
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
