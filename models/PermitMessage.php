<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "message".
 *
 * @property \MongoId|string $_id
 */
class PermitMessage extends Message
{
    public function titleString(){
        return 'Permit Applications';
    }
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'permit_message'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'permit-message';
        $this->config->title = 'Permit Application';
    }

    /**
     * @inheritdoc
     */
    public function viewAttributes()
    {
        return array_merge(parent::viewAttributes(), [
            'id',
            'type',
            'dataset',
            'geometry.coordinates.0',
            'geometry.coordinates.1',
            'dataset',
            'short_url:url',
            'properties.title',
            'properties.popupContent',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            '_id' => 'MongoID',
            'id' => 'Unique ID',
            'datetime' => 'Date / Time',
            'type' => 'Message Type',
            'dataset' => 'Data Set',
            'properties' => 'Message',
            'geometry.coordinates.0' => 'lng',
            'geometry.coordinates.1' => 'lat',
            'dataset' => 'Original Dataset',
            'short_url' => 'Url',
        ]);
    }

}
