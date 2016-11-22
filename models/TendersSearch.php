<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * TendersSearch represents the model behind the search form about `app\models\Tenders`.
 */
class TendersSearch extends Tenders
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at', 'update_at'], 'integer'],
            [['description', 'status', 'json', 'response', 'token', 'tender_id','tender_method'], 'safe'],
            [['title', 'tender_cbd_id'], 'string', 'max'=>160],
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
        $query = Tenders::find()->select(['id','title','description', 'status','tender_cbd_id','tender_type','tender_method']);
        $seller = \app\models\Companies::checkCompanyIsSeller();
        $companyId = Yii::$app->user->identity->company_id;
        if ($module == 'buyer') {
            $query->with('user')->where(['company_id' => Yii::$app->user->identity->company_id])->orderBy('id DESC');
        } elseif ($module == 'seller'){
            $query->where(['in','id',(new Query())->select('tid')->from('bids')->where(['company_id'=>Yii::$app->user->identity->company_id])])->orderBy('id DESC');
        } else {
            if (!$seller) {
                $query->andWhere(['<>', 'status', 'draft']);
                $query->orWhere(['status' => 'draft', 'company_id' => $companyId]);
            }
            if ($seller || Yii::$app->user->isGuest) {
                $query->andWhere(['<>', 'status', 'draft']);
            }
            $query->orderBy('id DESC');
        }
        
        $query->andWhere(['in', 'tender_type', [1,2]]); // скрываем незаполненные тендера

        // тестовый режим
        $cookies = Yii::$app->request->cookies;
        if(($cookies['auction-mode']->value == 'test' || $_COOKIE['auction-mode'] == 'test') && !Yii::$app->user->isGuest){
            $query->andWhere(['=', 'test_mode', 1]);
        }else{
            $query->andWhere(['=', 'test_mode', 0]);
        }

        $action = Yii::$app->controller->action->id;
        switch ($action) {
            case 'archive':
                $query->andWhere(['in', 'status', Yii::$app->params['archive.status.tender']]);
                break;
            case 'actual':
                $query->andWhere(['in', 'status', Yii::$app->params['actual.status.tender']]);
                break;
            case 'current':
                $query->andWhere(['in', 'status', Yii::$app->params['current.status.tender']]);
                break;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

//        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
//            return $dataProvider;
//        }
//var_dump(strtotime($this->created_at)); die;
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
//            'created_at' => strtotime($this->created_at),
//            'update_at' => strtotime($this->update_at),
        ]);
//var_dump($this->created_at);die;
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
            ->andFilterWhere(['like', 'tender_cbd_id', $this->tender_cbd_id])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'tender_method', $this->tender_method])
            ->andFilterWhere(['like', 'json', $this->json])
            ->andFilterWhere(['like', 'response', $this->response])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'tender_id', $this->tender_id]);

        return $dataProvider;
    }

    /*
     * Check if some of limited tender has active status
     * if status != active -> doesn't show
     * */
    public static function isActiveAward($model)
    {
        if (isset($model['response']) && ($model->tender_method == 'limited_negotiation'
                || $model->tender_method == 'limited_reporting'
                || $model->tender_method == 'limited_negotiation.quick')) {
            $tenderJSON = json_decode($model['response'], true);
            if (isset($tenderJSON['data']['awards'])) {
                foreach ($tenderJSON['data']['awards'] as $award) {
                    if ($award['status'] != 'active')
                        return false;
                }
            } else {
                return false;
            }
        }
        return $model;
    }
}
