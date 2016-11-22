<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CashFlow;

/**
 * CashFlowSearch represents the model behind the search form about `app\models\CashFlow`.
 */
class CashFlowSearch extends CashFlow
{
    public $tender_name;
    public $tender_json;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'balance_id', 'tender_id', 'payment_id', 'invoice_id', 'cash_flow_reason_id'], 'integer'],
            [['way', 'lot_id', 'tender_name', 'tender_json'], 'safe'],
            [['amount'], 'number'],
            [['payed_at'], 'string'],
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
        $query = CashFlow::find();

        $query->joinWith(['tender']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['payed_at'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['tender_name'] = [
            'asc' => ['tenders.title' => SORT_ASC],
            'desc' => ['tenders.title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tender_json'] = [
            'asc' => ['tenders.json' => SORT_ASC],
            'desc' => ['tenders.json' => SORT_DESC],
        ];

        if ($this->load($params)  && !$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'balance_id' => Yii::$app->user->identity->company_id,
            'tender_id' => $this->tender_id,
            'payment_id' => $this->payment_id,
            'invoice_id' => $this->invoice_id,
            'cash_flow_reason_id' => $this->cash_flow_reason_id,
            'created_at' => $this->created_at,
        ]);

        if (isset($this->payed_at) && $this->payed_at != '') {
            $dateArr = explode('до', $this->payed_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'payed_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'payed_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'way', $this->way])
            ->andFilterWhere(['like', 'lot_id', $this->lot_id])
            ->andFilterWhere(['like', 'tenders.title', $this->tender_name])
            ->andFilterWhere(['like', 'tenders.json', $this->tender_json]);

        return $dataProvider;
    }
}