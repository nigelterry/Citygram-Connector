<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Base */

$title = $model->pageTitle;
$this->params['breadcrumbs'][] = ['label' => $title . 's', 'url' => [$model->urlName . '/' . 'index']];

?>
<div class="message-view">

    <h1><?= Html::encode($title . ' - ' .  $model->id) ?></h1>

    <p>
        <?php
        if($model->hasMap) {
            echo Html::a('Map', [$model->urlName . '/' . 'map', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        }
        echo Html::a('Dump', [$model->urlName . '/' . 'dump', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        echo Html::a('Item', [$model->urlName . '/' . 'item', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        if(!empty($model->source_type)) {
            $s = $model->source_type;
            $m = new $s;
            $n = $m->urlName;
            echo '&nbsp' . Html::a('Source', [$n . '/view', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        } else {
            $n = $model->messageUrl;
            echo '&nbsp' . Html::a('Message', [$n . '/view', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
            echo '&nbsp' . Html::a('Map', [$n . '/map', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $model->viewAttributes(),
    ]) ?>

</div>
