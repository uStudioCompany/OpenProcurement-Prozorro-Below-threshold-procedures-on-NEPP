<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "complaints".
 *
 * @property integer $id
 * @property string $question_id
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
class Questions extends \yii\db\ActiveRecord
{

    public $cancellationReason;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['cancellationReason'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, введіть країну адреси доставки')],
            [['company_id', 'user_id', 'create_at', 'tid'], 'integer'],
            [['question_id', 'token', 'type'], 'string', 'max' => 255],
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
            'question_id' => Yii::t('app', 'Question ID'),
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
                            if (!in_array($complaint['status'], ['stopped','satisfied', 'declined', 'invalid', 'draft','mistaken'])) {
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
                                if (!in_array($complaint['status'], ['stopped', 'satisfied', 'declined', 'invalid','mistaken'])) {
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

    /**
     * @param $tenders
     * @param $tender
     * Скарга
     *
     */
    public static function IsShowSendComplaintPendingForm($tenders, $tender){
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

    /**
     * @param $tenders
     * @param $tender
     * Ответ на вимоги
     *
     */

    public static function IsShowComplaintAnswerForm($tenders, $tender){
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


}
