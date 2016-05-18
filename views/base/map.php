<style>
    body {
        padding: 0;
        margin: 0;
    }

    html, body, .wrap, #map {
        height: 100%;
        padding: 0;
        margin: 0;
    }
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\DetailView;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\layers\Marker;
use \dosamigos\leaflet\layers\Polygon;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\widgets\Map;
use dosamigos\leaflet\PluginManager;

/* @var $this yii\web\View */
/* @var $model app\models\CrimeMessage */

// first lets setup the center of our map
$center = new LatLng([
    'lat' => isset($model->center['coordinates'][1]) ? $model->center['coordinates'][1] : $model->geometry['coordinates'][1],
    'lng' => isset($model->center['coordinates'][0]) ? $model->center['coordinates'][0] : $model->geometry['coordinates'][0],
]);

// now lets create a marker that we are going to place on our map
$sourceID = (string)$model->_id;
$popContent = '<div class="primary-popup">' . $model->properties['popupContent'] . '</div>';
//$popUp = new Popup(['content' => $popContent, 'latLng' => $center, 'clientOptions' => ['className' => 'primary-popup', 'autoPan' => false]]);
//    Html::a('Full Details', [Inflector::camel2id(explode('\\', $model->source_type)[2]) . '/item', 'id' => (string)$model->_id]);
$marker = new Marker(['latLng' => $center,
    'name' => 'marker',
    'popupContent' => $popContent,
    'clientOptions' => ['zIndexOffset' => 1000]
]);

switch (Yii::$app->params['mapProvider']) {
    case 'HERE';
// The Tile Layer (very important) HERE
        $tileLayer = new TileLayer([
            'name' => 'tiles',
            'urlTemplate' => 'http://{s}.{base}.maps.cit.api.here.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?app_id={app_id}&app_code={app_code}&lg={language}',
            'clientOptions' => [
                'subdomains' => ['1', '2', '3', '4'],
                'attribution' => 'Map &copy; 2016 <a href="http://developer.here.com">HERE</a>',
                'app_id' => '5q1Pm7iltj9UCBGN2mED',
                'app_code' => 'I7kzs-8k7dxFgZiRPxVVhA',
                'base' => 'base',
                'type' => 'maptile',
                'mapID' => 'newest',
                'scheme' => 'pedestrian.day',
                'size' => '256',
                'format' => 'png8',
                'language' => 'eng',
            ],
            'clientEvents' => ['loading' => 'addEvents'],
        ]);
        break;

    case 'Streetmap':
    default:
// The Tile Layer (very important) Open Street Map
        $tileLayer = new TileLayer([
            'name' => 'tiles',
            'urlTemplate' => 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            'clientOptions' => [
                'attribution' => '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
            ],
            'clientEvents' => ['loading' => 'addEvents'],
        ]);
}

// now our component and we are going to configure it
$leaflet = new LeafLet([
    'center' => $center, // set the center
    'zoom' => 16,
    'clientOptions' => ['zoomControl' => Yii::$app->params['devicedetect']['isDesktop']],
    'name' => 'map'
]);
$leaflet->addLayer($marker)// add the marker (addLayer is used to add different layers to our map)
->addLayer($tileLayer);

$leaflet->appendJs('var id = "' . $model->id . '";');
$function = new \ReflectionClass($model);
$leaflet->appendJs('var searchModel = "' . $function->getShortName() . 'Search";');

$leaflet->appendJs(isset(Yii::$app->request->queryParams['query_limit']) ?
    'var query_limit = ' . Yii::$app->request->queryParams['query_limit'] :
    'var query_limit = ""' . ';');
$leaflet->appendJs(file_get_contents('js/maptools.js'));

if (isset($model->center['coordinates'][0])) {
    $polygon = new Polygon();
    $latlng = $model->geometry['coordinates'][0];
    $a = [];
    foreach ($latlng as $l) {
        $a[] = new LatLng(['lng' => $l[0], 'lat' => $l[1]]);
    }
    $polygon->setLatLngs($a);
    $leaflet->addLayer($polygon);
}


// finally render the widget
echo Map::widget(['leafLet' => $leaflet, 'height' => '100%', 'options' => ['id' => 'map']]);
