<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class PermitReportD extends PermitReport
{

    private $count = 0;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'permit_report_durham'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'permit-report-d';
        $this->config->dataset = 'Durham Permit Application';
        $this->config->title = 'Durham Permit Application';

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            '_id' => 'ID',
            'dataset' => 'Data Set',
            "properties.datasetid" => 'Dataset ID'
        ]);
    }

    public function title($url)
    {
        $properties = (object)$this->properties;
        if (!isset($properties->siteadd)) {
            while (false) ;
        }
        return 'New Permit HERE->' . $url . ' ' .
        date(' D M j ',
            $this->datetime->sec) .
        (!empty($properties->p_descript) ? ' Permit type ' .ucwords(strtolower($properties->p_descript)) : "") .
        (isset($properties->siteadd) ? (' at address ' . ucwords(strtolower($properties->siteadd))) : "");
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        if (!isset($properties) || !isset($properties->siteadd)) {
            while (false) ;
        }
        return Html::tag('div',
            'Permit #' . $properties->permit_id .
            date(' D M j',
                $this->datetime->sec) .
            (!empty($properties->p_descript) ? ' Permit type ' .ucwords(strtolower($properties->p_descript)) : "") .
            (isset($properties->siteadd) ? ' at address ' . ucwords(strtolower($properties->siteadd)) . '. ' : ""),
            ['class' => 'popup-body']) .
        $this->linkBlock();
    }

    public function datetime($record)
    {
//        $properties = (object)$this->properties;
        return new \MongoDate(strtotime($record->record_timestamp));
    }

    public function dataset()
    {
        return 'Durham Permit Data';
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
            $url = 'https://durham.opendatasoft.com/api/records/1.0/search/?dataset=active-building-permits' .
                '&q=' . urlencode("record_timestamp > #now(days=-$days)") .
                '&sort=record_timestamp' .
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
                $id = 'Durham_' . $record->fields->permit_id;
                $report = PermitReportD::find()->where(['id' => $id])->one();
                if ($report === null) {
                    if (empty($record->fields->p_descript)) {
                        while (false) ;
                    }
                    $report = new PermitReportD();
                    $report->_id = new \MongoId();
                    $report->dataset = 'Durham Permit Report';
                    $report->id = $id;
                    $report->created_at = new \MongoDate(time());
                    $report->properties = (object)$record->fields;
                    $report->datetime = $report->datetime($record);
                    if (isset($record->geometry)) {
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
