<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Identifier;
use app\models\tenderModels\Address;
use app\models\tenderModels\ContactPoint;

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
        $this->additionalContactPoints = ['iClass' => AdditionalContactPoints::className()];


//        switch ($this->stage) {
//            case 'create':
//            case 'update':
//                $this->identifier   = new Identifier($scenario); // X3 ?
//                $this->address      = new Address($scenario); // X3 ?
////                $this->additionalIdentifiers = ['iClass' => Identifier::className()]; // X3 ?
//                break;
////            case 'limitedavards':
////                $this->identifier   = new Identifier([], [], $this->stage, $scenario);
////                $this->address      = new Address([], [], $this->stage);
//            case 'load':
//                break;
//        }

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['name'], 'required', 'on'=>['limitedavards', 'eu_prequalification']],
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
