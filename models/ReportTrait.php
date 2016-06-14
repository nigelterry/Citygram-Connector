<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 6:25 PM
 */

namespace app\models;

use \yii\helpers\Html;
use yii\helpers\Inflector;

Trait ReportTrait {

	use ModelTrait;
	
	public $requiredProperties = [ 'datasetName', 'messageType', 'messageUrl', 'pageTitle' ];
	public $datasetName;
	public $messageType;
	public $messageUrl;


	public function construct() {

	}

	function linkBlock() {
		return Html::tag( 'div',
			Html::a( 'Details', [ $this->urlName . '/item', 'id' => (string) $this->_id ],
				[ 'class' => 'details-link' ] ) .
			Html::a( 'Center', [ $this->messageUrl . '/map', 'id' => (string) $this->_id ],
				[ 'class' => 'center-link' ] ),
			[ 'class' => 'popup-links' ] );
	}

	/**
	 * @inheritdoc
	 */
	public function attributes() {
		$atts = array_merge( $this->baseAttributes(),
			[
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
				'type'
			] );

		if ( method_exists( $this, 'modelAttributes' ) ) {
			$atts = array_merge( $atts, $this->modelAttributes() );
		}

		return $atts;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		$atts = array_merge( $this->baseAttributeLabels(),
			[
				'dataset'              => 'Data Set',
			] );

		if ( method_exists( $this, 'modelAttributeLabels' ) ) {
			$atts = array_merge( $atts, $this->modelAttributeLabels() );
		}

		return $atts;
	}

	/**
	 * @inheritdoc
	 */
	public function viewAttributes() {
		$atts = array_merge( $this->baseViewAttributes(),
			[
                [
                    'attribute' => 'datetime',
                    'format'    => 'datetime',
                    'label'     => 'Incident Date / Time',
                    'value'     => $this->datetime->toDateTime()
                ],
				'dataset' => '',
			] );

		if ( method_exists( $this, 'modelViewAttributes' ) ) {
			$atts = array_merge( $atts, $this->modelViewAttributes() );
		}
		return $this->flatten($atts);
	}

	/**
	 * @inheritdoc
	 */
	public function indexAttributes() {
		$atts = array_merge( $this->baseIndexAttributes(),
			[

			] );

		if ( method_exists( $this, 'modelIndexAttributes' ) ) {
			$atts = array_merge( $atts, $this->modelIndexAttributes() );
		}
		return $this->flatten($atts);
	}


	/**
	 * @inheritdoc
	 */
	public function rules() {
		$atts = array_merge( $this->baseRules(),
			[
				[ [ '_id', 'datetime' ], 'safe' ],
			] );

		if ( method_exists( $this, 'modelRules' ) ) {
			$atts = array_merge( $atts, $this->modelRules() );
		}

		return $atts;
	}

	/**
	 * @inheritdoc
	 */
	public function center( $record ) {
		return null;
	}

	public function download( $days ) {
		proc_nice( 19 );
		$start   = 0;
		$rows    = 100;
		$new     = 0;
		$updates = 0;
		$hasgeo  = 0;
		$nhits   = 0;
		do {
			ini_set( 'max_execution_time', 1000 );
			$records = $this->getData( $days, $start, $rows, $nhits );
			$start   = $start + $rows;
			foreach ( $records as $record ) {
				$id     = $this->id( $record );
				$m      = $this->className();
				$report = $m::find()->where( [ 'id' => $id ] )->one();
				if ( $report === null ) {
					$report             = new $m;
					$report->_id        = new \MongoDB\BSON\ObjectID;
					$report->dataset    = $this->datasetName;
					$report->id         = $id;
					$report->created_at = new \MongoDB\BSON\UTCDateTime( time() * 1000 );
					$report->datetime   = $this->datetime( $record );
					if ( $report->datetime === false ) {
						file_put_contents( 'runtime/logs/BadDurhamPermitScrape', print_r( $record ) );
						break;
					}
					$report->properties = $this->properties( $record );
					$report->other      = $this->other( $record );
					$report->geometry   = $this->geometry( $record );
					$report->center     = $this->center( $record );
					if ( ! empty( $report->geometry ) ) {
						$hasgeo ++;
						$m = 'app\\models\\' . $this->messageType;
						if ( ( $message = $m::find()->where( [ 'id' => $id ] )->one() ) === null ) {
							$model = new $m;
							$model->buildMessage( $report );
						} else {
							echo 'We should never get here. Message present for new Report' . "\n";
						}
					}
					$new ++;
					$report->save();
				} else {
					$updates ++;
				}
			}
		} while ( count( $records ) != 0 );

		return [ 'total' => $nhits, 'new' => $new, 'updates' => $updates, 'hasgeo' => $hasgeo, 'days' => $days ];
	}

	public function rebuild() {
		proc_nice( 19 );
		$limit   = 100;
		$offset  = 0;
		$hasgeo  = 0;
		$updates = 0;
		while ( $reports = self::find()->limit( $limit )->offset( $offset )->all() ) {
			foreach ( $reports as $report ) {
				if ( isset( $report->geometry ) ) {
					$searchClass = '\\app\\models\\' . $this->messageType . 'Search';
					if ( $message = $searchClass::find()
					                            ->where( [ 'id' => $report->id ] )
					                            ->orWhere( [ '_id' => $report->_id ] )
					                            ->one()
					) {
						$message->reBuildMessage( $report );
					} else {
						if ( isset( $message->errors ) ) {
							file_put_contents( __DIR__ . "/../runtime/logs/errordump.log",
								'Debug 4: ' . print_r( $message, true ) . "\n" . print_r( $report, true ),
								FILE_APPEND );
						}
						$m       = 'app\\models\\' . $this->messageType;
						$message = new $m;
						$message->buildMessage( $report );
					}
					$hasgeo ++;
				}
				$updates ++;
			}
			$offset += $limit;
		}

		return [ 'total' => 0, 'new' => 0, 'updates' => $updates, 'hasgeo' => $hasgeo, 'days' => 'n/a' ];
	}

}