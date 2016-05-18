<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CrimeMessageSearch represents the model behind the search form about `app\models\CrimeMessage`.
 */
Trait SearchTrait
{
    public $days;
    public $query_select = [];
    public $query_limit = 0;
    public $query_offset = 0;

    public $ne_lat;
    public $ne_long;
    public $sw_lat;
    public $sw_long;

    public $ignore;

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

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $m = $this->config->className; // Just keep PHPStorm happy
        $query = $m::find();
        $query->orderBy('datetime DESC');
        $query->select($this->query_select);
        if (isset($params[$this->config->className]) && isset($params[$this->config->className]['query_limit']) && $params[$this->config->className]['query_limit'] == null) {
            unset($params[$this->config->className]['query_limit']);
        }
        $query->limit($this->query_limit);
        $query->offset($this->query_offset);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($this->days) {
            $period_start = new \MongoDate(max(time() - $this->days * 24 * 60 * 60, 0));
//            $query->where(['datetime' => ['$gte' => $period_start]]);
        }

        if ($this->ignore) {
            $query->andWhere(['id' => ['$ne' => $this->ignore]]);
        }

        if ($this->ne_lat) {
            $query->andWhere(['geometry' =>
                [
                    '$geoWithin' => [
                        '$geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [
                                [
                                    [(double)$this->sw_long, (double)$this->sw_lat],
                                    [(double)$this->sw_long, (double)$this->ne_lat],
                                    [(double)$this->ne_long, (double)$this->ne_lat],
                                    [(double)$this->ne_long, (double)$this->sw_lat],
                                    [(double)$this->sw_long, (double)$this->sw_lat]
                                ],
                            ],
                        ]
                    ]
                ]
            ]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', '_id', $this->_id])
//            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'properties', $this->properties])
            ->andFilterWhere(['like', 'geometry', $this->geometry])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'created_at', $this->updated_at])
            ->andFilterWhere(['like', 'dataset', $this->dataset]);
//            $query->andFilterWhere(['like', 'short_url', $this->short_url]);
//            ->andFilterWhere(['$gte', 'datefrom', $this->datetime]);
//            ->andFilterWhere(['$ne', 'id', $params['ignore']]);
//            ->andFilterWhere(['$lte', 'dateto', $this->datetime]);

        return $dataProvider;
    }
}
