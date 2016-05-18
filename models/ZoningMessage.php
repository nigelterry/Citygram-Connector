<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "message".
 *
 * @property \MongoId|string $_id
 */
class ZoningMessage extends Message
{
    public function titleString(){
        return 'Rezoning Applications Applications';
    }
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'zoning_message'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'zoning-message';
        $this->config->title = 'Rezoning Application';
    }

    public function viewAttributes()
    {
        return array_merge(parent::viewAttributes(), [
            'id',
            'type',
            'dataset',
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
