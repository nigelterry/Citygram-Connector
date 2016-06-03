<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

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
		if ( $table = $this->scrape( $record->fields->permit_id ) ) {
			if ( $table === false ) {
				return false;
			}
			$record->fields->status = $table[3][2];

			return new \MongoDate( strtotime( $table[3][3] ) );
		} else {
			$record->fields->failedFetch = true;

			return new \MongoDate( time() );
		}// Use today's date as they don't provide one
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
		$url  = 'http://ldo.durhamnc.gov/durham/ldo_web/ldo_main.aspx';
		$page = $this->httpGet( $url, [ ] );

		$params = $this->analyzeForm( $page );
		if ( $params === false ) {
			return false;
		}

		$params['fn']   = 'LR_QUERY_DEVAPP2';
		$params['pgid'] = 'qda2';

		$page = $this->httpPost( $url, $params );

		$params2 = $this->analyzeForm( $page, 2 );
		if ( $params === false ) {
			return false;
		}

		$params2['PERMIT_NUMBER'] = $id;
		$params2['fn']            = 'LR_QUERY_DEVAPP_RS2';
		$params2['SUBMIT']        = 'Submit';
		$params                   = array_merge( $params, $params2 );
		$page                     = $this->httpPost( $url, $params );
		if ( strpos( $page, 'Invalid application number' ) ) {
			return false;
		}
		$table = $this->parseTable( $page, 1 );

		return $table;
	}


	private function httpPost( $url, $params ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 );
		$output = curl_exec( $ch );
		curl_close( $ch );

		return $output;
	}

	private function httpGet( $url, $params ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 );
		$output = curl_exec( $ch );
		curl_close( $ch );

		return $output;
	}

	private function analyzeForm( $page, $req = 1000 ) {
		$lines = explode( "\n", $page );
		if ( 1 >= count( $lines ) ) {
			return false;
		}
		$line = 0;
		if ( count( $lines ) <= $line ) {
			return false;
		}
		while ( ! strpos( $lines[ $line ], '<form' ) ) {
			$line ++;
		}
		$params = [ ];
		$line ++;
		$count = 0;
		while ( ! strpos( $lines[ $line ], '</form' ) && $count < $req ) {
			if ( '' !== trim( $lines[ $line ] ) ) {
				$parts              = explode( ' ', trim( $lines[ $line ] ) );
				$keys               = explode( '"', $parts[2] );
				$vals               = explode( '"', $parts[3] );
				$params[ $keys[1] ] = $vals[1];
				$count ++;
			}
			$line ++;
		}

		return $params;
	}

	function parseTable( $html, $which ) {
		// Find the table
		preg_match_all( "/<table.*?>.*?<\/[\s]*table>/s", $html, $table_html );

		if ( 2 > count( $table_html[0] ) ) {
			return false;;
		}

		preg_match_all( "/<tr.*?>(.*?)<\/[\s]*tr>/s", $table_html[0][ $which ], $matches );

		$table = [ ];

		foreach ( $matches[1] as $row_html ) {
			preg_match_all( "/<td.*?>(.*?)<\/[\s]*td>/", str_replace( "\r\n", "", $row_html ), $td_matches );
			$row = array();
			for ( $i = 0; $i < count( $td_matches[1] ); $i ++ ) {
				$td        = strip_tags( html_entity_decode( $td_matches[1][ $i ] ) );
				$row[ $i ] = trim( $td );
			}

			if ( count( $row ) > 0 ) {
				$table[] = $row;
			}
		}

		return $table;
	}

}
