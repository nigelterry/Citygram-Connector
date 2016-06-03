<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * CrimeMessageSearch represents the model behind the search form about `app\models\CrimeMessage`.
 */
Trait ModelTrait {
	protected $useGoogle = false;

	public $days;
	public $query_select = [ ];
	public $query_limit = 0;
	public $query_offset = 0;

	public $ne_lat;
	public $ne_long;
	public $sw_lat;
	public $sw_long;

	public $ignore;

	public $urlName;
	public $modelName;
	public $hasMap;
	public $className;
	public $shortName;
	public $messageUrl;
	public $pageTitle;

	public function __construct() {
		parent::__construct();
		$r               = new \ReflectionClass( $this );
		$this->className = $r->getName();
		$this->shortName = $r->getShortName();
		$this->modelName = StringHelper::basename( get_class( $this ) );
		$this->urlName   = Inflector::camel2id( StringHelper::basename( get_class( $this ) ) );
		$this->hasMap    = false;
		$this->construct();
		if ( method_exists( $this, 'modelConstruct' ) ) {
			$this->modelConstruct();
		}
		if ( isset( $this->messageType ) ) {
			$this->messageUrl = Inflector::camel2id( $this->messageType );
		}
		$this->checkExists( $this->requiredProperties );
	}

	/**
	 * @inheritdoc
	 */
	public static function collectionName() {
		return [ 'citygram', Inflector::camel2id( StringHelper::basename( get_called_class() ), '_' ) ];
	}

	/**
	 * @inheritdoc
	 */
	public function baseRules() {
		return [
			[
				[
					'_id',
					'type',
					'properties',
					'dataset',
					'geometry',
					'created_at',
					'dataset',
					'query_select',
					'query_limit',
					'ignore',
					'ne_lat',
					'ne_long',
					'sw_lat',
					'sw_long'
				],
				'safe'
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	/*     public function scenarios()
		{
			// bypass scenarios() implementation in the parent class
			return Model::scenarios();
		}*/

	/**
	 * @inheritdoc
	 */
	public function baseAttributes() {
		return [
			'created_at',
			'updated_at'
		];
	}

	/**
	 * @inheritdoc
	 */
	public function baseAttributeLabels() {
		return [
			'_id'                    => 'MongoID',
			'created_at'             => 'Created At',
			'updated_at'             => 'Updated At',
			'geometry.coordinates.0' => 'Longitude',
			'geometry.coordinates.1' => 'Latitude',
			'datetime.sec'           => 'Date / Time',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function baseViewAttributes() {
		return [

		];
	}

	/**
	 * @inheritdoc
	 */
	public function baseIndexAttributes() {
		return [

		];
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search( $params ) {
		$m     = $this->className; // Just keep PHPStorm happy
		$query = $m::find();
		$query->orderBy( 'datetime DESC' );
		$query->select( $this->query_select );
		if ( isset( $params[ $this->className ] ) && isset( $params[ $this->className ]['query_limit'] ) && $params[ $this->className ]['query_limit'] == null ) {
			unset( $params[ $this->className ]['query_limit'] );
		}
		$query->limit( $this->query_limit );
		$query->offset( $this->query_offset );
		$dataProvider = new ActiveDataProvider( [
			'query' => $query,
		] );

		$this->load( $params );

		if ( $this->days ) {
			$period_start = new \MongoDate( max( time() - $this->days * 24 * 60 * 60, 0 ) );
			$query->where( [ 'datetime' => [ '$gte' => $period_start ] ] );
		}

		if ( $this->ignore ) {
			$query->andWhere( [ 'id' => [ '$ne' => $this->ignore ] ] );
		}

		if ( $this->ne_lat ) {
			$query->andWhere( [
				'geometry' =>
					[
						'$geoWithin' => [
							'$geometry' => [
								'type'        => 'Polygon',
								'coordinates' => [
									[
										[ (double) $this->sw_long, (double) $this->sw_lat ],
										[ (double) $this->sw_long, (double) $this->ne_lat ],
										[ (double) $this->ne_long, (double) $this->ne_lat ],
										[ (double) $this->ne_long, (double) $this->sw_lat ],
										[ (double) $this->sw_long, (double) $this->sw_lat ]
									],
								],
							]
						]
					]
			] );
		}

		if ( ! $this->validate() ) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere( [ 'like', '_id', $this->_id ] )
//            ->andFilterWhere(['like', 'type', $this->type])
              ->andFilterWhere( [ 'like', 'properties', $this->properties ] )
		      ->andFilterWhere( [ 'like', 'geometry', $this->geometry ] )
		      ->andFilterWhere( [ 'like', 'created_at', $this->created_at ] )
		      ->andFilterWhere( [ 'like', 'created_at', $this->updated_at ] )
		      ->andFilterWhere( [ 'like', 'dataset', $this->dataset ] );
//            $query->andFilterWhere(['like', 'short_url', $this->short_url]);
//            ->andFilterWhere(['$gte', 'datefrom', $this->datetime]);
//            ->andFilterWhere(['$ne', 'id', $params['ignore']]);
//            ->andFilterWhere(['$lte', 'dateto', $this->datetime]);

		return $dataProvider;
	}

	public function actionColumn( $model ) {
		return [
			'class'      => 'yii\grid\ActionColumn',
			'template'   => '{view} {dump} {map} {item}',
			'urlCreator' => function ( $action, $model, $key, $index ) {
				return Url::to( [ $model->urlName . '/' . $action, 'id' => $key->{'$id'} ] );
			},
			'buttons'    => [
				'map'  => function ( $url, $model, $key ) {
					return ( isset( $model->geometry['coordinates'][0] ) &&
					         $model->hasMap ) ? Html::a( 'Map', $url ) : '';
				},
				'dump' => function ( $url, $model, $key ) {
					return Html::a( 'Dump', $url );
				},
				'item' => function ( $url, $model, $key ) {
					return Html::a( 'Item', $url );
				},
				'view' => function ( $url, $model, $key ) {
					return Html::a( 'View', $url );
				},
			],
		];
	}

	protected function checkExists( $properties ) {
		foreach ( $properties as $property ) {
			$prop = $this->{$property};
			if ( ! isset( $prop ) || $prop === null || $prop === '' ) {
				throw new InvalidConfigException( '$' . $property . ' must be configured in ' . $this->className );
			}
		}
	}

	public function flatten( $atts ) {
		$flat = [ ];
		foreach ( $atts as $att => $format ) {
			if ( is_array( $format ) ) {
				$flat[] = $format;
			} else {
				$flat[] = $att . $format;
			}
		}

		return $flat;
	}

}
