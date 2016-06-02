<?php

namespace app\models;

use Yii;
use \yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection 'message'.
 *
 * @property \MongoId|string $_id
 */
class CrimeMessage extends BaseMessage
{
	use MessageTrait;

    public function modelConstruct()
    {
        $this->pageTitle = 'Crime Message';
    }

	/**
	 * @inheritdoc
	 *
	 * This method can be removed if it returns an empty array
	 */
	public function modelAttributes()
	{
		return [

		];
	}

    /**
     * @inheritdoc
     *
     * This method can be removed if it returns an empty array
     */
    public function modelAttributeLabels()
    {
        return [

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
