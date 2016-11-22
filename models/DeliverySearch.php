<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Delivery;
use yii\helpers\VarDumper;

/**
 * DeliverySearch represents the model behind the search form about `app\models\Delivery`.
 */
class DeliverySearch extends Delivery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',  'company_id'], 'integer'],
            [['locality', 'countryName','region', 'locality_en', 'locality_ru', 'postalCode'], 'safe'],
            [['lat', 'lng'], 'number'],
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
        $query = Delivery::find()->with('country', 'dregion')->where(['company_id'=>Yii::$app->user->identity->company_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
//        VarDumper::dump($params, 10, true);
//        VarDumper::dump($this, 10, true);
//        die;
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'countryName' => $this->countryName,
            'region' => $this->region,
//            'lat' => $this->lat,
//            'lng' => $this->lng,
//            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'locality', $this->locality])
            ->andFilterWhere(['like', 'locality_en', $this->locality_en])
            ->andFilterWhere(['like', 'locality_ru', $this->locality_ru])
            ->andFilterWhere(['like', 'postalCode', $this->postalCode]);

        return $dataProvider;
    }

    public function getCountry()
    {
        return parent::getCountry();
    }

    public function getDregion()
    {
        return parent::getDregion();
    }
}
