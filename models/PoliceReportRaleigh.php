<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for collection "police_report".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 */
class PoliceReportRaleigh extends BaseReport
{

    use ReportTrait;

    public function modelConstruct()
    {
        $this->pageTitle   = 'Raleigh Police Report';
        $this->messageType = 'CrimeMessage';
        $this->datasetName = 'Raleigh Police Report';
    }

    /**
     * @inheritdoc
     */
    public function modelAttributes()
    {
        return
            [
                'properties.inc_datetime',
                'properties.lcr_desc',
                'properties.district',
                'properties.lcr',
                'properties.inc_no',
            ];
    }

    /**
     * @inheritdoc
     */
    public function modelAttributeLabels()
    {
        return
            [
                'properties.inc_datetime' => 'Date / Time',
                'properties.lcr_desc'     => 'Description',
                'properties.district'     => 'District',
                'properties.lcr'          => 'LCR',
                'properties.inc_no'       => 'Incident #',
            ];
    }

    public function modelViewAttributes()
    {
        return [
            'properties.lcr_desc'    => '',
            'properties.inc_no'      => '',
            [
                'attribute' => 'datetime',
                'format'    => 'datetime',
                'label'     => 'Incident Date / Time',
                'value'     => $this->datetime->toDateTime()
            ],
            'dataset'                => '',
            'geometry.coordinates.1' => '',
            'geometry.coordinates.0' => '',
        ];
    }

    public function modelIndexAttributes()
    {
        return [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'datetime',
                'format'    => 'datetime',
                'label'     => 'Incident Date / Time',
                'value'     => function ($model, $key, $index, $column) {
                    return !empty($model->datetime) ? $model->datetime->toDateTime() : null;
                }
            ],
            'id'      => '',
            'dataset' => '',
            [
                'attribute' => 'created_at',
                'format'    => 'date',
                'label'     => 'Added to DB at',
                'value'     => function ($model, $key, $index, $column) {
                    return !empty($model->created_at) ? $model->created_at->toDateTime() : null;
                }
            ],
            [
                'attribute' => 'updated_at',
                'format'    => 'date',
                'label'     => 'Updated at',
                'value'     => function ($model, $key, $index, $column) {
                    return !empty($model->updated_at) ? $model->updated_at->toDateTime() : null;
                }
            ],
        ];
    }


    public function title($shortUrl)
    {
        $properties = (object)$this->properties;

        return 'Crime incident HERE->' . $shortUrl . ' ' .
               $this->datetime->toDateTime()->format('D M j \a\t g:ia') .
               '. Raleigh Police described as ' .
               (isset($properties->lcr_desc) ? $properties->lcr_desc : 'undefined');
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;

        return Html::tag('div',
            'Crime incident #' . $properties->inc_no .
            $this->datetime->toDateTime()->format('D M j \a\t g:ia') .
            '. Raleigh Police described as ' .
            (isset($properties->lcr_desc) ? $properties->lcr_desc : 'undefined'), ['class' => 'popup-body']) .
               $this->linkBlock();
    }

    public function datetime($record)
    {
        return new \MongoDB\BSON\UTCDateTime(strtotime($record->inc_datetime) * 1000);
    }

    public function id($record)
    {
        return 'Raleigh_' . $record->inc_no;
    }

    public function properties($record)
    {
        $r = clone $record;
        unset($r->geometry);

        return $r;
    }

    public function geometry($record)
    {
        return (object)(! empty($record->location) ? $record->location : null);
    }

    public function other($record)
    {
        return null;
    }

    public function getData($days, $start, $rows, &$nhits)
    {
        $appToken = 'oru4D4moiJgtp0waK9fX01XtW';
        $url      = 'https://data.raleighnc.gov/resource/emea-ai2t.json' .
                    '?$where=' . urlencode('inc_datetime > "' . date('Y-m-d',
                    strtotime("-$days days")) . '"') .
                    '&$order=' . urlencode('inc_datetime DESC') .
                    '&$offset=' . $start .
                    '&$limit=' . $rows .
                    '&$$app_token=' . $appToken;
        $data     = json_decode(file_get_contents($url));
        $nhits    = $nhits + count($data);

        return $data;
    }
}
