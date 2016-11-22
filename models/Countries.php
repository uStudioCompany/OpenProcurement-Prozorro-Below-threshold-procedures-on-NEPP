<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property integer $id
 * @property string $name
 * @property string $en_name
 * @property string $ru_name
 * @property string $lang
 *
 * @property Companies[] $companies
 * @property Companies[] $companies0
 * @property Languages $lang0
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'en_name', 'ru_name'], 'string', 'max' => 50],
            [['lang'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'name' => Yii::t('app', 'Назва'),
            'en_name' => Yii::t('app', 'En Name'),
            'ru_name' => Yii::t('app', 'Ru Name'),
            'lang' => Yii::t('app', 'Мова'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Companies::className(), ['countryName' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies0()
    {
        return $this->hasMany(Companies::className(), ['registrationCountryName' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang0()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSheme()
    {
        return $this->hasMany(CountrySheme::className(), ['country_id'=>'id']);
    }
}
