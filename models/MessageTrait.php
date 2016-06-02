<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 6:26 PM
 */

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\mongodb\ActiveRecord;

Trait MessageTrait {
	
	use ModelTrait;

	public $requiredProperties = ['pageTitle', 'mapUrl'];
	public $mapUrl;

	public function construct() {
		$this->hasMap = true;
		$this->mapUrl = Yii::$app->params['mapUrl'];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		$rules = array_merge( $this->baseRules(),
			[
				[ [ '_id', 'datetime', 'short_url', 'sw_long', 'sw_lat', 'ne_long', 'ne_lat' ], 'safe' ],
			]);

		if ( method_exists( $this, 'modelRules' ) ) {
			$rules = array_merge( $rules, $this->modelRules() );
		}

		return $rules;
	}

	/**
	 * @inheritdoc
	 */
	public function attributes() {
		$atts = array_merge( $this->baseAttributes(),
			[
				'_id',
				'id',
				'datetime',
				'type',
				'properties',
				'dataset',
				'geometry',
				'center',
				'source_type',
				'long_url',
				'short_url',
				'bitly',
			] );

		if ( method_exists( $this, 'modelAttributes' ) ) {
			$atts = array_merge( $atts, $this->modelAttributes() );
		}

		return $atts;
	}

	/**
	 * @inheritdoc
	 */
	public function viewAttributes() {
		$atts = array_merge( $this->baseViewAttributes(),
			[
				'datetime.sec' => ':datetime',
			] );

		if ( method_exists( $this, 'modelViewAttributes' ) ) {
			$atts = array_merge( $atts, $this->modelViewAttributes() );
		}

		$flat = [];
		foreach($atts as $att => $format){
			$flat[] = $att . $format;
		}
		return $flat;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {

		$atts = array_merge( $this->baseAttributeLabels(),
			[
				'_id'                    => 'MongoID',
				'id'                     => 'Unique ID',
				'datetime.sec'               => 'Date / Time',
				'type'                   => 'Message Type',
				'dataset'                => 'Data Set',
				'properties'             => 'Message',
				'properties.dataset'     => 'Original Dataset',
				'properties.short_url'   => 'Short Url',
				'properties.title' => 'SMS Message',
				'properties.popupContent' => 'Popup Content',
				'long_url' => 'Full Url',
				'short_url' => 'Shortened Url',
			] );


		if ( method_exists( $this, 'modelAttributeLabels' ) ) {
			$atts = array_merge( $atts, $this->modelAttributeLabels() );
		}

		return $atts;
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

		return $atts;
	}


	public function buildMessage( ActiveRecord $report ) {
		$this->messageContent( $report );
		$this->created_at = new \MongoDate( time() );
		$this->save();
	}

	public function reBuildMessage( ActiveRecord $report ) {
		if ( isset( $this->_id ) && $this->_id != $report->_id ) {
			$this->delete();
			$this->isNewRecord = true;
		}
		$this->messageContent( $report );
		$this->updated_at = new \MongoDate( time() );
		$this->save();
	}

	public function messageContent( ActiveRecord $report ) {
		$this->_id                = $report->_id;
		$this->source_type        = get_class( $report );
		$this->id                 = $report->id;
		$this->type               = 'Feature';
		$this->dataset            = $report->dataset;
		$properties               = new \stdClass();
		$properties->title        = $report->title( $this->get_short_url() );
		$properties->popupContent = $report->popupContent();
		$this->properties         = $properties;
		$this->geometry           = (object) $report->geometry;
		$this->center             = (object) $report->center;
		$this->datetime           = $report->datetime;
	}

	public function get_short_url() {
		$u = $this->mapUrl . $this->urlName . '/map?id=' . (string) $this->_id;
		if ( empty( $this->short_url ) || $this->long_url != $u ) {
			$this->long_url = $u;
/*			if ( $this->useGoogle ) {
				ini_set( 'max_execution_time', 1000 );
				$wait   = 0;
				$gooUrl = 'https://www.googleapis.com/urlshortener/v1/url?key=' . Yii::$app->params['key'];
				while ( ( $goo = $this->post_request( $gooUrl, [ 'longUrl' => $this->long_url ] ) )
				        && file_put_contents( __DIR__ . '/../runtime/logs/google.log',
						( "\n\n" . date( 'l jS \of F Y h:i:s A' ) .
						  "\n" . print_r( $goo, true ) . "\nwait : $wait" ), FILE_APPEND )
				        && $goo->status_code != 200 ) {
					if ( $goo->status_code == 403 ) {
						$wait = 30;
					} else {
						$wait = $wait < 400 ? $wait * 4 : $wait;
					}
					if ( $wait != 0 ) {
						sleep( $wait );
					}
				}
				$this->short_url = $goo->id;
			} else {*/
				$response = $this->createUrl( $this->long_url, 'json', 'Map of Crime Report ' . $this->id );
				if ( ! isset( $response->status ) || $response->status != "success" ) {
					file_put_contents( __DIR__ . '/../runtime/logs/trg.pw.error.log',
						( "\n\n" . date( 'l jS \of F Y h:i:s A' ) .
						  "\n" . print_r( $response, true ) ), FILE_APPEND );
				}
				$this->short_url = $response->shorturl;
			}
/*		}*/

		return $this->short_url;
	}

	public function post_request( $url, $postParams ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $postParams ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ] );
		if ( ! ( $result = curl_exec( $ch ) ) || ( $result = json_decode( $result ) ) == null ) {
			$result              = new \stdClass();
			$result->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			return (object) [ 'status_code' => '418' ];
		}
		$result->status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		return $result;
	}

	public static function createUrl( $url, $format = 'json', $title = '' ) {
		$signature = Yii::$app->params['urlShortenerKey'];
		$api_url   = Yii::$app->params['urlShortenerUrl'];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $api_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );            // No header in the result
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // Return, do not echo result
		curl_setopt( $ch, CURLOPT_POST, 1 );              // This is a POST request
		curl_setopt( $ch, CURLOPT_POSTFIELDS, [     // Data to POST
			'url'       => $url,
			'format'    => $format,
			'title'     => $title,
			'action'    => 'shorturl',
			'signature' => $signature,
		] );

// Fetch and return content
		$data = curl_exec( $ch );
		curl_close( $ch );

// Do something with the result. Here, we just echo it.
		return json_decode( $data );
	}

}