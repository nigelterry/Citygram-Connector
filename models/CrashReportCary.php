<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection "police_report".
 *
 * @property \MongoId|string $_id
 */
class CrashReportCary extends BaseReport
{

	use ReportTrait;

	private $count = 0;

	public function modelConstruct()
	{
		$this->pageTitle = 'Cary Crash Report';
		$this->datasetName = 'Cary Crash Report';
		$this->messageType = 'CrashMessage';
	}

    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            'dataset' => 'Source',
            'properties.lightcond' => 'Light Conditions',
            'properties.location_description' => 'Location',
            'properties.injuries' => 'Injuries',
            'properties.vehicle1' => 'First Vehicle',
            'properties.vehicle2' => 'Second Vehicle',
            'properties.weather' => 'Weather Conditions',
        ];
    }

    public function modelViewAttributes(){
        return [
            'dataset' => '',
	        'properties.lightcond' => '',
	        'properties.weather' => '',
	        'properties.location_description' => '',
	        'properties.injuries' => '',
	        'properties.vehicle1' => '',
	        'properties.vehicle2' => '',
            'properties.lat' => '',
            'properties.lon' => ''
        ];
    }

	public function modelIndexAttributes(){
		return [
			'dataset' => '',
			'properties.lightcond' => '',
			'properties.weather' => '',
			'properties.location_description' => '',
			'properties.injuries' => '',
			'properties.vehicle1' => '',
			'properties.vehicle2' => '',
			'properties.lat' => '',
			'properties.lon' => ''
		];
	}

    public function title($shortUrl)
    {
        $properties = (object)$this->properties;
        return 'Crash HERE->' . $shortUrl . ' ' .
        date('D M j \a\t g:ia',
            $this->datetime->sec) .
        ' between a ' .  ' and ' ;
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Crash incident #' . $properties->tamainid .
        date(' D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Cary Police described as ' .
        ' between a ' . ' and ' , ['class' => 'popup-body']) .
                $this->linkBlock();
    }

    public function datetime($record)
    {
        $properties = $record->fields;
        return new \MongoDate(strtotime($properties->crash_date));
    }

    public function id($record){
        return 'Cary_' . $record->fields->tamainid;
    }

    public function properties($record){
        return (object)$record->fields;
    }

    public function geometry($record){
        return (object)($record->geometry ? $record->geometry : null);
    }

    public function other($record){
        $other = new \stdClass();
        $other->datasetid = $record->datasetid;
        $other->recordid = $record->recordid;
        $other->record_timestamp = $record->record_timestamp;
        return $other;
    }

    public function getData($days, $start, $rows, &$nhits){
        $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=cpd-crash-incidents' .
            '&q=' . urlencode("crash_date > #now(days=-$days)") .
            '&sort=crash_date' .
            '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }

}
