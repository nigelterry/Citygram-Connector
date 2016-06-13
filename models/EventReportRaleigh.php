<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;
use yii\mongodb\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for collection "police_report".
 *
 * @property \MongoId|string $_id
 */
class EventReportRaleigh extends BaseReport {

	use ReportTrait;

	public function modelConstruct() {
		$this->pageTitle   = 'Raleigh Event Report';
		$this->messageType = 'EventMessage';
		$this->datasetName = 'Raleigh Event Report';
	}

	/**
	 * @inheritdoc
	 */
	public function modelAttributes() {
		return
			[
				/*				'properties.event_startdate',
								'properties.lcr_desc',
								'properties.objectid',
								'properties.lcr',
								'properties.inc_no',*/
			];
	}

	/**
	 * @inheritdoc
	 */
	public function modelAttributeLabels() {
		return
			[
				/*                'properties.inc_datetime' => 'Date / Time',
								'properties.lcr_desc' => 'Description',
								'properties.district' => 'District',
								'properties.lcr' => 'LCR',
								'properties.inc_no' => 'Incident #',*/
			];
	}

	public function modelViewAttributes() {
		return [
			/*			'properties.lcr_desc' => '',
						'properties.inc_no' => '',
						'datetime.sec' => ':datetime',
						'dataset' => '',
						'geometry.coordinates.1' => '',
						'geometry.coordinates.0' => '',*/
		];
	}

	public function modelIndexAttributes() {
		return [
			[ 'class' => 'yii\grid\SerialColumn' ],
			[ 'attribute' => 'datetime.sec', 'format' => 'date', 'label' => 'Event Date' ],
			'properties.event_name' => '',
			[ 'attribute' => 'properties.setup_starttime', 'format' => 'time', 'label' => 'Start Time' ],
			[ 'attribute' => 'properties.breakdown_endtime', 'format' => 'time', 'label' => 'End Time' ],
			[ 'attribute' => 'created_at.sec', 'format' => 'date', 'label' => 'Added to DB at' ],
			[ 'attribute' => 'updated_at.sec', 'format' => 'date', 'label' => 'Updated at' ],
		];
	}


	public function title( $shortUrl ) {
		$properties = (object) $this->properties;

		return 'Road Closure HERE->' . $shortUrl . ' ' .
		       date( 'D M j', $this->datetime->sec ) .
		       ' from ' . $properties->setup_starttime .
		       ' to ' . $properties->breakdown_endtime .
		       ' ' . ( isset( $properties->event_name ) ? $properties->event_name : 'undefined' );
	}

	public function popupContent() {
		$properties = (object) $this->properties;

		return 'Road Closure ' .
		       date( 'D M j', $this->datetime->sec ) .
		       ' from ' . $properties->setup_starttime .
		       ' to ' . $properties->breakdown_endtime .
		       ( isset( $properties->event_type ) ? ' for a ' . $properties->event_type . '. ' : '' ) .
		       ( isset( $properties->event_name ) ? ' ' . $properties->event_name : '' ) .
		       ( isset( $properties->comments ) ? ' ' . $properties->comments : '' ) .
		       '.' .
		       $this->linkBlock();
	}

	public function datetime( $record ) {
		return new \MongoDate( $record->event_startdate );
	}

	public function id( $record ) {
		return 'Raleigh_' . $record->objectid;
	}

	public function properties( $record ) {
		$r = clone $record;
		unset( $r->geometry );

		return $r;
	}

	public function geometry( $record ) {
		$geometry       = new \stdClass();
		$geometry->type = 'MultiLineString';
		if ( $record->shape->geometry ) {
			$pairs       = $record->shape->geometry->paths[0];
			$coordinates = [ ];
			foreach ( $pairs as $pair ) {
				if ( 0 != $pair[1] && 0 != $pair[1] ) {
					$coordinates[] = $pair;
				}
			}
			$geometry->coordinates[0] = $coordinates;
		} else {
			$geometry->coordinates = null;
		}

		return $geometry;
	}

	public function center( $record ) {
		$center              = new \stdClass();
		$center->type        = 'Point';
		$coordinates         = [ ];
		$coordinates[0]      = (float) $record->shape->longitude;
		$coordinates[1]      = (float) $record->shape->latitude;
		$center->coordinates = $coordinates;

		return $center;
	}

	public function other( $record ) {
		return null;
	}

	public function getData( $days, $start, $rows, &$nhits ) {
		$appToken = 'oru4D4moiJgtp0waK9fX01XtW';
		$url      = 'https://data.raleighnc.gov/resource/jyeq-4twd.json' .
		            '?$where=' . urlencode( 'event_startdate > "' . date( 'Y-m-d',
					strtotime( "-$days days" ) ) . '"' ) .
		            //            '&$order=' . urlencode('event_startdate DESC') .
		            '&$offset=' . $start .
		            '&$limit=' . $rows .
		            '&$$app_token=' . $appToken;
		$data     = json_decode( file_get_contents( $url ) );
		$nhits    = $nhits + count( $data );

		return $data;
	}
}
