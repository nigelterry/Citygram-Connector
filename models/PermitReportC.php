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
        $properties = (object)$this->properties;
        return new \MongoDate(strtotime($properties->applieddate));
    }

    public function download($days)
    {
        proc_nice(19);
        ini_set('max_execution_time', 1000);
        $start = 0;
        $rows = 100;
        $new = 0;
        $updates = 0;
        $hasgeo = 0;
        do {
            $url = 'https://data.townofcary.org/api/records/1.0/search/?dataset=permit-applications' .
                '&q=' . urlencode("applieddate > #now(days=-$days)") .
                '&sort=applieddate' .
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
    }
}
