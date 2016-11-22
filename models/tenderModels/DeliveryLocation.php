<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class DeliveryLocation extends BaseModel
{
    public $latitude;
    public $longitude;
    public $elevation;

    public function rules()
    {
        return [
            [['latitude','longitude','elevation'], 'string', 'max'=>255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'latitude' => \Yii::t('app', 'широта'),
            'longitude' => \Yii::t('app', 'довгота'),
            'elevation' => \Yii::t('app', 'висота'),
        ];
    }
}
