<?php

namespace app\models\planModels;


class Identifier extends BaseModel
{
    public $scheme;
    public $id;             
    public $legalName; 

    public function rules()
    {
        return [
            [['scheme'], 'safe'],
            [['id'], 'safe'],
            [['legalName'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scheme' => \Yii::t('app', 'Схема iдентифiкацiї Органiзацiй'),
            'id' => \Yii::t('app', 'iдентифiкатор органiзацiї'),
            'legalName' => \Yii::t('app', 'Легально зареєстрована назва органiзацiї'),
        ];
    }
}
