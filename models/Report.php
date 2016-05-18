<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 6:25 PM
 */

namespace app\models;

use \yii\helpers\Html;

abstract class Report extends Base
{
    abstract public function title($url);

    abstract public function popupContent();

    abstract public function datetime($record);

    abstract public function id($record);

    abstract public function properties($record);

    abstract public function geometry($record);

    abstract public function other($record);

    abstract public function getData($days, $start, $rows, &$nhits);    
   
    function linkBlock(){
        return Html::tag('div',
            Html::a('Details', [$this->config->urlName . '/item', 'id' => (string)$this->_id], ['class' => 'details-link']) .
            Html::a('Center', [$this->config->messageUrl . '/map', 'id' => (string)$this->_id], ['class' => 'center-link']),
            ['class' => 'popup-links']);
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'id',
            'dataset',
            'properties',
            'geometry',
            'created_at',
            'updated_at',
            'datetime',
            'other',
            'center',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'dataset' => 'Data Set',
            'geometry.coordinates.1' => 'Lat',
            'geometry.coordinates.0' => 'lng',
            'datetime.sec' => 'DateTime',
        ];
    }

    /**
     * @inheritdoc
     */
    public function viewAttributes()
    {
        return array_merge(parent::viewAttributes(), [
            'datetime.sec:datetime',
            'id',
            'dataset',
            'geometry.coordinates.1',
            'geometry.coordinates.0',
        ]);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'datetime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function center($record){
        return null;
    }

    public function download($days)
    {
        proc_nice(19);
        $start = 0;
        $rows = 100;
        $new = 0;
        $updates = 0;
        $hasgeo = 0;
        $nhits = 0;
        do {
            ini_set('max_execution_time', 1000);
            $records = $this->getData($days, $start, $rows, $nhits);
            $start = $start + $rows;
            foreach ($records as $record) {
                $id = $this->id($record);
                $m = $this->className();
                $report = $m::find()->where(['id' => $id])->one();
                if ($report === null) {
                    $report = new $m;
                    $report->_id = new \MongoId();
                    $report->dataset = $this->config->dataset;
                    $report->id = $id;
                    $report->created_at = new \MongoDate(time());
                    $report->datetime = $this->datetime($record);
                    $report->properties = $this->properties($record);
                    $report->other = $this->other($record);
                    $report->geometry = $this->geometry($record);
                    $report->center = $this->center($record);
                    if (!empty($report->geometry)) {
                        $hasgeo++;
                        $m = 'app\\models\\' . $this->config->messageType;
                        if (($message = $m::find()->where(['id' => $id])->one()) === null) {
                            $model = new $m;
                            $model->buildMessage($report);
                        } else {
                            echo 'We should never get here. Message present for new Report' . "\n";
                        }
                    }
                    $new++;
                    $report->save();
                } else {
                    $updates++;
                }
            }
        } while (count($records) != 0);
        return ['total' => $nhits, 'new' => $new, 'updates' => $updates, 'hasgeo' => $hasgeo, 'days' => $days];
    }

    public function rebuild()
    {
        proc_nice(19);
        $limit = 100;
        $offset = 0;
        $hasgeo = 0;
        $updates=0;
        while($reports = self::find()->limit($limit)->offset($offset)->all()) {
            foreach ($reports as $report) {
                if (isset($report->geometry)) {
                    $searchClass = '\\app\\models\\' . $this->config->messageType . 'Search';
                    if ($message = $searchClass::find()
                        ->where(['id' => $report->id])
                        ->orWhere(['_id' => $report->_id])
                        ->one()) {
                        $message->reBuildMessage($report);
                    } else {
                        if(isset($message->errors)){
                            file_put_contents(__DIR__ . "/../runtime/logs/errordump.log",
                                'Debug 4: ' . print_r($message, true) . "\n" .print_r($report, true), FILE_APPEND);
                        }
                        $m = 'app\\models\\' . $this->config->messageType;
                        $message = new $m;
                        $message->buildMessage($report);
                    }
                    $hasgeo++;
                }
                $updates++;
            }
            $offset += $limit;
        }
        return ['total' => 0, 'new' => 0, 'updates' => $updates, 'hasgeo' => $hasgeo, 'days' => 'n/a'];
    }
}