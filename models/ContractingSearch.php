<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
/**
 * ContractingSearch represents the model behind the search form about `app\models\Contracting`.
 */
class ContractingSearch extends Contracting
{
    public $tender_cbd_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'company_id', 'created_at', 'update_at'], 'integer'],
            [['description', 'status', 'json', 'signed_data', 'response', 'token', 'contract_id', 'date_modified', 'tender_cbd_id'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['contract_cbd_id'], 'string', 'max' => 32],
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
            $query = Contracting::find()->with('user')->where(['contracting.company_id' => Yii::$app->user->identity->company_id])->orderBy('id DESC');
        } elseif ($module == 'seller') {
            $query = Contracting::find()->where(['in', 'contracting.id', (new Query())->select('tid')->from('bids')->where(['bids.company_id' => Yii::$app->user->identity->company_id])])->orderBy('id DESC');
        } else {
            $query = Contracting::find()->orderBy('id DESC');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $query->joinWith(['tenders']);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
        ]);

        if (isset($this->created_at) && $this->created_at != '') {
            $dateArr = explode('до', $this->created_at);
            $dateFrom = $dateArr[0] . '00:00:00';
            $dateTo = $dateArr[1] . '23:59:59';

            $query->andFilterWhere(['>=', 'created_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'created_at', strtotime($dateTo)]);
        }
        if (isset($this->update_at) && $this->update_at != '') {
            $dateArr = explode('до', $this->update_at);
            $dateFrom = $dateArr[0] . '00:00:00';
            $dateTo = $dateArr[1] . '23:59:59';

            $query->andFilterWhere(['>=', 'update_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'update_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'contract_cbd_id', $this->contract_cbd_id])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'contracting.status', $this->status])
            ->andFilterWhere(['like', 'json', $this->json])
            ->andFilterWhere(['like', 'signed_data', $this->signed_data])
            ->andFilterWhere(['like', 'response', $this->response])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'contract_id', $this->contract_id])
            ->andFilterWhere(['like', 'date_modified', $this->date_modified]);

        if ($this->tender_cbd_id) {
            $query->andFilterWhere(['like', 'tenders.tender_cbd_id', $this->tender_cbd_id]);
        }

        return $dataProvider;
    }
}
