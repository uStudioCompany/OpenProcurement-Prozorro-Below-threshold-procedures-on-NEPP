<?php

namespace app\models\viewerModels;

use Yii;
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
            [['token', 'tender_id', 'mail_send_at', 'user_action'], 'safe'],
            [['title', 'tender_cbd_id', 'description', 'status', 'date_modified'], 'string', 'max' => 255]
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
            'tender_id' => Yii::t('app', 'tender_id'),
            'tender_cbd_id' => Yii::t('app', 'tenderID'),
            'tender_type' => Yii::t('app', 'tender_type'),
            'mail_send_at' => Yii::t('app', 'mail_send_at'),
            'user_action' => Yii::t('app', 'user_action'),
        ];
    }




    /**
     * @param $id
     * @return \app\models\Tenders
     * @throws ErrorException
     */
    public static function getModelById($id)
    {
        $tenders = Tenders::findOne(['id' => $id]);
        if (!$tenders) {
            throw new ErrorException('Нет такого тендера.');
        }
        return $tenders;
    }


}
