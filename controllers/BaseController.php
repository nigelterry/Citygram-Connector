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
	public $defaultAction = 'api';

	private $routeOldUrls = [
		'crime-reports/api' => 'crime-message/api',
		'crime-reports' => 'crime-message/api',
		'crime_message/map' => 'crime-message/map',
		'crime_message/crimes' => 'crime-message/crimes',
	];
	
	private $modelName;
	private $doAction;

	function __construct( $id, $module ) {
		parent::__construct( $id, $module );
		$this->modelName       = '\\app\\models\\' . Inflector::id2camel( $this->id );
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
						'allow'   => !isset(Yii::$app->request->queryParams['action']) || in_array(Yii::$app->request->queryParams['action'], [ 'api', 'item', 'map', 'related'])
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
	public function actionRoute() {

		$queryParams = Yii::$app->request->queryParams;
		$requestedRoute = $queryParams['controller'] . (isset($queryParams['action']) ? '/' . $queryParams['action'] : '');
		if(array_key_exists($requestedRoute, $this->routeOldUrls)) {
			$requestedRoute = $this->routeOldUrls[$requestedRoute];
		}
		$routeParts = explode('/', $requestedRoute);
		if(sizeof($routeParts) == 1) {
			$routeParts[1] = $this->defaultAction;
		}
		$this->modelName = 'app\\models\\' . Inflector::id2camel($routeParts[0]);
		$this->doAction = 'action' . ucwords($routeParts[1]);
		Yii::info("Routing to $this->modelName with action $this->doAction", __METHOD__);
		return $this->{$this->doAction}($queryParams);
	}

	/**
	 * Lists all Message models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel  = new $this->modelName;
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
	public function actionView( $queryParams ) {
		return $this->render( '/base/view', [
			'model' => $this->findModel( $queryParams['id'] ),
		] );
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionItem( $queryParams ) {
		$this->layout = 'plain';

		return $this->render( '/base/item', [
			'model' => $this->findModel( $queryParams['id'] ),
		] );
	}

	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionDump( $queryParams ) {
		return $this->render( '/base/dump', [
			'model' => $this->findModel( $queryParams['id'] ),
		] );
	}

	/**
	 * Displays a single PoliceReportR model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionMap( $queryParams ) {
		$this->layout = 'plain';

		return $this->render( '/base/map', [
			'model' => $this->findModel( $queryParams['id'] ),
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
		$m = $this->modelName;
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
	public function actionApi( $queryParams ) {
		$page = isset($queryParams['page']) ? $queryParams['page'] : 0;
		$limit = isset($queryParams['limit']) ? $queryParams['limit'] : 1000;
		$days = isset($queryParams['days']) ? $queryParams['days'] : 20;
		$pretty = isset($queryParams['pretty']) ? $queryParams['pretty'] : false;

		file_get_contents( 'http://webstats.sapphirewebservices.com/piwik.php?idsite=18&rec=1&url=' .
		                   urlencode( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) .
		                   '&_cvar=' . urlencode( '{"1":["ip","' . $_SERVER['REMOTE_ADDR'] . '"]}' ) );
		$m                         = $this->modelName;
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
	public function actionRelated( $queryParams ) {
		$page = isset($queryParams['page']) ? $queryParams['page'] : 0;
		$limit = isset($queryParams['limit']) ? $queryParams['limit'] : 10000;
		$offset = isset($queryParams['offset']) ? $queryParams['offset'] : 0;
		$days = isset($queryParams['days']) ? $queryParams['days'] : 500;
		$pretty = isset($queryParams['pretty']) ? $queryParams['pretty'] : false;

		$m                         = $this->modelName;
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