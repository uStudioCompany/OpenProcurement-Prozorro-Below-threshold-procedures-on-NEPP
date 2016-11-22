<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delivery_address".
 *
 * @property integer $id
 * @property integer $countryName
 * @property integer $region
 * @property string $locality
 * @property string $locality_en
 * @property string $locality_ru
 * @property string $postalCode
 * @property string $lat
 * @property string $lng
 * @property integer $company_id
 *
 * @property Companies $company
 * @property Countries $countryName0
 * @property Regions $region0
 */
class Delivery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delivery_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['countryName', 'region', 'lat', 'lng', 'locality', 'postalCode'], 'required'],
                [['countryName', 'region', 'company_id'], 'integer'],
                [['lat', 'lng'], 'number'],
                [['locality', 'locality_en', 'locality_ru'], 'string', 'max' => 255],
                [['postalCode'], 'string', 'min' => 5, 'max' => 5],
            ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'countryName' => Yii::t('app', 'Country Name'),
            'region' => Yii::t('app', 'Region'),
            'locality' => Yii::t('app', 'Locality'),
            'locality_en' => Yii::t('app', 'Locality En'),
            'locality_ru' => Yii::t('app', 'Locality Ru'),
            'postalCode' => Yii::t('app', 'Postal Code'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'company_id' => Yii::t('app', 'Company ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'countryName']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDregion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region']);
    }
}
