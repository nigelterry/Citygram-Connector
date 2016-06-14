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
        echo Html::a('Dump', [$model->urlName . '/' . 'dump', 'id' => (string)$model->_id], ['class' => 'btn btn-primary btn-sm']) . '&nbsp';
        echo Html::a('Item', [$model->urlName . '/' . 'item', 'id' => (string)$model->_id], ['class' => 'btn btn-primary btn-sm']);
        if($model->hasMap) {  // Message
	        $s = $model->source_type;
	        $m = new $s;
	        $n = $m->urlName;
	        echo '&nbsp' . Html::a('Map', [$model->urlName . '/' . 'map', 'id' => $model->_id->__toString()], ['class' => 'btn btn-primary btn-sm']);
	        echo '&nbsp' . Html::a('Source', [$n . '/view', 'id' => $model->_id->__toString()], ['class' => 'btn btn-warning btn-sm']);
        } else { //Report
	        if(isset( $model->geometry['coordinates'][0])) {
		        $n = $model->messageUrl;
		        echo '&nbsp' . Html::a( 'Map', [ $n . '/map', 'id' => $model->_id->__toString() ],
				        [ 'class' => 'btn btn-warning btn-sm' ] );
		        echo '&nbsp' . Html::a( 'Message', [ $n . '/view', 'id' => $model->_id->__toString() ],
				        [ 'class' => 'btn btn-warning btn-sm' ] );
	        }
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $model->viewAttributes(),
    ]) ?>

</div>
