<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 6/2/16
 * Time: 5:04 PM
 */

namespace app\models;


use yii\mongodb\ActiveRecord;

abstract class BaseReport extends ActiveRecord{

	abstract public function title($url);

	abstract public function popupContent();

	abstract public function datetime($record);

	abstract public function id($record);

	abstract public function properties($record);

	abstract public function geometry($record);

	abstract public function other($record);

	abstract public function getData($days, $start, $rows, &$nhits);  

}