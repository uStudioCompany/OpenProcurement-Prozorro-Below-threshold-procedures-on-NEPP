<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Companies;

/**
 * TendersSearch represents the model behind the search form about `app\models\Tenders`.
 */
class PlansSearch extends Plans
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at', 'update_at'], 'integer'],
            [['title', 'description', 'status', 'json', 'response', 'token', 'plan_id'], 'safe'],
            ['plan_cbd_id', 'string', 'max' => 32],
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
        $module = \Yii::$app->controller->module->id;
        if ($module == 'buyer') {
            $query = Plans::find()->with('user')->where(['company_id' => Yii::$app->user->identity->company_id])->orderBy('id DESC');
        } else {
            $query = Plans::find()->orderBy('id DESC');
        }

        //$query = Plans::find()->with('user')->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        $this->load($params);

//        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
//            return $dataProvider;
//        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
        ]);

        if (isset($this->created_at) && $this->created_at != '') {
            $dateArr = explode('до', $this->created_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'created_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'created_at', strtotime($dateTo)]);
        }
        if (isset($this->update_at) && $this->update_at != '') {
            $dateArr = explode('до', $this->update_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'update_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'update_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'json', $this->json])
            ->andFilterWhere(['like', 'response', $this->response])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'plan_id', $this->plan_id])
            ->andFilterWhere(['like', 'plan_cbd_id', $this->plan_cbd_id]);

        if(in_array(Companies::getCompanyBusinesType(), ['seller',''])){ // черновики видит только покупатель
            $query->filterWhere(['!=', 'status', 'draft']);
        }

        return $dataProvider;
    }
}
