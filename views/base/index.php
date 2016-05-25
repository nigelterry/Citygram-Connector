<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CrimeMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$title = $searchModel->config->title . 's';
$this->params['breadcrumbs'][] = $title;
?>
<div class="model-index">

    <h1><?= Html::encode($title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    if(method_exists(get_class($searchModel), 'indexAttributes')){
	    echo GridView::widget( [
		    'dataProvider' => $dataProvider,
		    'filterModel'  => $searchModel,
		    'columns'      => $searchModel->indexAttributes()
	    ]);
    } else {
	    echo GridView::widget( [
		    'dataProvider' => $dataProvider,
		    'filterModel'  => $searchModel,
		    'columns'      => [
			    [ 'class' => 'yii\grid\SerialColumn' ],
			    [ 'attribute' => 'datetime.sec', 'format' => 'datetime', 'label' => 'Incident Date / Time' ],
			    'id',
//			'type',
			    'dataset',
			    'properties.title',
//            'short_url',
			    'properties.status',
			    [ 'attribute' => 'datetime.sec', 'format' => 'datetime', 'label' => 'Report Date / Time' ],
			    [ 'attribute' => 'created_at.sec', 'format' => 'datetime', 'label' => 'Added to DB at' ],
			    [ 'attribute' => 'updated_at.sec', 'format' => 'datetime', 'label' => 'Updated at' ],
			    [
				    'class'    => 'yii\grid\ActionColumn',
				    'template' => '{view} {dump} {map} {item} {update} {delete}',
				    'buttons'  => [
					    'map'  => function ( $url, $model, $key ) {
						    return ( isset( $model->geometry['coordinates'][0] ) &&
						             $model->config->hasMap ) ? Html::a( 'Map', $url ) : '';
					    },
					    'dump' => function ( $url, $model, $key ) {
						    return Html::a( 'Dump', $url );
					    },
					    /*					'item' => function ( $url, $model, $key ) {
												return Html::a( 'Item', [$model->source_type .'/item', 'id' => (string)$model->source_id] );
											},*/
				    ],
			    ],
		    ],
	    ] );
    }
    ?>

</div>
