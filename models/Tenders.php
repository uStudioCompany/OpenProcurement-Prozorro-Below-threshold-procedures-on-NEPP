<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use app\components\ApiHelper;
use app\components\SimpleTenderConvertOut;
use yii\base\ErrorException;

/**
 * This is the model class for table "tenders".
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
 * @property string $response
 * @property string $token
 * @property string $transfer_token
 * @property string $tender_id
 * @property string $tender_cbd_id
 * @property string $date_modified
 * @property string $auction_date
 * @property string $tender_type
 * @property string $tender_method
 * @property integer $mail_send_at
 * @property integer $user_action
 * @property integer $ecp
 * @property integer $test_mode
 *
 * @property User $user
 */
class Tenders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tenders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'update_at'], 'integer'],
            [['json', 'response'], 'string'],
            [['token','transfer_token', 'tender_id', 'mail_send_at', 'user_action', 'ecp', 'test_mode', 'tender_type', 'tender_method'], 'safe'],
            [['title', 'tender_cbd_id', 'description', 'status', 'date_modified','auction_date'], 'string', 'max' => 255],
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
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
            'json' => Yii::t('app', 'Json'),
            'response' => Yii::t('app', 'Response'),
            'token' => Yii::t('app', 'Token'),
            'transfer_token' => Yii::t('app', 'Transfer Token'),
            'tender_id' => Yii::t('app', 'tender_id'),
            'tender_cbd_id' => Yii::t('app', 'tenderID'),
            'auction_date' => Yii::t('app', 'auctionDate'),
            'tender_type' => Yii::t('app', 'tender_type'),
            'mail_send_at' => Yii::t('app', 'mail_send_at'),
            'user_action' => Yii::t('app', 'user_action'),
            'ecp' => Yii::t('app', 'ecp'),
            'test_mode' => Yii::t('app', 'ecp'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if (is_a(Yii::$app, 'yii\web\Application')) {// если не консоль
                $this->user_id = Yii::$app->user->id;
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

    public function getBids()
    {
        return $this->hasOne(Bids::className(), ['tid' => 'id']);
    }

    public static function getTenderEnquiryEndDate($id)
    {
        $res = Tenders::findOne($id);
        $res = Json::decode($res['response']);

        if (!isset($res['data']['enquiryPeriod'])) { // @TODO: убрать КОСТЫЛЬ
            return false;
        }

        $res = $res['data']['enquiryPeriod']['endDate'];
        if ($res) {
            $res = Yii::$app->formatter->asDate($res, 'dd-MM-Y H:i');
            return $res;
        }
    }

    public static function getTenderDocumentsOf()
    {
        return Yii::$app->params['document_types'];
    }

    public static function getTenderType()
    {
        return [
            '1' => \Yii::t('app', 'Проста закупiвля'),
            '2' => \Yii::t('app', 'Мультилотова закупiвля'),
        ];
    }

    public static function getTenderMethodDef()
    {
        return 'open_belowThreshold';
    }

    public static function getTenderMethod()
    {

        return [
            'open_belowThreshold' => Yii::t('app','Звичайна процедура'),

        ];
    }

    public static function getTenderMethodByCompanyId($companyId, $tenderMethod)
    {

        return [
            'open_belowThreshold' => Yii::t('app', 'Звичайна процедура'),

        ];


    }

    public static function getAllTenderMethod()
    {


        return [
            'open_belowThreshold' => Yii::t('app','Звичайна процедура'),

        ];
    }


    public static function GetJustificationMethod()
    {
        return [
            'artContestIP' => Yii::t('app','cт35 п1'),
            'noCompetition' => Yii::t('app','cт35 п2'),
            'quick' => Yii::t('app','cт35 п3'),
            'twiceUnsuccessful' => Yii::t('app','cт35 п4'),
            'additionalPurchase' => Yii::t('app','cт35 п5'),
            'additionalConstruction' => Yii::t('app','cт35 п6'),
            'stateLegalServices' => Yii::t('app','cт35 п7'),
        ];
    }

    /**
     * @param $post
     * @return \app\models\Tenders
     * @throws ErrorException
     */
    public static function getModel($post)
    {
        if (isset($post['Tender']['tenderId']) && $post['Tender']['tenderId']) {
            return self::getModelById($post['Tender']['tenderId']);
        } else {
            return new Tenders();
        }
    }

    /**
     * @param $id
     * @return \app\models\Tenders
     * @throws ErrorException
     */
    public static function getModelById($id)
    {
        if ((int)$id != 0) {
            $tenders = Tenders::findOne(['id' => $id]);
        } else {
            $tenders = Tenders::findOne(['tender_cbd_id' => $id]);
        }
        if (!$tenders) {
            throw new ErrorException('Нет такого тендера.');
        }
        return $tenders;
    }


    public static function CheckAllowedStatus($id, $status, $tenders = null)
    {
        if ($tenders == null) {
            $tenders = self::getModelById($id);
        }
        if  ($status == 'update') {
            foreach (Json::decode($tenders->response)['data']['awards'] as $award) {
                if (in_array($award['status'], ['active', 'pending'])) {
                    return false;
                }
            }
        }
        $allowedStatuses = Yii::$app->params['allowed.tender.' . $status . '.status'];
        foreach ($allowedStatuses as $allowedStatus) {
            if ($tenders->status == $allowedStatus) {
                return true;
            }
        }
        return false;
    }

    public static function CheckAllowedAnwerStatus($answer, $tenderMethod, $tenderStatus)
    {
        if (in_array($tenderStatus, Yii::$app->params['allowed.tender.question.answer.status'])) {
            if ($tenderMethod == 'open_belowThreshold' && $answer == '' && $tenderStatus == 'active.enquiries') {
                return true;
            }


            return false;
        }

        return false;
    }

    public static function CheckAllowedQuestionStatus($status, $tenderMethod)
    {


        if($tenderMethod =='open_belowThreshold'){
            if ($status =='active.enquiries') {
                return true;
            }else{
                return false;
            }
        }

    }

    public static function checkTenderECP($documents){
        foreach ($documents as $d=>$document) {
            if($document->format == 'application/pkcs7-signature'){
                return true;
            }
        }
        return false;
    }

    public static function getUsefulTenderInformation($tender)
    {
        if (!isset($tender) || is_null($tender) || !$tender)
            return null;
        $newTender = [];
        $newTender['tid'] = $tender->id;
        $newTender['tender_cbd_id'] = $tender->tender_cbd_id;
        $newTender['user_id'] = $tender->user_id;
        $newTender['company_id'] = $tender->company_id;
        $newTender['status'] = $tender->status;
        $newTender['title'] = $tender->title;
        $newTender['description'] = $tender->description;
        $data = Json::decode($tender->response, true)['data'];
        $newTender['lots'] = $data['lots'];
        $newTender['items'] = $data['items'];
        $newTender['bids'] = $data['bids'];
        $newTender['qualifications'] = $data['qualifications'];
        return $newTender;
    }

}
