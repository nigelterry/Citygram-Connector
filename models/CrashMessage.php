<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection 'message'.
 *
 * @property \MongoId|string $_id
 */
class CrashMessage extends Message
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'crash_message'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'crash-message';
        $this->config->title = 'Crash Report';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'MongoID',
            'id' => 'Unique ID',
            'datetime' => 'Date / Time',
            'type' => 'Message Type',
            'dataset' => 'Data Set',
            'properties' => 'Message',
            'geometry.coordinates.0' => 'Longitude',
            'geometry.coordinates.1' => 'Latitude',
            'properties.dataset' => 'Original Dataset',
            'properties.short_url' => 'Short Url',
            'properties["title"]' => "SMS Message"
        ];
    }

    public function viewAttributes()
    {
        return [
            'dataset',
            'id',
            'type',
            'long_url:url:Long Url',
            'short_url:url',
            'properties.title',
            'properties.popupContent',
            'geometry.type',
            'geometry.coordinates.1',
            'geometry.coordinates.0',
        ];
    }

}
