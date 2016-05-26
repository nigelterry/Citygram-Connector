<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * This is the model class for collection "police_report".
 *
 * @property \MongoId|string $_id
 */
class PoliceReportR extends PoliceReport
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return ['citygram', 'police_report_raleigh'];
    }

    public function __construct()
    {
        parent::__construct();
        $this->config->urlName = 'police-report-r';
        $this->config->dataset = 'Police Report Raleigh';
        $this->config->title = 'Raleigh Police Report';
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
            [
                'properties.inc_datetime' => 'Date / Time',
                'properties.lcr_desc' => 'Description',
                'properties.district' => 'District',
                'properties.lcr' => 'LCR',
                'properties.inc_no' => 'Incident #',
            ]);
    }
	
	public function viewAttributes(){
		return array_merge( [
			'properties.lcr_desc',
			'properties.inc_no',
		], parent::viewAttributes() );
	}

	public function indexAttributes(){
		return [
			[ 'class' => 'yii\grid\SerialColumn' ],
			[ 'attribute' => 'datetime.sec', 'format' => 'datetime', 'label' => 'Incident Date / Time' ],
			'id',
//			'type',
			'dataset',
			[ 'attribute' => 'datetime.sec', 'format' => 'date', 'label' => 'Report Date / Time' ],
			[ 'attribute' => 'created_at.sec', 'format' => 'date', 'label' => 'Added to DB at' ],
			[ 'attribute' => 'updated_at.sec', 'format' => 'date', 'label' => 'Updated at' ],
			[
				'class'    => 'yii\grid\ActionColumn',
				'template' => '{view} {dump} {map} {item} {update} {delete}',
				'buttons'  => [
					'map'  => function ( $url, $model, $key ) {
						return ( isset( $model->geometry['coordinates'][0] ) &&
						         $model->config->hasMap ) ? Html::a( 'Map', $url ) : '';
					},
					'dump' => function ( $url, $model, $key ) {
						return Html::a( 'Dump', $url );
					},
					/*					'item' => function ( $url, $model, $key ) {
											return Html::a( 'Item', [$model->source_type .'/item', 'id' => (string)$model->source_id] );
										},*/
				],
			],
		];
	}


	public function title($shortUrl)
    {
        $properties = (object)$this->properties;
        return 'Crime incident HERE->' . $shortUrl . ' ' .
        date('D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Raleigh Police described as ' .
        (isset($properties->lcr_desc) ? $properties->lcr_desc : 'undefined');
    }

    public function popupContent()
    {
        $properties = (object)$this->properties;
        return Html::tag('div',
            'Crime incident #' . $properties->inc_no .
        date(' D M j \a\t g:ia',
            $this->datetime->sec) .
        '. Raleigh Police described as ' .
        (isset($properties->lcr_desc) ? $properties->lcr_desc : 'undefined'),['class' => 'popup-body']) .
        $this->linkBlock();
    }

    public function datetime($record)
    {
        return new \MongoDate(strtotime($record->inc_datetime));
    }

    public function id($record){
        return 'Raleigh_' . $record->inc_no;
    }

    public function properties($record){
        $r = clone $record;
        unset($r->geometry);
        return $r;
    }

    public function geometry($record){
        return (object)($record->location ?? null);
    }
    
    public function other($record){
        return null;
    }

    public function getData($days, $start, $rows, &$nhits){
        $appToken = 'oru4D4moiJgtp0waK9fX01XtW';
        $url = 'https://data.raleighnc.gov/resource/emea-ai2t.json' .
            '?$where=' . urlencode('inc_datetime > "' . date('Y-m-d',
                    strtotime("-$days days")) . '"') .
            '&$order=' . urlencode('inc_datetime DESC') .
            '&$offset=' . $start .
            '&$limit=' . $rows .
            '&$$app_token=' . $appToken;
        $data = json_decode(file_get_contents($url));
        $nhits = $nhits + count($data);
        return $data;
    }
}
