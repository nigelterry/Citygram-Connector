<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CrimeMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = $model->pageTitle;
$this->params['breadcrumbs'][] = ['label' => $title . 's', 'url' => [$model->urlName . '/' . 'index']];

?>
<div class="model-index">

    <h1><?= Html::encode($title . ' - ' . $model->id) ?></h1>

    <p>

        <?php
        if($model->hasMap) {
            echo Html::a('Map', [$model->urlName . '/' . 'map', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        }
        echo Html::a('View', [$model->urlName . '/' . 'view', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']) . '&nbsp';
        echo Html::a('Item', [$model->urlName . '/' . 'item', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        if(!empty($model->source_type)) {
            $s = $model->source_type;
            $m = new $s;
            $n = $m->urlName;
            echo '&nbsp' . Html::a('Source', [$n . '/dump', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        } else {
            $n = $model->messageUrl;
            echo '&nbsp' . Html::a('Message', [$n . '/dump', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
            echo '&nbsp' . Html::a('Map', [$n . '/map', 'id' => (string)$model->_id], ['class' => 'btn btn-primary']);
        }
        ?>
    </p>

    <?php
    $b = ArrayHelper::toArray($model);
    $a = expand($model, "");
    ArrayHelper::multisort($a, 'attribute');
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $a,
    ]);

    function expand(&$array, $prefix)
    {
        $result = [];
        foreach ($array as $k => $v) {
            if (is_object($v)) {
                if ('MongoId' == get_class($v)) {
                    $label = (($prefix != "") ? $prefix . '.' . $k : $k);
                    $result = array_merge($result, [['attribute' => $label, 'label' => $label]]);
                } elseif ('MongoDate' == get_class($v)) {
                    $label = (($prefix != "") ? $prefix . '.' . $k : $k) . '.sec';
                    $result = array_merge($result, [['attribute' => $label, 'label' => $label, 'format' => 'datetime']]);
                }
            } else {
                if
                (is_array($v)) {
                    $result = array_merge($result, expand($v, ($prefix != "") ? $prefix . '.' . $k : $k));
                } else {
                    $label = ($prefix != "") ? $prefix . '.' . $k : $k;
                    $result = array_merge($result, [['attribute' => $label, 'label' => $label]]);
                }
            }
        }
        return $result;
    }
    ?>

</div>
