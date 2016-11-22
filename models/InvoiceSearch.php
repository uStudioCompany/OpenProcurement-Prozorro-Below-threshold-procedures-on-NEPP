<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * InvoiceSearch represents the model behind the search form about `app\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{

    public $legalName;

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['balance_id'], 'integer'],
            [['code'], 'safe'],
            [['amount'], 'number'],
            [['legalName', 'destination', 'status', 'created_at', 'payed_at'], 'string'],
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
        $query = Invoice::find();

        // add conditions that should always apply here

        if (isset($this->payed_at) &&  $this->payed_at != '') {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder' => ['payed_at' => SORT_DESC]]
            ]);
        }
        else{
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
            ]);
        }

        $query->joinWith(['companies', 'payment']);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
        ]);

        if (!User::checkAdmin() || Yii::$app->controller->action->id == 'view-invoices'){
            $query->andFilterWhere([
                'balance_id' => Yii::$app->user->identity->company_id,
            ]);
        }

        if (isset($this->created_at) && $this->created_at != '') {
            $dateArr = explode('до', $this->created_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'invoice.created_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'invoice.created_at', strtotime($dateTo)]);
        }

        if (isset($this->payed_at) && $this->payed_at != '') {
            $dateArr = explode('до', $this->payed_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'payed_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'payed_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'invoice.status', $this->status])
            ->andFilterWhere(['like', 'legalName', $this->legalName]);
        if (isset($this->status) && $this->status == 'payed'){
            $query->andFilterWhere(['like', 'payment.destination', $this->destination]);
        }
        else{
            $query->andFilterWhere(['or', ['like', 'invoice.destination', $this->destination], ['like', 'payment.destination', $this->destination]]);
        }
        return $dataProvider;
    }
}