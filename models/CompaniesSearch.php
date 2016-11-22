<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Companies;

/**
 * CompaniesSearch represents the model behind the search form about `app\models\Companies`.
 */
class CompaniesSearch extends Companies
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'LegalType', 'registrationCountryName', 'countryName', 'region', 'status'], 'integer'],
            [['legalName', 'legalName_en', 'legalName_ru', 'identifier', 'parent_identifier', 'moneygetId', 'fio', 'fio_en', 'fio_ru', 'userPosition', 'userPosition_en', 'userPosition_ru', 'userDirectionDoc', 'userDirectionDoc_en', 'userDirectionDoc_ru', 'locality', 'locality_en', 'locality_ru', 'streetAddress', 'streetAddress_en', 'streetAddress_ru', 'postalCode', 'preferLang'], 'safe'],
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
        $query = Companies::find()->where(['id'=>Yii::$app->user->identity->company_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'LegalType' => $this->LegalType,
            'registrationCountryName' => $this->registrationCountryName,
            'countryName' => $this->countryName,
            'region' => $this->region,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'legalName', $this->legalName])
            ->andFilterWhere(['like', 'legalName_en', $this->legalName_en])
            ->andFilterWhere(['like', 'legalName_ru', $this->legalName_ru])
            ->andFilterWhere(['like', 'identifier', $this->identifier])
            ->andFilterWhere(['like', 'parent_identifier', $this->parent_identifier])
            ->andFilterWhere(['like', 'moneygetId', $this->moneygetId])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'fio_en', $this->fio_en])
            ->andFilterWhere(['like', 'fio_ru', $this->fio_ru])
            ->andFilterWhere(['like', 'userPosition', $this->userPosition])
            ->andFilterWhere(['like', 'userPosition_en', $this->userPosition_en])
            ->andFilterWhere(['like', 'userPosition_ru', $this->userPosition_ru])
            ->andFilterWhere(['like', 'userDirectionDoc', $this->userDirectionDoc])
            ->andFilterWhere(['like', 'userDirectionDoc_en', $this->userDirectionDoc_en])
            ->andFilterWhere(['like', 'userDirectionDoc_ru', $this->userDirectionDoc_ru])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andFilterWhere(['like', 'locality_en', $this->locality_en])
            ->andFilterWhere(['like', 'locality_ru', $this->locality_ru])
            ->andFilterWhere(['like', 'streetAddress', $this->streetAddress])
            ->andFilterWhere(['like', 'streetAddress_en', $this->streetAddress_en])
            ->andFilterWhere(['like', 'streetAddress_ru', $this->streetAddress_ru])
            ->andFilterWhere(['like', 'postalCode', $this->postalCode])
            ->andFilterWhere(['like', 'preferLang', $this->preferLang]);

        return $dataProvider;
    }
}
