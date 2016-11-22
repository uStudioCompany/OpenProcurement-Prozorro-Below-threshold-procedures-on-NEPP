<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;

class Enum extends BaseModel
{
    public $value;
    public $title;             
    public $title_en;
    public $description;

    public function rules()
    {
        $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
        return [
            [['title', 'value'], 'required', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['title_en'], 'required','whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible"); }', 'when'=>function($model) {return isset($post['tender_method']) ? $post['tender_method'] == 'open_aboveThresholdEU' : false;}],
            ['value','integer'],
            ['value', 'compare', 'compareValue' => 0, 'operator' => '>='],
            ['value',\app\validators\FeaturesCostValidator::className()],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'value' => \Yii::t('app', 'Значення критерiю'),
            'title' => \Yii::t('app', 'Назва значення'),
            'title_en' => \Yii::t('app', 'Option name en'),
            'description' => \Yii::t('app', 'Опис значення'),

        ];
    }
}
