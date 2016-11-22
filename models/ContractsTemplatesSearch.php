<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contracts;

/**
 * ContractsSearch represents the model behind the search form about `app\models\Contracts`.
 */
class ContractsTemplatesSearch extends ContractsTemplates
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'create_at'], 'integer'],
            [['description'], 'string'],
            [['text','name'], 'safe'],
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
        $query = ContractsTemplates::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'name' => $this->name,
        ]);

        if (isset($this->create_at) && $this->create_at != '') {
            $dateArr = explode('до', $this->create_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'create_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'create_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
