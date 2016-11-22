<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PaymentSearch represents the model behind the search form about `app\models\Payment`.
 */
class PaymentSearch extends Payment
{

    public $legalName;
    
    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['id', 'invoice_id'], 'integer'],
            [['payment_id', 'status', 'destination', 'codes', 'json'], 'safe'],
            [['amount'], 'number'],
            [['legalName', 'created_at'], 'string'],
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
        $query = Payment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]]
        ]);

        $query->joinWith(['invoice', 'companies']);

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
        
        if (isset($this->created_at) && $this->created_at != '') {
            $dateArr = explode('до', $this->created_at);
            $dateFrom = $dateArr[0]. '00:00:00';
            $dateTo = $dateArr[1]. '23:59:59';

            $query->andFilterWhere(['>=', 'payment.created_at', strtotime($dateFrom)])
                ->andFilterWhere(['<=', 'payment.created_at', strtotime($dateTo)]);
        }

        $query->andFilterWhere(['like', 'payment_id', $this->payment_id])
            ->andFilterWhere(['like', 'payment.status', $this->status])
            ->andFilterWhere(['like', 'destination', $this->destination])
            ->andFilterWhere(['like', 'codes', $this->codes])
            ->andFilterWhere(['like', 'json', $this->json])
            ->andFilterWhere(['like', 'legalName', $this->legalName]);

        return $dataProvider;
    }
}
