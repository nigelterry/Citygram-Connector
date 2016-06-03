<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class ZoningReportDurham extends BaseReport
{
	use ReportTrait;

	private $count = 0;

	public function modelConstruct()
	{
		$this->pageTitle = 'Durham Re-zoning Report';
		$this->messageType = 'ZoningMessage';
		$this->datasetName = 'Durham Zoning Report';
	}

    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return [
            '_id' => 'ID',
            'dataset' => 'Data Set',
            "properties.datasetid" => 'Dataset ID'
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function modelViewAttributes()
	{
		return [
			'datetime.sec' => ':date',
			'properties.zone_gen' => '',
			'properties.zone_code' => '',
			'properties.shape_area' => ''
		];
	}

    public function title($url)
    {
        $properties = (object)$this->properties;
        return 'New Zoning Application HERE->' . $url . ' ' .
            date(' D M j \a\t g:ia',
            $this->datetime->sec) .
            (!empty($properties->zone_gen) ? ' Zoning type ' .ucwords(strtolower($properties->zone_gen)) : "");
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Rezoning #' . $properties->objectid .
            date(' D M j \a\t g:ia',
                $this->datetime->sec) .
            (!empty($properties->zone_gen) ? ' Permit case ' .ucwords(strtolower($properties->zone_gen)) : "")) .
            $this->linkBlock();
    }

    public function datetime($record)
    {
//
        return new \MongoDate(strtotime($record->fields->city_counc));
    }

    public function id($record){
        $properties = $record->fields;
        return 'Durham_' . $properties->objectid ;
    }

    public function properties($record){
        return (object)$record->fields;
    }

    public function geometry($record){
        $properties = $record->fields;
        return (object)($properties->geo_shape ?? null);
    }

    public function center($record){
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
        $url = 'https://opendurham.nc.gov/api/records/1.0/search/?dataset=zoning' .
            '&q=' . urlencode("city_counc > #now(days=-$days)") .
            '&sort=city_counc' .
            '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }

}
