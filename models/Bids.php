<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "bids".
 *
 * @property integer $id
 * @property string $bid_id
 * @property string $token
 * @property string $json
 * @property string $answer
 * @property integer $tid
 * @property integer $company_id
 * @property integer $user_id
 * @property integer $create_at
 * @property integer $update_at
 * @property integer $date_modified
 */
class Bids extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bids';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'company_id', 'user_id', 'create_at', 'update_at'], 'integer'],
            [['bid_id', 'token'], 'string', 'max' => 50],
            [['json', 'answer','date_modified'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bid_id' => Yii::t('app', 'Bid ID'),
            'token' => Yii::t('app', 'Token'),
            'json' => Yii::t('app', 'json'),
            'answer' => Yii::t('app', 'answer'),
            'tid' => Yii::t('app', 'Tid'),
            'company_id' => Yii::t('app', 'Company ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'create_at' => Yii::t('app', 'Create At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->create_at = time();
        } else {
            $this->update_at = time();
        }

        return parent::beforeSave($insert);
    }

    public static function getModel($id){
        $res = Bids::find()
            ->where(['company_id'=>Yii::$app->user->identity->company_id])
            ->andWhere(['tid'=>$id])
            ->one();

        return $res ? $res : new Bids();

    }

    public static function getCompanyBid($tenderId){
        $res = Bids::findOne(['company_id'=>Yii::$app->user->identity->company_id, 'tid'=>$tenderId]);
        return $res ? $res->bid_id : '';

    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTenders()
    {
        return $this->hasOne(Tenders::className(), ['id' => 'tid']);
    }

    public function getLotsArr() {
        $lots = null;
        $bid_data = json_decode($this->json,1);
        if (isset($bid_data['data']['lotValues'])) {
            foreach ($bid_data AS $bid_lot) {
                $lots[] = $bid_lot['relatedLot'];
            }
        }
        return $lots;
    }



    /** Проверяет принимала ли участие компания в первом этапе указаного тендера
     * Если указан $lotID, то проверяет на участие в конкретном лоте
     *
     * @param $tenders
     * @param null $lotID
     * @return bool
     */
    public static function checkFirstStageOnBidByCompany($tenders, $lotID = null)
    {
        $data = Json::decode($tenders->response)['data'];
        $stage1TenderID = $data['dialogueID'];
        if (isset($stage1TenderID)) {
            $stage1Tender = Tenders::findOne(['tender_id' => $stage1TenderID]);
            if (isset($stage1Tender)) {
                $stage1Qualifications = Json::decode($stage1Tender->response)['data']['qualifications'];
                foreach ($stage1Qualifications as $qualification) {
                    if (isset($qualification['bidID']) && $qualification['status'] == 'active') {
                        $stage1Bid = Bids::findOne(['bid_id' => $qualification['bidID']]);
                        if ($lotID == null) {
                            if (Yii::$app->user->identity->company_id == $stage1Bid->company_id) {
                                return true;
                            }
                        } else {
                            if ($stage1Bid->company_id == Yii::$app->user->identity->company_id && $qualification['lotID'] == $lotID) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /** Проверяет, есть ли возможность у пользователя подавать жалобы (на квалификацию и преквалификацию)
     * Если у него были ставки, то может, если нет - нет
     *
     * @param $tenders
     * @return bool
     */
    public static function AccessMembersOfAuction($tenders)
    {
        $bids = Json::decode($tenders->response)['data']['bids'];
        foreach ($bids as $bid) {
            if (isset($bid['id']) && $bid['status'] == 'active') {
                $stage1Bid = Bids::findOne(['bid_id' => $bid['id']]);
                if (Yii::$app->user->identity->company_id == $stage1Bid->company_id) {
                    return true;
                }
            }
        }
        return false;
    }
}
