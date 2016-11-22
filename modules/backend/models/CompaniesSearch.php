<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
//use app\modules\backend\models\Companies;

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
            [['id', 'LegalType', 'registrationCountryName', 'identifier', 'countryName', 'region', 'postalCode', 'status'], 'integer'],
            [['legalName', 'legalName_en', 'legalName_ru', 'moneygetId', 'fio', 'fio_en', 'fio_ru', 'userPosition', 'userPosition_en', 'userPosition_ru', 'userDirectionDoc', 'userDirectionDoc_en', 'userDirectionDoc_ru', 'locality', 'locality_en', 'locality_ru', 'streetAddress', 'streetAddress_en', 'streetAddress_ru', 'preferLang'], 'safe'],
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
        $query = Companies::find();//->joinWith('companyType');//->all(); //->column('name');

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
            'identifier' => $this->identifier,
            'countryName' => $this->countryName,
            'region' => $this->region,
            'postalCode' => $this->postalCode,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'legalName', $this->legalName])
            ->andFilterWhere(['like', 'legalName_en', $this->legalName_en])
            ->andFilterWhere(['like', 'legalName_ru', $this->legalName_ru])
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
            ->andFilterWhere(['like', 'address', $this->streetAddress])
            ->andFilterWhere(['like', 'address_en', $this->streetAddress_en])
            ->andFilterWhere(['like', 'address_ru', $this->streetAddress_ru])
            ->andFilterWhere(['like', 'preferLang', $this->preferLang]);

        return $dataProvider;
    }
}
