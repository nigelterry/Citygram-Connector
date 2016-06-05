<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection "message".
 *
 * @property \MongoId|string $_id
 */
class EventMessage extends BaseMessage
{
	use MessageTrait;

	public function modelConstruct()
	{
		$this->pageTitle = 'Road Closure Message';
	}

    public function modelViewAttributes()
    {
        return [
            'id' => '',
            'type' => '',
            'dataset' => '',
	        'datetime.sec' => ':date',
	        'center.coordinates.1' => '',
	        'center.coordinates.0' => '',
            'short_url' => ':url',
            'properties.title' => '',
            'properties.popupContent' => '',
        ];
    }

	public function modelIndexAttributes()
	{
		return [
			'id' => '',
			'type' => '',
			'dataset' => '',
			'datetime.sec' => ':date',
			'center.coordinates.1' => '',
			'center.coordinates.0' => '',
			'short_url' => ':url',
			'properties.title' => '',
			'properties.popupContent' => '',
		];
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
            'geometry.coordinates.0' => 'lng',
            'geometry.coordinates.1' => 'lat',
            'dataset' => 'Original Dataset',
            'short_url' => 'Url',
            'center.coordinates.1' => 'Center long',
            'center.coordinates.0' => 'Center lat',
        ];
    }

}
