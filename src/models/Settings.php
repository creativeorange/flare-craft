<?php

namespace creativeorange\craft\flare\models;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

class Settings extends Model
{
    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * @var string
     */
    public $key = '$FLARE_KEY';

    /**
     * @var array
     */
    public $ingoredExceptions;

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'parser' => [
                'class'      => EnvAttributeParserBehavior::class,
                'attributes' => ['key'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
        ];
    }

    public function getKey()
    {
        return Craft::parseEnv($this->key);
    }

    public function getIgnoredExceptions()
    {
        if (!is_array($this->ingoredExceptions)) {
            return [];
        }

        $ingoredExceptions = array_map(function($row) {
            if (isset($row['class']) && \is_callable($row['class'])) {
                $row['class'] = 'Advanced check set through config file';
            }

            return $row;
        }, $this->ingoredExceptions);

        return array_filter($ingoredExceptions);
    }
}
