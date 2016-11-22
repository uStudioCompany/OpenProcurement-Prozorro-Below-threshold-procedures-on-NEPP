<?php

namespace app\models\tenderModels;

use Yii;

class Classification extends BaseModel
{
    public $scheme = 'CPV';
    public $id;             
    public $description;
    public $uri;


    public function rules()
    {
        return [
            [['id', 'scheme'], 'safe'],
            [['description'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }','message'=>\Yii::t('app','Будь ласка, виберіть початкову класiфікацію предмета закупівлі')],
            [['description'], \app\validators\CpvValidator::className()],
            [['uri'], 'safe'],
        ];    
    }

    public function attributeLabels()
    {
        return [
            'scheme' => Yii::t('app','Схема Класифiкацiї Елементiв'),
            'id' => Yii::t('app','Код класифiкацiї'),
            'description' => Yii::t('app','Класифiкацiя згiдно ' . $this->scheme),
            'uri' => Yii::t('app','URI для iдентифiкацiї коду'),
        ];
    }
}
