<?php

namespace app\models;

use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "user_join_requests".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $fio
 * @property string $phone
 * @property integer $company_id
 * @property integer $create_time
 * @property string $auth_key
 * @property string $password_hash
 * @property string $activationcode
 *
 * @property Companies $company
 */
class UserJoinRequests extends \yii\db\ActiveRecord
{

//    public $confirmPassword;

    public $joinToIdentifier;

//    public $info1;

//    public $info2;

//    public $info3;

//    public $subscribe_status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_join_requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'create_time'], 'integer'],
            [['username', 'fio', 'phone'], 'required'],
//            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => "Пароли не совпадают"],
            [['username'], 'email'],
            ['username', 'unique', 'targetClass' => 'app\models\User', 'message' => Yii::t('app','Такой логин уже существует.')],
//            [['password', 'confirmPassword'], 'string', 'min' => 6],
//            ['info1', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I certify the accuracy of the provided information')],
//            ['info2', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I agree to the storage and processing of my personal data')],
//            ['info3', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I agree with Regulation of e-GP')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Login (email)'),
            'password' => Yii::t('app', 'Password'),
            'fio' => Yii::t('app', 'fio'),
            'phone' => Yii::t('app', 'Contact phone'),
            'company_id' => Yii::t('app', 'Company ID'),
            'create_time' => Yii::t('app', 'Create Time'),
//            'auth_key' => Yii::t('app', 'Auth Key'),
//            'password_hash' => Yii::t('app', 'Password Hash'),
            'activationcode' => Yii::t('app', 'Activationcode'),
            'subscribe_status' => Yii::t('app', 'I agree to the correspondence by e-mail and receive SMS-messages'),
            'info1' => Yii::t('app', 'I certify the accuracy of the provided information'),
            'info2' => Yii::t('app', 'I agree to the storage and processing of my personal data'),
            'info3' => Yii::t('app', 'I agree with Regulation of e-GP'),
        ];
    }

    public function beforeSave($insert)
    {
//        $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
//        $this->auth_key = Yii::$app->security->generateRandomString();
        $this->activationcode = Yii::$app->security->generateRandomString(16);
        $this->create_time = date('U');

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }
}
