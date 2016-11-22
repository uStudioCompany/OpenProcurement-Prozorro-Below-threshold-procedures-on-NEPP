<?php

namespace app\models;

use app\components\ApiHelper;
use app\components\HTender;
use Yii;
use yii\helpers\FormatConverter;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "contracting".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $tid
 * @property string $title
 * @property string $description
 * @property string $status
 * @property integer $created_at
 * @property integer $update_at
 * @property string $json
 * @property string $signed_data
 * @property string $response
 * @property string $token
 * @property string $contract_id
 * @property string $ecp
 * @property string $date_modified
 * @property string $contract_cbd_id
 *
 * @property string $tender_cbd_id
 */
class Contracting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contracting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'company_id','tid', 'created_at', 'update_at'], 'integer'],
            [['company_id', 'date_modified'], 'required'],
            [['json', 'signed_data', 'response'], 'string'],
            [['title', 'description', 'status'], 'string', 'max' => 255],
            [['token', 'contract_id', 'contract_cbd_id'], 'string', 'max' => 32],
            [['date_modified'], 'string', 'max' => 50],
            [['ecp'], 'safe'],
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
            'company_id' => Yii::t('app', 'Company ID'),
            'tid' => Yii::t('app', 'Tender ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
            'json' => Yii::t('app', 'Json'),
            'signed_data' => Yii::t('app', 'Signed Data'),
            'response' => Yii::t('app', 'Response'),
            'token' => Yii::t('app', 'Token'),
            'contract_id' => Yii::t('app', 'Contract ID'),
            'date_modified' => Yii::t('app', 'Date Modified'),
            'ecp' => Yii::t('app', 'ecp'),
            'contract_cbd_id' => Yii::t('app', 'contractID')
        ];
    }

    public static function getModel($id){
        $res = Contracting::find()
            ->where(['id'=>$id])
            ->one();

        return $res ? $res : new Contracting();

    }

    public static function ConvertOut($post){
//        Yii::$app->VarDumper->dump($post, 10, true);die;
    }
    public static function saveToDB($post, $contractModel){

        if(isset($post['Changes'])) {
            $data['data'] = $post['Changes'];
            $data['data']['period']['endDate'] = date('c', strtotime(str_replace('/', '.', $data['data']['period']['endDate'])));
            $contractModel->json = Json::encode($data);
            $contractModel->save(false);
            return $data;
        }elseif(isset($post['Terminate'])) {
            $data['data'] = $post['Terminate'];
            $data['data']['status'] = 'terminated';
            if($data['data']['terminateType'] == 0){
                unset($data['data']['terminationDetails']);
            }
            unset($data['data']['terminateType']);

            $contractModel->json = Json::encode($data);
            $contractModel->save(false);
            return $data;
        }
    }

    public static function SplitData($data){

        //данные по ченджу у нас должны быть всегда
        $res['change']['data']['rationaleTypes'] = $data['data']['rationaleTypes'];
        $res['change']['data']['rationale'] = $data['data']['rationale'];
        $res['change']['data']['contractNumber'] = $data['data']['contractNumber'];
        $res['change']['data']['dateSigned'] = ApiHelper::convertDate($data['data']['dateSigned'], true);

        // а тут надо проверить
        if($res['contracts']['data']['period']['endDate'] != ''){
            $res['contracts']['data']['period'] = $data['data']['period'];
        }elseif ($data['data']['value']['amount'] != ''){
            $res['contracts']['data']['value'] = $data['data']['value'];
        }


//Yii::$app->VarDumper->dump($res, 10, true, true);
        return $res;
    }

    public function getTenders()
    {
        return $this->hasOne(Tenders::className(), ['id' => 'tid']);
    }

    public function getTenderCbdId()
    {
        return $this->tenders->tender_cbd_id;
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
