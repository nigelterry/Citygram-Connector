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
class PoliceReportDurham extends BaseReport
{

	use ReportTrait;

	public function modelConstruct()
	{
		$this->pageTitle = 'Durham Police Report';
		$this->messageType = 'CrimeMessage';		
		$this->datasetName = 'Durham Police Report';
	}

    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            'dataset' => 'Source',
            'properties.inci_id' => 'Durham Police ID #',
            'properties.reportedas' => 'Reported as',
            'properties.properties.chrgdesc' => 'Charge Description',
            'properties.crimeday' => 'Day of week',
            'datetime.sec' => 'Incident Date & Time',
            'geometry.coordinates.1' =>'Latitude',
            'geometry.coordinates.0' => 'Longitude',
            'properties.big_zone' => 'Police Zone',
        ];
    }

    /**
     * @inheritdoc
     */
    public function modelViewAttributes(){
        return [
            'dataset' => '',
            'properties.inci_id' => '',
            'properties.reportedas' => '',
            'properties.chrgdesc' => '',
            'datetime.sec' => ':datetime',
            'geometry.coordinates.1' => '',
            'geometry.coordinates.0' => '',
            'properties.big_zone',
        ];
    }

    /**
     * @inheritdoc
     */    public function title($shortUrl)
    {
        $properties = (object)$this->properties;
        return 'Crime incident HERE->' . $shortUrl . ' ' .
        date('D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Reported as ' .
        (isset($properties->reportedas) ?
            (ucwords(strtolower($properties->reportedas)) . ' ')
            : '') .
            ' The Durham Police charge was ' .
            ucwords(strtolower($properties->chrgdesc));
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Crime incident #' . $properties->inci_id .
        date(' D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Durham Police described as ' .
        (isset($properties->reportedas) ?
            (ucwords(strtolower($properties->reportedas)) . ' ')
            :
            '') . ucwords(strtolower($properties->chrgdesc)), ['class' => 'popup-body']) .
        $this->linkBlock();
    }

    public function datetime($record)
    {
        $properties = (object)$record->fields;
        return new \MongoDate(strtotime(substr($properties->date_occu, 0, 10) . ' ' .
            substr($properties->hour_rept, 0, 2) . ':' .
            substr($properties->hour_rept, 2, 2)));
    }

    public function id($record){
        return 'Durham_' . $record->fields->inci_id;
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
        $url = 'https://durham.opendatasoft.com/api/records/1.0/search?dataset=durham-police-crime-reports' .
            '&q=' . urlencode("date_occu > #now(days=-$days)") .
            '&sort=date_occu' .
            '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }

}
