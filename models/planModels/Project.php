<?php

namespace app\models\planModels;

class Project extends BaseModel
{
    public $id;
    public $name;

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['name'], 'string'],
            [['id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'Назва проекту'),
        ];
    }
}
