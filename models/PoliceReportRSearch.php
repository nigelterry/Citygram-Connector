<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PoliceReportR;

/**
 * PoliceReportSearch represents the model behind the search form about `app\models\PoliceReport`.
 */
class PoliceReportRSearch extends PoliceReportR
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'safe'],
        ];
    }

}
