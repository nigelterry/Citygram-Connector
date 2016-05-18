<?php

/* @var $this yii\web\View */
/* @var $model app\models\Report */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = $model->config->title . 's - Loaded';
$this->params['breadcrumbs'][] = $title;
if(get_class(Yii::$app) == 'yii\web\Application') {
    ?>
    <div class="load-report">
        <h1><?= 'Total :' . $data['total'] . ', Updates :' . $data['updates'] . ', New :' . $data['new'] . ', Has Location :' . $data['hasgeo'] ?></h1>
    </div>
    <?php
} else {
    echo 'Total :' . $data['total'] . ', Updates :' . $data['updates'] . ', New :' . $data['new'] . ', Has Location :' . $data['hasgeo'] . "\n";
}