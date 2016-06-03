<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class ZoningReportCary extends BaseReport
{

	use ReportTrait;

	private $count = 0;

	public function modelConstruct()
	{
		$this->pageTitle = 'Cary Re-zoning Report';
		$this->messageType = 'ZoningMessage';
		$this->datasetName = 'Cary Zoning Report';
	}

	/**
	 * @inheritdoc
	 */
	public function modelViewAttributes()
	{
		return [
			'dataset' => '',
			'properties.casename' => '',
			'center.coordinates.1' => '',
			'center.coordinates.0' => '',
			'datetime.sec' => ':date',
			'properties.current_zo' => '',
			'properties.proposed_z' => '',
			'properties.acreage' => ''
		];
	}

	/**
	 * @inheritdoc
	 */
	public function modelIndexAttributes()
	{
		return [
			'dataset' => '',
			'properties.casename' => '',
			'center.coordinates.1' => '',
			'center.coordinates.0' => '',
			'datetime.sec' => ':date',
			'properties.current_zo' => '',
			'properties.proposed_z' => '',
			'properties.acreage' => ''
		];
	}

	/**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            'dataset' => 'Data Set',
            'properties.casename' => 'Case Name',
            'properties.datasetid' => 'Dataset ID',
            'center.coordinates.1' => 'Center lat',
            'center.coordinates.0' => 'Center long',
	        'datetime.sec' => 'Submittal Date',
            'properties.current_zo' => 'Current Zoning',
            'properties.proposed_z' => 'Proposed Zoning',
            'properties.acreage' => 'Acreage'

        ];
    }

    public function title($url)
    {
        $properties = (object)$this->properties;
        if(!isset($properties->owneraddress1)){
            while(false);
        }
        return 'New Permit HERE->' . $url . ' ' .
        date(' D M j \a\t g:ia',
            $this->datetime->sec) .
        (!empty($properties->description) ? ' Zoning case ' .ucwords(strtolower($properties->casename)) : "");
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Rezoning #' . $properties->rezoneid .
            date(' D M j \a\t g:ia',
                $this->datetime->sec) .
            (!empty($properties->casename) ? ' Permit case ' .ucwords(strtolower($properties->casename)) : "")) .
            $this->linkBlock();
    }

    public function datetime($record)
    {
        $properties = (object)$record->fields;
        return new \MongoDate(strtotime($properties->submittal));
    }

    public function id($record){
        return 'Cary_' . $record->fields->rezoneid;
    }

    public function properties($record){
        return (object)$record->fields;
    }

    public function geometry($record){
        $properties = $record->fields;
        return (object)($properties->geo_shape ? $properties->geo_shape : null);
    }

    public function center($record){
        return (object)($record->geometry);
    }

    public function other($record){
        $other = new \stdClass();
        $other->datasetid = $record->datasetid;
        $other->recordid = $record->recordid;
        $other->record_timestamp = $record->record_timestamp;
        return $other;
    }

    public function getData($days, $start, $rows, &$nhits){
        $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=rezonings' .
            '&q=' . urlencode("submittal > #now(days=-$days)") .
            '&sort=submittal' .
            '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }

}
