<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class DeliveryAddress extends BaseModel
{
    public $countryName;
    public $region;
    public $locality;
    public $streetAddress;
    public $postalCode;

    public function rules()
    {
        return [
            [['countryName'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>\Yii::t('app','Будь ласка, введіть країну адреси доставки')],
            [['region',], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }','message'=>\Yii::t('app','Будь ласка, введіть область адреси доставки')],
            [['locality'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }','message'=>\Yii::t('app','Будь ласка, введіть населений пункт адреси доставки')],
            [['streetAddress'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }','message'=>\Yii::t('app','Будь ласка, введіть вулицю адреси доставки')],
            [['postalCode'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }','message'=>\Yii::t('app','Будь ласка, введіть поштовий індекс адреси доставки')],

            [['countryName','region','locality', 'streetAddress'], 'string', 'max'=>255],
            [['postalCode'], 'integer','message'=>\Yii::t('app','Будь ласка, введіть коректний поштовий індекс (допустимі символи [0-9])')],
            [['postalCode'], 'string', 'min' => 5, 'max' => 5, 'message'=>\Yii::t('app','Будь ласка, введіть коректний поштовий індекс (повинен складатися з 5 цифр)')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'streetAddress' => \Yii::t('app', 'Вулиця'),
            'locality' => \Yii::t('app', 'Населений пункт'),
            'region' => \Yii::t('app', 'Регіон / Область'),
            'postalCode' => \Yii::t('app', 'Поштовий iндекс'),
            'countryName' => \Yii::t('app', 'Назва країни'),
        ];
    }
}
