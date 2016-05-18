<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Base */

$title = $model->config->title;
$this->params['breadcrumbs'][] = ['label' => $title . 's', 'url' => ['index']];

?>
<div class="message-view">

    <h1><?= Html::encode($title . ' - ' .  $model->id) ?></h1>

    <p>
        <?php
        if($model->config->hasMap) {
            echo Html::a('Map', ['map', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        }
        echo Html::a('Dump', ['dump', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        echo Html::a('Item', ['item', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        if(!empty($model->source_type)) {
            $s = $model->source_type;
            $m = new $s;
            $n = $m->config->urlName;
            echo '&nbsp' . Html::a('Source', [$n . '/view', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        } else {
            $n = $model->config->messageUrl;
            echo '&nbsp' . Html::a('Message', [$n . '/view', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $model->viewAttributes(),
    ]) ?>

</div>
