<?php

namespace app\models\tenderModels;


class Identifier extends BaseModel
{
    public $scheme;
    public $id;             
    public $legalName; 
    public $uri;

    public function rules()
    {
        return [
            [['scheme'], 'safe'],
            [['id'], 'safe'],
            [['legalName'], 'safe'],
            [['uri'], 'safe'],

            [['scheme','id'],'required', 'on' => 'limitedavards' ],
            [['uri','legalName'],'string', 'on' => 'limitedavards' ],
            [['scheme','id', 'legalName', 'uri'],'string', 'max'=>100,'on' => 'limitedavards' ],
            [['id'], 'integer', 'on' => 'limitedavards'],
            [['id'], 'string', 'length'=>[8,10], 'on' => 'limitedavards'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scheme' => \Yii::t('app', 'Схема iдентифiкацiї Органiзацiй'),
            'id' => \Yii::t('app', 'iдентифiкатор органiзацiї'),
            'legalName' => \Yii::t('app', 'Легально зареєстрована назва органiзацiї'),
            'uri' => \Yii::t('app', 'URI для iдентифiкацiї органiзацiї'),
        ];
    }
}
