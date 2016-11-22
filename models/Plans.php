<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use app\components\ApiHelper;
use app\models\planModels\Plan;

/**
 * This is the model class for table "plans".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $company_id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property integer $created_at
 * @property integer $update_at
 * @property string $json
 * @property string $signed_data
 * @property string $response
 * @property string $token
 * @property string $plan_id
 * @property string $plan_cbd_id
 * @property string $date_modified
 *
 * @property User $user
 */
class Plans extends \yii\db\ActiveRecord
{

    public $data = '';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plans';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'update_at'], 'integer'],
            [['json', 'response', 'signed_data'], 'string'],
            [['token', 'plan_id'], 'safe'],
            [['title', 'status', 'date_modified'], 'string', 'max' => 255],
            ['plan_cbd_id', 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'title' => Yii::t('app', 'Title'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
            'json' => Yii::t('app', 'Json'),
            'response' => Yii::t('app', 'Response'),
            'token' => Yii::t('app', 'Token'),
            'plan_id' => Yii::t('app', 'plan_id'),
            'plan_cbd_id' => Yii::t('app', 'PlanID'),
        ];
    }

    public static function getPlans($id)
    {
        //$plans = Plans::find('response')->where(['id' => $id])->limit(1)->asArray()->one();
        $plans = self::getModel($id);
        $plans->json = json_decode($plans->json, 1);
        if ($plans->response) {
            $json = json_decode($plans->response, 1);
            $json = ['Plan' => $json['data']];
            //$json = [ 'Plan' => json_decode($plans->response, 1)['data'] ];
            ApiHelper::FormatDate($json['Plan']);
            $plans->json = $json;
        }
        return $plans;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if (is_a(Yii::$app, 'yii\web\Application')) {// если не консоль
                $this->user_id    = Yii::$app->user->id;
                $this->company_id = Yii::$app->user->identity->company_id;
            }
            $this->created_at = time();
        } else {
            $this->update_at = time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param $id
     * @return Plans
     * @throws ErrorException
     */
    public static function getModel($id)
    {
        $plans = Plans::findOne(['id' => $id]);
        if (!$plans) {
            throw new ErrorException('Нет такого плана.');
        }
        return $plans;
    }


    /**
     * @param $plans Plans
     * @param $plan Plan
     * @param $post array
     * @param bool|false $validate
     * @throws ErrorException
     * @return bool
     */
    public static function submitToDB($plans, $plan, $post, $validate = false)
    {
//        Yii::$app->VarDumper->dump($post, 10, true);die;
        // Delete empty item template
        unset($post['Plan']['items']['__EMPTY_ITEM__']);

        // Resort items
        $post['Plan']['items'] = array_values($post['Plan']['items']);

        //procurementMethod
        if ($post['procurementMethod'] == '') {
            $post['Plan']['tender']['procurementMethod'] = '';
            $post['Plan']['tender']['procurementMethodType'] = '';
        } else {
            $method = explode('_', $post['procurementMethod']);
            $post['Plan']['tender']['procurementMethod'] = $method[0];
            $post['Plan']['tender']['procurementMethodType'] = $method[1];
        }

        $post['Plan']['tender']['tenderPeriod']['startDate'] = '01/' . $post['Plan']['tender']['tenderPeriod']['startDate'];


        $plan->load($post, 'Plan');
        if ($validate) {

            // костыль для items
            $newItem = [];
            foreach ($plan->items as $i => $item) {
                if ($i === 'iClass' || $i === '__EMPTY_ITEM__') continue;
                if ($item->description != '') {
                    $newItem[] = $item;
                }
            }
            if (!count($newItem)) {
                $plan->items = [];
            } else {
                $plan->items = $newItem;
            }
            //-----------------------------------------------

//$plan->validate();
//Yii::$app->VarDumper->dump($plan, 10, true);die;

            if (!$plan->validate()) {
                Yii::$app->session->setFlash('message_error', 'Ошибка валидации');
                return false;
            }
        }

//        $plans->title       = $post['Plan']['budget']['project']['name'];
        $plans->description = $post['Plan']['budget']['description'];

        // костыль для items для post
        $newItem = [];
        foreach ($post['Plan']['items'] as $i => $item) {
            if ($item['description'] != '') {
                $newItem[] = $item;
            }
        }
        if (!count($newItem)) {
            $post['Plan']['items'] = [];
        } else {
            $post['Plan']['items'] = $newItem;
        }
        //-----------------------------------------------


        $plans->json = json_encode(['Plan' => $post['Plan']]);
        $plans->status = 'draft';

        $plans->data = ['data' => $post['Plan']];

        if (!$plans->save($validate)) {
            throw new ErrorException('Не удалось сохранить данные в DB');
        }

        return true;
    }

    /**
     * @param $plans Plans
     * @throws \Exception
     * @return bool
     */
    public static function submitToApi($plans)
    {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $plans->data['data']['tender']['tenderPeriod']['startDate'])) {
            $plans->data['data']['tender']['tenderPeriod']['startDate'] = $plans->data['data']['tender']['tenderPeriod']['startDate'] . ' 00:00';
        } elseif (preg_match('/^\d{2}\/\d{4}$/', $plans->data['data']['tender']['tenderPeriod']['startDate'])) {
            $plans->data['data']['tender']['tenderPeriod']['startDate'] = '01/' . $plans->data['data']['tender']['tenderPeriod']['startDate'] . ' 00:00';
        }

//        Yii::$app->VarDumper->dump($plans, 10, true);die;


