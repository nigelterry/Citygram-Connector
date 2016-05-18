<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class ZoningReportC extends ZoningReport
{
    private $count = 0;
    
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'zoning_report_cary'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'zoning-report-c';
        $this->config->dataset = 'Cary Rezoning Application';
        $this->config->title = 'Cary  Rezoning Application';

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
        return (object)($properties->geo_shape ?? null);
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

    /*public function download($days)
    {
        proc_nice(19);
        ini_set('max_execution_time', 1000);
        $start = 0;
        $rows = 100;
        $new = 0;
        $updates = 0;
        $hasgeo = 0;
        do {
            $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=rezonings' .
                '&q=' . urlencode("submittal > #now(days=-$days)") .
                '&sort=submittal' .
                '&start=' . $start . '&rows=' . $rows;
            echo $url . "\n";
            $data = file_get_contents($url);
            $data = json_decode($data);
            $nhits = $data->nhits;
            $records = $data->records;
            $start = $start + $rows;
            $count = 0;
            foreach ($records as $record) {
                $count++;
                $id = 'Cary_' . $record->fields->permitnum;
                $report = PermitReportC::find()->where(['id' => $id])->one();
                if ($report === null) {
                    $report = new PermitReportC();
                    $report->_id = new \MongoId();
                    $report->dataset = $this->config->dataset;
                    $report->id = $id;
                    $report->created_at = new \MongoDate(time());
                    $report->properties = (object)$record->fields;
                    $report->datetime = $report->datetime($report);
                    if(isset($record->geometry)) {
                        $report->geometry = (object)$record->geometry;
                    }
                    $report->other = new \stdClass();
                    $report->other->datasetid = $record->datasetid;
                    $report->other->recordid = $record->recordid;
                    $report->other->record_timestamp = $record->record_timestamp;
                    $new++;
                    $report->save();
                    if (isset($record->geometry->coordinates)) {
                        $hasgeo++;
                        if (($message = PermitMessage::find()->where(['id' => $id])->one()) === null) {
                            $model = new PermitMessage();
                            $model->buildMessage($report);
                        } else {
                            echo 'We should never get here. Message present for new Report' . "\n";
                        }
                    }
                } else {
                    $updates++;
                }
            }
        } while (count($records) != 0);

        return ['total' => $nhits, 'new' => $new, 'updates' => $updates, 'hasgeo' => $hasgeo, 'days' => $days];
    }*/
}
