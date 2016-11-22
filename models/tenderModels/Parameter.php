<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class Parameter extends BaseModel
{
    public $code;
    public $value;             

    public function rules()
    {
        return [
            [['value'], 'safe'],
            [['code'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'value' => \Yii::t('app', 'Значення критерiю'),
            'code' => \Yii::t('app', 'Код критерiю'),
        ];
    }
}
