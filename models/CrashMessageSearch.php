<?php

namespace app\models;

use Yii;

/**
 * CrimeMessageSearch represents the model behind the search form about `app\models\CrimeMessage`.
 */
class CrashMessageSearch extends CrashMessage
{

    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'type', 'properties', 'dataset', 'geometry', 'created_at', 'dataset', 'query_select', 'query_limit',
                'ignore', 'ne_lat', 'ne_long', 'sw_lat', 'sw_long', 'short_url'], 'safe'],
        ];
    }
}
