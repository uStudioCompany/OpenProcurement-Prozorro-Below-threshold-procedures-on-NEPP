<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bids;

/**
 * BidsSearch represents the model behind the search form about `app\models\Bids`.
 */
class BidsSearch extends Bids
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tid', 'company_id', 'create_at', 'update_at'], 'integer'],
            [['bid_id', 'token'], 'safe'],
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
        $query = Bids::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tid' => $this->tid,
            'company_id' => $this->company_id,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'bid_id', $this->bid_id])
            ->andFilterWhere(['like', 'token', $this->token]);

        return $dataProvider;
    }
}
