<?php
namespace app\models\planModels;

use Yii;

class AdditionalClassification extends BaseModel
{
    public $scheme;
    public $id;             
    public $description;
    public $dkType;

    public function rules()
    {
        return [
            [['id','description','scheme'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'when'=>'return false'],
            [['id','scheme','description'], 'safe'],
            [['dkType'], 'string', 'max'=>30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'scheme' => \Yii::t('app', 'Схема Класифiкацiї Елементiв'),
            'id' => \Yii::t('app', 'Код класифiкацiї'),
            'description' => \Yii::t('app', 'Код ') . $this->scheme,
            'dkType' => \Yii::t('app', 'dkType'),
        ];
    }
}
