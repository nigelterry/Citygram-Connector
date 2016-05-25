<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class PermitReportC extends PermitReport
{
    private $count = 0;
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'permit_report_cary'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'permit-report-c';
        $this->config->dataset = 'Cary Permit Application';
        $this->config->title = 'Cary Permit Application';

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            '_id' => 'ID',
            'dataset' => 'Data Set',
            "properties.datasetid" => 'Dataset ID'
        ]);
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
        (!empty($properties->description) ? ' Permit type ' .ucwords(strtolower($properties->description)) : "") .
        (isset($properties->owneraddress1) ? (' at address ' . ucwords(strtolower($properties->owneraddress1))) : "") .
        ' project cost $' . $properties->projectcost;
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        if(!isset($properties) || !isset($properties->owneraddress1)){
            while(false);
        }
        return Html::tag('div',
            'Permit #' . $properties->permitnum .
            date(' D M j \a\t g:ia',
                $this->datetime->sec) .
            (!empty($properties->description) ? ' Permit type ' .ucwords(strtolower($properties->description)) : "") .
            (isset($properties->owneraddress1) ? ' at address ' . ucwords(strtolower($properties->owneraddress1)) . '. ' : "") .
            'Project cost $' . $properties->projectcost , ['class' => 'popup-body']) .
        $this->linkBlock();
    }

    public function datetime($record)
    {
        $properties = (object)$record->fields;
        return new \MongoDate(strtotime($properties->applieddate));
    }

    public function id($record){
        return 'Cary_' . $record->fields->permitnum;
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
        $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=permit-applications' .
               '&q=' . urlencode("applieddate > #now(days=-$days)") .
               '&sort=applieddate' .
               '&start=' . $start . '&rows=' . $rows;
        $data = json_decode(file_get_contents($url));
        $nhits = $data->nhits;
        return $data->records;
    }
	
}
