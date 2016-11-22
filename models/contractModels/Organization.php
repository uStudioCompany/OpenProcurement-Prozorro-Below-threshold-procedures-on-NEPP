<?php

namespace app\models\contractModels;

class Organization extends BaseModel
{
    public $name;
    public $identifier;             // class Identifier
    public $address;                // class Address
    public $additionalIdentifiers;  // array of Identifier
    public $contactPoint;           // class ContactPoint
    public $additionalContactPoints; // class AdditionalContactPoints

    public function __construct($scenario='default')
    {

        $this->contactPoint = new ContactPoint($scenario);
        $this->identifier   = new Identifier($scenario);
        $this->address      = new Address($scenario);
//        $this->additionalContactPoints = ['iClass' => AdditionalContactPoints::className()];


        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['name'], 'required', 'on'=>'limitedavards'],
            [['name'], 'string', 'max'=>100, 'on'=>'limitedavards'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'Назва органiзацiї'),
            'identifier' => \Yii::t('app', 'iдентифiкатор цiєї органiзацiї'),
            'address' => \Yii::t('app', 'Адреса юридичної особи-замовника'),
            'contactPoint' => \Yii::t('app', 'Контактi данi'),
        ];
    }
}
