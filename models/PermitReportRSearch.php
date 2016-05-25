<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PermitReportSearch represents the model behind the search form about `app\models\PermitReport`.
 */
class PermitReportRSearch extends PermitReportR
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
