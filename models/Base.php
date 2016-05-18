<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 4/12/16
 * Time: 4:19 PM
 */

namespace app\models;

use Yii;
use \yii\mongodb\ActiveRecord;

abstract class Base extends ActiveRecord
{
    protected $map_url;

    public $config;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @inheritdoc
     */
    public function viewAttributes()
    {
        return [

        ];
    }



    public function __construct()
    {
        parent::__construct();
        $this->map_url = Yii::$app->params['map_url'];
        $r = new \ReflectionClass($this);
        $this->config = (object)[
            'collectionName' => $this->collectionName()[1],
            'className' => $r->getName(),
            'shortName' => $r->getShortName(),
            'hasMap' => false,
        ];
    }
    
    

}