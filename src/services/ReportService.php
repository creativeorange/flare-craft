<?php

namespace creativeorange\craft\flare\services;

use Composer\InstalledVersions;

use Craft;
use craft\helpers\App;

class ReportService
{
    public function appInfo()
    {
        $info = [
            'PHP version' => App::phpVersion(),
            'OS version' => PHP_OS . ' ' . php_uname('r'),
            'Database driver & version' => self::_dbDriver(),
            'Image driver & version' => self::_imageDriver(),
            'Craft edition & version' => 'Craft ' . App::editionName(Craft::$app->getEdition()) . ' ' . Craft::$app->getVersion(),
        ];

        if (!class_exists(InstalledVersions::class, false)) {
            $path = Craft::$app->getPath()->getVendorPath() . DIRECTORY_SEPARATOR . 'composer' .  DIRECTORY_SEPARATOR . 'InstalledVersions.php';
            if (file_exists($path)) {
                require $path;
            }
        }

        if (class_exists(InstalledVersions::class, false)) {
            self::_addVersion($info, 'Yii version', 'yiisoft/yii2');
            self::_addVersion($info, 'Twig version', 'twig/twig');
            self::_addVersion($info, 'Guzzle version', 'guzzlehttp/guzzle');
        }

        return $info;
    }

    /**
     * Returns the DB driver name and version
     *
     * @return string
     */
    private static function _dbDriver(): string
    {
        $db = Craft::$app->getDb();

        if ($db->getIsMysql()) {
            $driverName = 'MySQL';
        } else {
            $driverName = 'PostgreSQL';
        }

        return $driverName . ' ' . App::normalizeVersion($db->getSchema()->getServerVersion());
    }

    /**
     * Returns the image driver name and version
     *
     * @return string
     */
    private static function _imageDriver(): string
    {
        $imagesService = Craft::$app->getImages();

        if ($imagesService->getIsGd()) {
            $driverName = 'GD';
        } else {
            $driverName = 'Imagick';
        }

        return $driverName . ' ' . $imagesService->getVersion();
    }

    /**
     * @param array $info
     * @param string $label
     * @param string $packageName
     */
    private static function _addVersion(array &$info, string $label, string $packageName): void
    {
        try {
            $version = InstalledVersions::getPrettyVersion($packageName) ?? InstalledVersions::getVersion($packageName);
        } catch (\OutOfBoundsException $e) {
            return;
        }

        if ($version !== null) {
            $info[$label] = $version;
        }
    }
}
