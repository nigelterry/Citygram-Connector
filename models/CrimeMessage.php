<?php

namespace app\models;

use Yii;
use \yii\mongodb\ActiveRecord;

/**
 * Class CrimeMessage
 * @package app\models
 *
 * This class handles the MESSAGE model
 *
 */
class CrimeMessage extends BaseMessage   // All MESSAGE models extend from BaseMessage
{
	use MessageTrait;   // All MESSAGE models use MessageTrait to add code

    public function modelConstruct()
    {
        $this->pageTitle = 'Crime Message';  // Page title used for view, dump and index pages
    }

	/**
	 * @inheritdoc
	 *
	 * This method can be removed if it returns an empty array
	 *
	 * returns an array or attributes for the MESSAGE model
	 *
	 * Note that _id, id, source_type, type, dataset, datetime, long_url, short_url, properties, geometry,
	 * created_at and updated_at are already defined
	 *
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
     *
     * returns an associative array of keys = attibute name, value = user friendly label
     */
    public function modelAttributeLabels()
    {
        return [

        ];
    }

	/**
	 * @inheritdoc
	 *
	 * This method can be removed if it returns an empty array
	 *
	 * This configures the fields shown in the view, item and index pages
	 *
	 * associative array of MESSAGE attributes for the item and view pages. Keys are the attribute names.
	 * values are format strings. See Yii2 docs for full details, or use ':date', ':datetime', ':time'
	 *
	 */
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
