<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 4/12/16
 * Time: 10:10 PM
 */

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\filters\AccessControl;

class BaseController extends Controller {
	protected $config;
	public $defaultAction = 'api';

	function __construct( $id, $module ) {
		parent::__construct( $id, $module );
		$this->config                  = new \stdClass();
		$this->config->modelName       = '\\app\\models\\' . Inflector::id2camel( $this->id );
		$this->config->searchModelName = $this->config->modelName . 'Search';
	}

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'roles' => [ '@' ],
					],
					[
						'allow'   => true,
						'actions' => [ 'api', 'item', 'map', 'related'],
					],
				],
			],
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'logout' => [ 'post' ],
				],
			]
		];
	}

	/**
	 * Lists all Message models.
	 * @return mixed
	 */
	public function actionIndex( $days = null ) {
		$m            = $this->config->searchModelName;
		$searchModel  = new $m;
		$dataProvider = $searchModel->search( Yii::$app->request->queryParams );

		return $this->render( '/base/index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		] );
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionView( $id ) {
		return $this->render( '/base/view', [
			'model' => $this->findModel( $id ),
		] );
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionItem( $id ) {
		$this->layout = 'plain';

		return $this->render( '/base/item', [
			'model' => $this->findModel( $id ),
		] );
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionDump( $id ) {
		return $this->render( '/base/dump', [
			'model' => $this->findModel( $id ),
		] );
	}

	/**
	 * Displays a single PoliceReportR model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionMap( $id ) {
		$this->layout = 'plain';

		return $this->render( '/base/map', [
			'model' => $this->findModel( $id ),
		] );
	}

	/**
	 * Finds the Message model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $_id
	 *
	 * @return Message the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel( $id ) {
		$m = $this->config->modelName;
		if ( ( $model = $m::findOne( $id ) ) !== null ) {
			return $model;
		} else {
			throw new NotFoundHttpException( 'The requested page does not exist.' );
		}
	}

	/**
	 * Lists all PoliceReport models.
	 * @return mixed
	 */
	public function actionApi( $page = 0, $limit = 10000, $days = 20, $pretty = false ) {
		file_get_contents( 'http://webstats.sapphirewebservices.com/piwik.php?idsite=18&rec=1&url=' .
		                   urlencode( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) .
		                   '&_cvar=' . urlencode( '{"1":["ip","' . $_SERVER['REMOTE_ADDR'] . '"]}' ) );
		$m                         = $this->config->searchModelName;
		$searchModel               = new $m;
		$searchModel->days         = $days;
		$searchModel->query_limit  = $limit;
		$searchModel->query_select = [ 'id', 'geometry', 'properties.title' ];
		$dataProvider              = $searchModel->search( Yii::$app->request->queryParams );
		$dataProvider->pagination  = false;
		$this->layout              = false;

		return $this->render( '/base/api', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
			'pretty'       => $pretty,
		] );
	}

	/**
	 * Lists all PoliceReport models.
	 * @return mixed
	 */
	public function actionRelated( $page = 0, $limit = 10000, $offset = 0, $days = 120, $pretty = false ) {
		/*        file_get_contents('http://webstats.sapphirewebservices.com/piwik.php?idsite=18&rec=1&url=' .
					urlencode('http://' . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI']) .
					'&_cvar=' . urlencode('{"1":["ip","' . $_SERVER['REMOTE_ADDR'] . '"]}'));*/
		$m                         = $this->config->searchModelName;
		$searchModel               = new $m;
		$searchModel->query_select = [ 'type', 'geometry', 'properties.popupContent' ];
		$searchModel->query_limit  = Yii::$app->params['devicedetect']['isDesktop'] ? 60 :
			( Yii::$app->params['devicedetect']['isTablet'] ? 30 : 15 );
		$searchModel->query_offset = $offset;
		$searchModel->days         = $days;
		$dataProvider              = $searchModel->search( Yii::$app->request->bodyParams );
		$dataProvider->pagination  = false;
		$this->layout              = false;

		return $this->render( '/base/api', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
			'pretty'       => $pretty,
		] );
	}
}