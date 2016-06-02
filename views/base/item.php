<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PermitReport */

?>
<div class="message-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $model->viewAttributes(),
    ]) ?>
</div>
