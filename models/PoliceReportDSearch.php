<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PoliceReportSearch represents the model behind the search form about `app\models\PoliceReport`.
 */
class PoliceReportDSearch extends PoliceReportD
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
