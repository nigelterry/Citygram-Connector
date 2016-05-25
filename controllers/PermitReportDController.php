<?php

namespace app\controllers;

use app\models\PermitReportD;
use Yii;


/**
 * PermitReportController implements the CRUD actions for PermitReport model.
 */
class PermitReportDController extends PermitReportController
{
	/**
	 * Displays a single Message model.
	 *
	 * @param integer $_id
	 *
	 * @return mixed
	 */
	public function actionScrape( $id ) {
		$this->layout = 'plain';
		$model = new PermitReportD();
		return $this->render( '/base/scrape', [
			'data' => $model->scrape( $id ),
		] );
	}

}
