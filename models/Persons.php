<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "persons".
 *
 * @property integer $id
 * @property string $userName
 * @property string $userName_en
 * @property string $userName_ru
 * @property string $userSurname
 * @property string $userSurname_en
 * @property string $userSurname_ru
 * @property string $userPatronymic
 * @property string $userPatronymic_en
 * @property string $userPatronymic_ru
 * @property string $email
 * @property string $telephone
 * @property string $faxNumber
 * @property string $mobile
 * @property string $url
 * @property integer $company_id
 * @property integer $availableLanguage
 *
 * @property Companies $company
 */
class Persons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'persons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userName', 'userSurname', 'userPatronymic','userName_en','userSurname_en', 'email', 'telephone','availableLanguage'], 'required'],
//            [['company_id'], 'integer'],
//            [['userName', 'userName_en', 'userName_ru','url', 'userSurname', 'userSurname_en', 'userSurname_ru', 'userPatronymic', 'userPatronymic_en','userPatronymic_ru', 'email'], 'string'],
            [['telephone', 'faxNumber', 'mobile'], 'string', 'max' => 20],
            [['email'], 'email'],
            [['userName', 'userName_en', 'userName_ru','url', 'userSurname', 'userSurname_en', 'userSurname_ru', 'userPatronymic', 'userPatronymic_en','userPatronymic_ru'], 'string','max' => 60],
//            [['url'], 'match', 'pattern' => '/^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$/g'],
            [['url'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'userName' => Yii::t('app', 'Name'),
            'userName_en' => Yii::t('app', 'English Name'),
            'userName_ru' => Yii::t('app', 'Name'),
            'userSurname' => Yii::t('app', 'Surname'),
            'userSurname_en' => Yii::t('app', 'English Surname'),
            'userSurname_ru' => Yii::t('app', 'Surname_ru'),
            'userPatronymic' => Yii::t('app', 'Patronymic'),
            'userPatronymic_en' => Yii::t('app', 'English Patronymic'),
            'userPatronymic_ru' => Yii::t('app', 'Patronymic_ru'),
            'email' => Yii::t('app', 'email'),
            'telephone' => Yii::t('app', 'Contact phone'),
            'faxNumber' => Yii::t('app', 'Fax number'),
            'mobile' => Yii::t('app', 'Mobile'),
            'url' => Yii::t('app', 'Web site address'),
            'company_id' => Yii::t('app', 'Id Компании'),
            'availableLanguage'=>Yii::t('app', 'Мова спiлкування')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }

    public static function findPersonsByCompanyId($company_id)
    {
        return static::findAll(['company_id' => $company_id]);
    }

}
