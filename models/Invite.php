<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "invite".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $company_id
 * @property string $token
 * @property string $email
 * @property string $fio
 * @property integer $status
 */
class Invite extends \yii\db\ActiveRecord
{

    /**
     * @var array
     */
    public $statuses_values = ['invite.new', 'invite.confirmed', 'invite.refused', 'X3'];

    /**
     * @var array
     */
    public $statuses_icons = ['question-sign', 'ok', 'ban-circle', ''];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'fio'], 'required'],
            [['token', 'fio'], 'string', 'max' => 255],
            ['email', 'unique', 'targetAttribute'=>['email'=>'username'],'targetClass' => 'app\models\User', 'message' => Yii::t('app', 'Такой логин уже существует.')],
            [['email'], 'email'],
            ['email', 'filter', 'filter' => 'trim'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'email'),
            'fio' => Yii::t('app', 'fio'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @param bool $insert whether this method called while inserting a record.
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (!$this->company_id) {
                    $this->company_id = Yii::$app->user->identity->company_id;
                }
                if (!$this->owner_id) {
                    $this->owner_id = Yii::$app->user->identity->id;
                }
                $this->status = 0;
                $this->token = Yii::$app->security->generateRandomString(32);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $insert whether this method called while inserting a record.
     * @return bool
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            Notifications::sendInvite($this->email, $this->token);
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->update();
    }
}
