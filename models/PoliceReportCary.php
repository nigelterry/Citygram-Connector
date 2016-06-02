<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "police_report".
 *
 * @property \MongoId|string $_id
 */
class PoliceReportCary extends BaseReport
{

	use ReportTrait;

	public function modelConstruct()
	{
		$this->messageUrl = 'crime-message';
		$this->pageTitle = 'Cary Police Report';
		$this->messageType = 'CrimeMessage';
		$this->datasetName = 'Cary Police Report';
	}

	/**
	 * @inheritdoc
	 */
	public function modelAttributes() {
		return [

		];
	}

    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            'dataset' => 'Source',
            'properties.id' => 'Cary Police ID #',
            'properties.crime_category' => 'Crime Category',
            'properties.crime_type' => 'Crime Type',
            'properties.crimeday' => 'Day of week',
            'properties.date_from' => 'Start Date',
            'properties.from_time' => 'Start Time',
            'properties.date_to' => 'End Date',
            'properties.to_time' => 'End Time',
            'properties.lat' =>'Latitude',
            'properties.lon' => 'Longitude',
            'properties.residential_subdivision' => 'Subdivision',
        ];
    }

    public function modelViewAttributes(){
        return [
            'dataset' => '',
            'properties.id' => '',
            'properties.crime_category' => '',
            'properties.crime_type' => '',
            'properties.crimeday' => '',
            'properties.date_from' => ':date',
            'properties.from_time' => ':time',
            'properties.date_to' => ':date',
            'properties.to_time' => ':time',
            'properties.lat' => '',
            'properties.lon' => '',
            'properties.residential_subdivision' => '',
        ];
    }

    public function title($shortUrl)
    {
        $properties = (object)$this->properties;
        return 'Crime incident HERE->' . $shortUrl . ' ' .
        date('D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Cary Police described as ' .
        (isset($properties->crime_category) ?
            (ucwords(strtolower($properties->crime_category)) . ' ')
            : 
            '') . ucwords(strtolower($properties->crime_type));
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Crime incident #' . $properties->incident_number .
        date(' D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Cary Police described as ' .
        (isset($properties->crime_category) ?
            (ucwords(strtolower($properties->crime_category)) . ' ') :
            '') .
            ucwords(strtolower($properties->crime_type)), ['class' => 'popup-body']) .
                $this->linkBlock();
    }

    public function datetime($record)
    {
        $properties = $record->fields;
        return new \MongoDate(strtotime($properties->date_to));
    }

    public function id($record){
        return 'Cary_' . $record->fields->id;
    }

    public function properties($record){
        return (object)$record->fields;
    }

    public function geometry($record){
        return (object)($record->geometry ?? null);
    }

    public function other($record){
        $other = new \stdClass();
        $other->datasetid = $record->datasetid;
        $other->recordid = $record->recordid;
        $other->record_timestamp = $record->record_timestamp;
        return $other;
    }

    public function getData($days, $start, $rows, &$nhits){
        $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=cpd-incidents' .
            '&q=' . urlencode("date_from > #now(days=-$days)") .
            '&sort=date_from' .
            '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }

}