        ApiHelper::FormatDate($plans->data['data'], true);
//        ApiHelper::CalcPdv($plans->data['data']['budget']);
        $plans->data['data']['procuringEntity'] = [];
        ApiHelper::fillCompany($plans->data['data']['procuringEntity']);
        unset($plans->data['data']['additionalClassifications'][0]['dkType']);
        if ($plans->data['data']['id'] == '') {
            unset($plans->data['data']['id']);
        }
        $plans->data['data']['budget']['id'] = md5($plans->data['data']['classification']['id']);

        //если ДК не выбран
        if ($plans->data['data']['additionalClassifications'][0]['scheme'] == '000') {
            $plans->data['data']['additionalClassifications'][0]['scheme'] = 'none';

            foreach ($plans->data['data']['items'] as $k => &$item) {
                $item['additionalClassifications'][0]['scheme'] = 'NONE';
                // костыль CPV товара должен быть равен главному cpv при создании
//                $item['classification'] = $plans->data['data']['classification'];
            }
        }

        //$plans->data['data']['budget']['id'] = $plans->data['data']['budget']['project']['id'] . $plans->data['data']['classification']['id']; // md5( time() );
//        $plans->data['data']['budget']['year'] = date('Y', strtotime($plans->data['data']['tender']['tenderPeriod']['startDate']));
//        Yii::$app->VarDumper->dump($plans->data, 10, true);die;

        $response = Yii::$app->opAPI->plans(json_encode($plans->data), $plans->plan_id, $plans->token);

//        //сохраняем предыдущую версию плана для сравнения и определения необходимости подписи ЕЦП
//        if($plans->response != ''){
//            $plans->prev_response = $plans->response;
//        }

        $plans->response = $response['raw'];
        $plans->status = 'published';
        $plans->date_modified = $response['body']['data']['dateModified'];

        // isNew
        if (isset($response['body']['access'])) {
            $plans->token = $response['body']['access']['token'];
            $plans->plan_id = $response['body']['data']['id'];
            $plans->plan_cbd_id = $response['body']['data']['planID'];
        }

        return true;
    }


    public static function getPlanProcurementMethod()
    {
        return [
            '' => \Yii::t('app', 'Без застосування електронної системи'),
            'open_belowThreshold' => Yii::t('app', 'Звичайна процедура'),

        ];
    }


}
