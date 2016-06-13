<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;
use app\components\ScrapeHtml;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class PermitReportDurham extends BaseReport {

	use ReportTrait;

	private $count = 0;

	public function modelConstruct() {
		$this->pageTitle   = 'Durham Permit Report';
		$this->messageType = 'PermitMessage';
		$this->datasetName = 'Durham Permit Report';
	}

	/**
	 * @inheritdoc
	 */
	public function modelAttributeLabels() {
		return [
			'_id'                   => 'ID',
			'id'                    => 'Permit Id',
			'dataset'               => 'Data Set',
			'properties.datasetid'  => 'Dataset ID',
			'properties.siteadd'    => 'Site Addr',
			'properties.p_descript' => 'Legal Description',
			'datetime.sec'          => 'Date',
		];
	}

	public function modelViewAttributes() {
		return [
			'properties.siteadd'    => '',
			'properties.p_descript' => '',
			'properties.status'     => ''
		];
	}

	public function modelIndexAttributes() {
		return [
			[ 'class' => 'yii\grid\SerialColumn' ],
			[ 'attribute' => 'datetime.sec', 'format' => 'datetime', 'label' => 'Incident Date / Time' ],
			'id'                     => '',
			'dataset'                => '',
			'properties.title'       => '',
			'properties.status'      => '',
			'properties.failedFetch' => '',
			[ 'attribute' => 'datetime.sec', 'format' => 'date', 'label' => 'Report Date / Time' ],
			[ 'attribute' => 'created_at.sec', 'format' => 'date', 'label' => 'Added to DB at' ],
			[ 'attribute' => 'updated_at.sec', 'format' => 'date', 'label' => 'Updated at' ],
		];
	}

	public function title( $url ) {
		$properties = (object) $this->properties;
		if ( ! isset( $properties->siteadd ) ) {
			while ( false ) {
				;
			}
		}

		return 'New Permit HERE->' . $url . ' ' .
		       date( ' D M j ',
			       $this->datetime->sec ) .
		       ( ! empty( $properties->p_descript ) ? ' Permit type ' . ucwords( strtolower( $properties->p_descript ) ) : "" ) .
		       ( isset( $properties->siteadd ) ? ( ' at address ' . ucwords( strtolower( $properties->siteadd ) ) ) : "" );
	}

	public function popupContent() {
		$properties = (object) $this->properties;
		if ( ! isset( $properties ) || ! isset( $properties->siteadd ) ) {
			while ( false ) {
				;
			}
		}

		return Html::tag( 'div',
			'Permit #' . $properties->permit_id .
			date( ' D M j',
				$this->datetime->sec ) .
			( ! empty( $properties->p_descript ) ? ' Permit type ' . ucwords( strtolower( $properties->p_descript ) ) : "" ) .
			( isset( $properties->siteadd ) ? ' at address ' . ucwords( strtolower( $properties->siteadd ) ) . '. ' : "" ),
			[ 'class' => 'popup-body' ] ) .
		       $this->linkBlock();
	}

	public function datetime( $record ) {
//        $properties = (object)$this->properties;
		$table = $this->scrape( $record->fields->permit_id );
		if ( $table === false ) {
			return false;
		}
		$record->fields->status = $table[3][2];

		return new \MongoDate( strtotime( $table[3][3] ) );
	}

	public function id( $record ) {
		return 'Durham_' . $record->fields->permit_id;
	}

	public function properties( $record ) {
		return (object) $record->fields;
	}

	public function geometry( $record ) {
		return (object) ( $record->geometry ? $record->geometry : null );
	}

	public function other( $record ) {
		$other                   = new \stdClass();
		$other->datasetid        = $record->datasetid;
		$other->recordid         = $record->recordid;
		$other->record_timestamp = $record->record_timestamp;

		return $other;
	}

	public function getData( $days, $start, $rows, &$nhits ) {
		$url    = 'https://durham.opendatasoft.com/api/records/1.0/search/?dataset=pending-building-permits' .
		          '&q=' .
		          '&start=' . $start . '&rows=' . $rows;
		$data   = json_decode( file_get_contents( $url ) );
		$nhits  = $data->nhits;
		$return = $data->records;
		$url    = 'https://durham.opendatasoft.com/api/records/1.0/search/?dataset=active-building-permits' .
		          '&q=' .
		          '&start=' . $start . '&rows=' . $rows;
		$data   = json_decode( file_get_contents( $url ) );
		$return = array_merge( $return, $data->records );
		$nhits  = $nhits + $data->nhits;

		return $return;
	}

	public function scrape( $id ) {
		$scrape = new ScrapeHtml(['uuid' => uniqid()]);
//		$scrape->uuid = uniqid();
		$url  = 'http://ldo.durhamnc.gov/durham/ldo_web/ldo_main.aspx';
		$page1 = $scrape->getPage( $url );
		$forms1 = $scrape->getForms( $page1 );
		$inputs1 = $scrape->getInputs($forms1[0][0]);
		foreach ($inputs1[1] as $key => $input){
			$attributes1[$key] = $scrape->getAttributes($input);
		}
		$params1 = [];
		foreach($attributes1 as $attribute){
			$params1[$attribute['name']] = $attribute['value'];
		}

		$params1['fn']   = 'LR_QUERY_DEVAPP2';
		$params1['pgid'] = 'qda2';

		$page2 = $scrape->getPage( $url, $params1, 'POST' );
		$forms2 = $scrape->getForms( $page2 );
		$inputs2 = $scrape->getInputs($forms2[0][0]);
		foreach ($inputs2[1] as $key => $input){
			$attributes = $scrape->getAttributes($input);
			$params2[$attributes['name']] = !empty($attributes['value']) ? $attributes['value'] : '';
		}
		$params2['PERMIT_NUMBER'] = $id;

		$page3 = $scrape->getPage( $url, $params2, 'POST' );
		if ( strpos( $page3, 'Invalid application number' ) ) {
			return false;
		}
		$tables = $scrape->getTables($page3);
		return $scrape->parseTable($tables[0][1]);
	}

}
