<?php

namespace  app\models;

use Yii;

/**
 * This is the model class for table "company_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $country_id
 * @property string $code_length
 *
 * @property Companies[] $companies
 */
class CompanyType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['country_id','code_length'], 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'country_id' => Yii::t('app', 'Country'),
            'code_length' => Yii::t('app', 'Code length'),


        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Companies::className(), ['LegalType' => 'id']);
    }

    public static function GetCompanyTypesByCountryId($id){

        return self::find()->where(['country_id'=>$id])->all();

    }
}
