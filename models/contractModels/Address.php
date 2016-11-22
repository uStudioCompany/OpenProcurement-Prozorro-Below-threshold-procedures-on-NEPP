<?php

namespace app\models\contractModels;

use Yii;
class Address extends BaseModel
{
    public $streetAddress;
    public $locality;             
    public $region; 
    public $postalCode;
    public $countryName;

    public function rules()
    {
        return [
            [['countryName','region','locality', 'streetAddress','postalCode'], 'required'],
            [['countryName','region','locality', 'streetAddress'], 'string', 'max'=>100],
            [['postalCode'], 'integer'],
            [['postalCode'], 'string', 'length'=>5],

            [['countryName'], 'safe', 'on'=>'limitedavards'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'streetAddress' => Yii::t('app', 'Вулиця'),
            'locality' => Yii::t('app', 'Населений пункт'),
            'region' => Yii::t('app', 'Область'),
            'postalCode' => Yii::t('app', 'Поштовий iндекс'),
            'countryName' => Yii::t('app', 'Назва країни'),
        ];
    }
}
