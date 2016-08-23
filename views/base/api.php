<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PoliceReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//	file_put_contents( __DIR__ . '/../../runtime/logs/api_pull.log',
//		date('l jS \of F Y h:i:s A') . ': ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['REMOTE_ADDR'] ."\n", FILE_APPEND);
    $models = $dataProvider->getModels();
    $models = array_values($models);
    $headers = Yii::$app->response->headers;
    $headers->set('Content-Type', 'application/json');
    Yii::$app->response->format = Response::FORMAT_RAW;
        $first = true;
        $count = 0;
        echo '{';
        echo '"type":"FeatureCollection",';
        echo '"features":[';
        foreach($models as $model) {
            $model->type = 'Feature';
            $count++;
            unset($model->_id);
            if($first){
                $first = false;
            } else {
                echo ',';
            }
            $properties = $model->properties;
            if(isset($model->datetime)){
                $properties['datetime'] = $model->datetime->toDateTime()->format('c');
                unset($model->datetime);
            }

            $properties['version'] = 2;
            $model->properties = $properties;
            echo json_encode(ArrayHelper::toArray($model), $pretty ? JSON_PRETTY_PRINT : 0);
        }
        echo '], "count" : ' . $count . '}';
