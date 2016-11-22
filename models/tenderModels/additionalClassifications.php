<?php

namespace app\models\tenderModels;

class AdditionalClassifications extends BaseModel
{
    public $scheme = 'ДКПП';
    public $id;             
    public $description;
    public $uri;
    public $dkType;

    public function rules()
    {
        return [
            [['scheme'], 'safe'],
            [['id'], 'safe'],
            [['description'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'message'=>'Необхiдно заповнити "Класифiкацiя згiдно ДК"','message'=>\Yii::t('app','Будь ласка, виберіть додаткову класiфікацію предмета закупівлі')],
            [['description'], 'string'],
            [['uri'], 'safe'],
            [['dkType'], 'string', 'max'=>20],
        ];    
    }

    public function attributeLabels()
    {
        return [
            'scheme' => \Yii::t('app', 'Схема Класифiкацiї Елементiв'),
            'id' => \Yii::t('app', 'Код класифiкацiї'),
            'description' =>  \Yii::t('app', 'Додаткова класифікація за ') . $this->scheme,
            'uri' => \Yii::t('app', 'URI для iдентифiкацiї коду'),
        ];
    }
}