<?php

namespace app\models;

use Yii;
use \yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection "message".
 *
 * @property \MongoId|string $_id
 */
class PermitMessage extends BaseMessage
{

	use MessageTrait;

	public function modelConstruct()
	{
		$this->pageTitle = 'Permit Message';
	}

    /**
     * @inheritdoc
     */
    public function modelViewAttributes()
    {
        return [
            'id' => '',
            'type' => '',
            'dataset' => '',
            'geometry.coordinates.0' => '',
            'geometry.coordinates.1' => '',
            'dataset' => '',
            'short_url' => ':url',
	        'long_url' => ':url',
            'properties.title' => '',
            'properties.popupContent' => '',
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function modelIndexAttributes()
	{
		return [
			'id' => '',
			'type' => '',
			'dataset' => '',
			'geometry.coordinates.0' => '',
			'geometry.coordinates.1' => '',
			'dataset' => '',
			'short_url' => ':url',
			'long_url' => ':url',
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
        ];
    }

}
