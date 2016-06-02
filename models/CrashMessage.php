<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection 'message'.
 *
 * @property \MongoId|string $_id
 */
class CrashMessage extends BaseMessage
{

	use MessageTrait;

	public function modelConstruct()
	{
		$this->pageTitle = 'Crash Message';
	}
	
    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            '_id' => 'MongoID',
            'id' => 'Unique ID',
            'datetime' => 'Date / Time',
            'type' => 'Message Type',
            'dataset' => 'Data Set',
            'properties' => 'Message',
            'properties.dataset' => 'Original Dataset',
        ];
    }

    public function modelViewAttributes()
    {
        return [
            'dataset' => '',
            'id' => '',
            'type' => '',
            'long_url' => ':url',
            'short_url' => ':url',
            'properties.title' => '',
            'properties.popupContent' => '',
            'geometry.type' => '',
            'geometry.coordinates.1' => '',
            'geometry.coordinates.0' => '',
        ];
    }

}
