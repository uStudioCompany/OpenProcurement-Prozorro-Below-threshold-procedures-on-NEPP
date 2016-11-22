<?php

namespace app\models;

use app\components\apiDataException;
use app\components\apiException;
use app\models\tenderModels\Qualifications;
use Yii;
use yii\base\ErrorException;

/**
 * This is the model class for table "complaints".
 *
 * @property integer $id
 * @property string $complaint_id
 * @property string $token
 * @property string $tid
 * @property integer $company_id
 * @property integer $user_id
 * @property integer $type
 * @property integer $create_at
 * @property integer $cancellationReason
 *
 *
 * @property Companies $company
 */
class Complaints extends \yii\db\ActiveRecord
{

    public $cancellationReason;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'complaints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['cancellationReason'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, введіть країну адреси доставки')],
            [['company_id', 'user_id', 'create_at', 'tid'], 'integer'],
            [['complaint_id', 'token', 'type'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companies::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'complaint_id' => Yii::t('app', 'Complaint ID'),
            'token' => Yii::t('app', 'Token'),
            'tid' => Yii::t('app', 'Tender Id'),
            'company_id' => Yii::t('app', 'Company ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'create_at' => Yii::t('app', 'Create At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }

    public static function getComplaintByid($data, $complaintId, $stage){


        if ($stage == 'complaints') {
            foreach ($data['data']['complaints'] as $c => $complaint) {
                if ($complaint['id'] == $complaintId) {
                    return $complaint;
                }
            }
            return false;
        } elseif ($stage == 'prequalification') {
            foreach ($data['data']['qualifications'] as $q => $qualification) {
                if ($qualification['status'] == 'cancelled') continue;
                if (isset($qualification['complaints']) && count($qualification['complaints'])) {
                    foreach ($qualification['complaints'] as $c => $complaint) {
                        if ($complaint['id'] == $complaintId) {
                            return $complaint;
                        }
                    }
                }

            }
            return false;
        } elseif ($stage == 'qualification') {
            foreach ($data['data']['awards'] as $a => $award) {
                if ($award['status'] == 'cancelled') continue;
                if (isset($award['complaints']) && count($award['complaints'])) {
                    foreach ($award['complaints'] as $c => $complaint) {
                        if ($complaint['id'] == $complaintId) {
                            return $complaint;
                        }
                    }
                }

            }
            return false;
        }
    }


    public static function getCompanyComplains($tid)
    {
        $res = (new \yii\db\Query())->select(['complaint_id'])->from('complaints')->where(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tid])->all();
        if ($res) {
            $arr = [];
            foreach ($res as $k => $v) {
                $arr[] = $v['complaint_id'];
            }
            return $arr;
        } else {
            return [];
        }

    }


    public static function getCompanyQualificationComplains($tid)
    {
        $res = (new \yii\db\Query())->select(['complaint_id'])->from('complaints')->where(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tid])->all();
        if ($res) {
            $arr = [];
            foreach ($res as $k => $v) {
                $arr[] = $v['complaint_id'];
            }
            return $arr;
        } else {
            return [];
        }

    }

    public static function getCompanyAwardComplains($tid)
    {
        $res = (new \yii\db\Query())->select(['complaint_id'])->from('complaints')->where(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tid, 'type' => 'award'])->all();
        if ($res) {
            $arr = [];
            foreach ($res as $k => $v) {
                $arr[] = $v['complaint_id'];
            }
            return $arr;
        } else {
            return [];
        }

    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function getNotClosedComplaintsInAllAwards($tenders, $awardId){

        $data = \yii\helpers\Json::decode($tenders->response);

        if($tenders->tender_type == 1){
            if (isset($data['data']['awards']) && count($data['data']['awards'])) {
                foreach ($data['data']['awards'] as $a=>$award) {
                    if (in_array($award['status'], ['cancelled'])) continue; // ???
                    if (isset($award['complaints']) && count($award['complaints'])) {
                        foreach ($award['complaints'] as $c => $complaint) {
                            //добавил 'cancelled', если что-то сломалось, то убрать:) и в мультилот тоже. может еще нжно добавить answered(тут вроде все нормально) ??claim?? и убрать stopping
                            if (!in_array($complaint['status'], ['stopped','satisfied', 'declined', 'invalid', 'draft','mistaken', 'stopping', 'cancelled', 'answered'])) {
                                return false;
                            }
                        }
                    }
                }
            }
            return true;
        }else{
            if (isset($data['data']['awards']) && count($data['data']['awards'])) {
                foreach ($data['data']['awards'] as $a=>$award) {
                    if (in_array($award['status'], ['cancelled'])) continue; // ???
                    if($award['id'] == $awardId){
                        if (isset($award['complaints']) && count($award['complaints'])) {
                            foreach ($award['complaints'] as $c => $complaint) {
                                if (!in_array($complaint['status'], ['stopped', 'satisfied', 'declined', 'invalid','mistaken', 'stopping', 'cancelled', 'answered'])) {
                                    return false;
                                }
                            }
                        }
                    }

                }
            }
            return true;
        }





    }

    /**
     * @param $tenders
     * @param $tender
     * Вимога
     *
     */

    public static function IsShowSendComplaintClaimForm($tenders, $tender){
        if(\app\models\Companies::getCompanyBusinesType() != '' && !\app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) {
            if (in_array($tenders->tender_method,['open_belowThreshold']) && $tenders->status == 'active.enquiries') {
                if(strtotime(str_replace('/', '.', $tender->enquiryPeriod->endDate)) <= strtotime('now') ){
                    return false;
                }else{
                    return true;
                }
            }
        }

        return false;
    }

    /**Скарга
     *
     * @param $tenders
     * @param $tender
     * @return bool
     */
    public static function IsShowSendComplaintPendingForm($tenders, $tender){
        $companyBusinessType = Companies::getCompanyBusinesType();
        if( in_array($tender->status,['cancelled', 'unsuccessful']) ) {
            return false;
        }
        //это убрать, чтобы buyer тоже мог
        if ($companyBusinessType != 'seller') {
            return false;
        }
        if($companyBusinessType != '' && !Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) {
            if (in_array($tenders->tender_method,['open_belowThreshold']) && $tenders->status == 'active.enquiries') {
                if(strtotime(str_replace('/', '.', $tender->enquiryPeriod->endDate)) <= strtotime('now') ){
                    return false;
                }else{
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $tenders
     * @param $tender
     * Ответ на вимоги
     *
     */
    public static function IsShowComplaintAnswerForm($tenders, $tender){
        if( in_array($tender->status,['cancelled', 'unsuccessful']) ) {
            return false;
        }
        if(\app\models\Companies::getCompanyBusinesType() == 'buyer' && \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) {
            if (in_array($tenders->tender_method,['open_belowThreshold']) && $tenders->status == 'active.enquiries') {
                if(strtotime(str_replace('/', '.', $tender->enquiryPeriod->endDate)) <= strtotime('now') ){
                    return false;
                }else{
                    return true;
                }
            }
        }

        return false;
    }


    public static function isOwner($tenders,$complaint_id) {
        if (Companies::getCompanyBusinesType() == 'seller') {
            $companyComplaints = Companies::getSellerCompanyComplaints($tenders->id);
            //echo '<pre>'; print_r($companyComplaints); DIE();
            foreach ($companyComplaints as $k => $companyComplaint) {
                if ($complaint_id == $companyComplaint->complaint_id) {
                    return true;
                }
            }
        } else if (Companies::getCompanyBusinesType() == 'buyer') {
            if ($tenders->company_id == Yii::$app->user->identity->company_id) {
                return true;
            }
        }
        return false;
    }

    public static function isCanAddDocuments($tender_method,$complaint_status)
    {
        return false;
    }

    public static function isOwnerComplaintById($complaintID)
    {
        $complaint = Complaints::findOne(['complaint_id' => $complaintID]);
        if ($complaint->company_id == Yii::$app->user->identity->company_id){
            return true;
        } else {
            return false;
        }
    }

    public static function getSatisfiedComplaint($obj)
    {
        if (isset($obj['complaints']) && count($obj['complaints'])) {
            foreach ($obj['complaints'] as $c => $complaint) {
                if (in_array($complaint['status'], ['satisfied'])) {
                    return true;
                }
            }
        }
    }

    public static function resolvedSatisfiedComplaint($tender, $tenders, $post)
    {
        $data = array_values($post['Qualifications'])[0];
        $qualification = Qualifications::getQualificationById($tender, $data['id']);

        if (isset($qualification['complaints']) && count($qualification['complaints'])) {
            foreach ($qualification['complaints'] as $c => $complaint) {
                if (in_array($complaint['status'], ['satisfied'])) {

                    $json = [
                        'data' => [
                            'status' => 'resolved',
                            'tendererAction' => 'Виправлено',
                        ]
                    ];

                    $url = $tenders->tender_id . '/qualifications/' . $data['id'] . '/complaints/' . $complaint['id'];

                    try {
                        $response = Yii::$app->opAPI->tenders(
                            Json::encode($json),
                            $url,
                            $tenders->token
                        );

                    } catch (apiDataException $e) {
                        throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    } catch (apiException $e) {
                        throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    }
                }
            }
        }
    }





}
