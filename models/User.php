<?php

namespace app\models;

use yii\base\NotSupportedException;
use yii\base\Security;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $fio
 * @property string $phone
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $company_id
 * @property integer $is_owner
 * @property string $activationcode
 * @property integer $subscribe_status
 *
 * @property Companies $company
 * @property Companies $companyIdOwner
 * @property Companies $company0
 * @property Companies $companyIdOwner0
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 10;

    const STATUS_ADMIN = 20;

    public $role;

    public $password;

    public $confirmPassword;

    public $info1;

    public $info2;

    public $info3;

//    public $subscribe_status;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            [['subscribe_status','status'], 'integer'],
            [['username', 'password', 'confirmPassword', 'subscribe_status', 'info1', 'info2', 'info3'], 'required'],
            [['username'], 'string', 'max' => 50],
            ['username', 'email'],
            ['username', 'unique', 'targetClass' => 'app\models\User', 'message' => Yii::t('app', 'Такой логин уже существует.')],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['info1', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I certify the accuracy of the provided information')],
            ['info2', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I agree to the storage and processing of my personal data')],
            ['info3', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'I agree with Regulation of e-GP')],
            [['password', 'confirmPassword'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', 'pass.not.equal')],

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
            'confirmPassword' => Yii::t('app', 'confirmPassword'),
            'fio' => Yii::t('app', 'fio'),
            'phone' => Yii::t('app', 'Phone'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'company_id' => Yii::t('app', 'Company ID'),
            'is_owner' => Yii::t('app', 'Company Id Owner'),
            'activationcode' => Yii::t('app', 'Activationcode'),
            'subscribe_status' => Yii::t('app', 'I agree to the correspondence by e-mail and receive SMS-messages'),
            'info1' => Yii::t('app', 'I certify the accuracy of the provided information'),
            'info2' => Yii::t('app', 'I agree to the storage and processing of my personal data'),
            'info3' => Yii::t('app', 'I agree with Regulation of e-GP'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {

            if (isset($this->password)) {
                $this->setPassword($this->password);
                $this->generateAuthKey();
                $this->generateActivationCode();
                $this->created_at = date('U');
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [self::STATUS_ACTIVE, self::STATUS_ADMIN]]);
    }

    public static function getOwnerByCompanyId($id)
    {
        $query = User::find()
            ->select(['user.id', 'user.username'])
            ->from(['user'])
            ->join('LEFT JOIN', 'companies', 'companies.id = user.company_id')
            ->where([
                'user.status' => self::STATUS_ACTIVE,
                'companies.id' => $id,
                'user.is_owner' => 1
            ])
            ->one();
        return $query;

    }


    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => [self::STATUS_ACTIVE, self::STATUS_ADMIN]]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => [self::STATUS_ACTIVE, self::STATUS_ADMIN]
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
//        var_dump($this->password_hash); die;
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateActivationCode()
    {
        $this->activationcode = Yii::$app->security->generateRandomString(16);
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function findByCompanyId($company_id)
    {
        return static::findAll(['company_id' => $company_id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }

    /**
     * @param $status
     * @throws \Exception
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->update();
    }

    public static function checkAdmin()
    {
        return Yii::$app->user->identity->status == self::STATUS_ADMIN;
    }

    public static function getAllStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'active'),
            self::STATUS_INACTIVE => Yii::t('app', 'inactive'),
          //  self::STATUS_ADMIN => Yii::t('app', 'Blocked')
        ];
    }

}
