<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property integer $id
 * @property string $userName
 * @property string $userSurname
 * @property string $userPatronymic
 * @property string $email
 * @property string $telephone
 * @property string $faxNumber
 * @property string $mobile
 * @property string $url
 * @property integer $company
 *
 * @property Companies $company0
 */
class Contacts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userName', 'userSurname', 'userPatronymic', 'email', 'telephone', 'faxNumber', 'mobile', 'url'], 'required'],
            [['company'], 'integer'],
            [['userName', 'userSurname', 'userPatronymic', 'email'], 'string', 'max' => 50],
            [['telephone', 'faxNumber', 'mobile'], 'string', 'max' => 20],
            [['url'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['userSurname'], 'unique'],
            [['userName'], 'unique'],
            [['telephone'], 'unique'],
            [['userPatronymic'], 'unique'],
            [['faxNumber'], 'unique'],
            [['mobile'], 'unique'],
            [['url'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userName' => Yii::t('app', 'User Name'),
            'userSurname' => Yii::t('app', 'User Surname'),
            'userPatronymic' => Yii::t('app', 'User Patronymic'),
            'email' => Yii::t('app', 'Email'),
            'telephone' => Yii::t('app', 'Telephone'),
            'faxNumber' => Yii::t('app', 'Fax Number'),
            'mobile' => Yii::t('app', 'Mobile'),
            'url' => Yii::t('app', 'Url'),
            'company' => Yii::t('app', 'Company'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company']);
    }
}
