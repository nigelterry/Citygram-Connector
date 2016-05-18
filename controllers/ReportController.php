<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 4/11/16
 * Time: 12:26 AM
 */

namespace app\controllers;

abstract class ReportController extends BaseController
{
    public function actionRebuild(){
        $m = $this->config->modelName;
        $model = new $m;
        return $this->render('/base/load', [
            'model' => $model,
            'data' => $model->rebuild()
            ]);
    }

    public function actionDownload($days = 30){
        $m = $this->config->modelName;
        $model = new $m;
        return $this->render('/base/load', [
            'model' => $model,
            'data' => $model->download($days)
        ]);
    }


}