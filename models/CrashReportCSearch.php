<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PoliceReportSearch represents the model behind the search form about `app\models\PoliceReport`.
 */
class CrashReportCSearch extends CrashReportC
{

	use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [

        ]);
    }

}
