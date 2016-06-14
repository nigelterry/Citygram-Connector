<?php

namespace app\models;

use Yii;

/**
 * This is the model class for collection "message".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 */
class ZoningMessage extends BaseMessage
{
	use MessageTrait;

	public function modelConstruct()
	{
		$this->pageTitle = 'Rezoning Message';
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
