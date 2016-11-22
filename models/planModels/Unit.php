<?php

namespace app\models\planModels;

class Unit extends BaseModel
{
    public $code;
    public $name;             

    public function rules()
    {
        return [
            [['code'], 'required'],
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
