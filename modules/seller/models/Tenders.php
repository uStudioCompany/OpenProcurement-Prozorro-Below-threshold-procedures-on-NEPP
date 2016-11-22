<?php

namespace app\modules\seller\models;

use Yii;


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
 * @property string $tender_id
 * @property string $tender_cbd_id
 * @property string $date_modified
 * @property string $tender_type
 * @property string $tender_method
 * @property integer $mail_send_at
 * @property integer $user_action
 *
 * @property User $user
 */
class Tenders extends \app\models\Tenders
{

//    public function rules()
//    {
//        return [
//            [['user_id', 'created_at', 'update_at'], 'integer'],
//            [['json', 'response'], 'string'],
//            [['token', 'tender_id', 'mail_send_at', 'user_action'], 'safe'],
//            [['title', 'tender_cbd_id', 'description', 'status', 'date_modified'], 'string', 'max' => 255]
//        ];
//    }

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

    public static function CheckAllowedStatus($id, $status)
    {
        $Tenderstatus = self::getModelById($id);
        $allowedStatuses = Yii::$app->params['allowed.tender.' . $status . '.status'];
        foreach ($allowedStatuses as $allowedStatus) {
            if ($Tenderstatus->status == $allowedStatus) {
                return true;
            }
        }
        return false;
    }



}
