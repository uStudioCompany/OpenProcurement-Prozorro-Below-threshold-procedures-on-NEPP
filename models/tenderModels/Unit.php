<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class Unit extends BaseModel
{
    public $code;
    public $name;             

    public function rules()
    {
        return [
            [['code'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['name'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => \Yii::t('app', 'Код одиницi'),
            'name' => \Yii::t('app', 'Назва одиницi'),
        ];
    }
}
