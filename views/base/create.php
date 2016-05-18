<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CrimeMessage */

$this->title = 'Create Crime Report';
$this->params['breadcrumbs'][] = ['label' => 'Crime Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crime-report-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
