<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Persons;
use yii\helpers\VarDumper;

/**
 * PersonsSearch represents the model behind the search form about `app\models\Persons`.
 */
class PersonsSearch extends Persons
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['userName', 'userName_en', 'userName_ru', 'userSurname', 'userSurname_en', 'userSurname_ru', 'userPatronymic', 'userPatronymic_en', 'userPatronymic_ru', 'email', 'telephone', 'faxNumber', 'mobile', 'url'], 'safe'],
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

//        print_r(Yii::$app->user->identity->company_id);die;
        $query = Persons::find()->where(['company_id'=>Yii::$app->user->identity->company_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'userName_en', $this->userName_en])
            ->andFilterWhere(['like', 'userName_ru', $this->userName_ru])
            ->andFilterWhere(['like', 'userSurname', $this->userSurname])
            ->andFilterWhere(['like', 'userSurname_en', $this->userSurname_en])
            ->andFilterWhere(['like', 'userSurname_ru', $this->userSurname_ru])
            ->andFilterWhere(['like', 'userPatronymic', $this->userPatronymic])
            ->andFilterWhere(['like', 'userPatronymic_en', $this->userPatronymic_en])
            ->andFilterWhere(['like', 'userPatronymic_ru', $this->userPatronymic_ru])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'faxNumber', $this->faxNumber])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
