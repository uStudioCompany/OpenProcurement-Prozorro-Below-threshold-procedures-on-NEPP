<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Enum;

class Feature extends BaseModel
{
    public $code;
    public $featureOf;             
    public $relatedItem;
    public $title;
    public $title_en;
    public $description;
    public $description_en;
    public $enum;           // array of Enum

    public function __construct($scenario = 'default')
    {
        $this->enum = ['iClass' => Enum::className()];
        parent::__construct($scenario);
    }

    public function rules()
    {
        $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
        return [
            [['title'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['title_en'], 'required','whenClient' => 'function (attribute, value) { return $(".tender_method_select").val() != "open_aboveThresholdUA.defense" && $(attribute.input).is(":visible"); }', 'when'=>function($model) {return isset($post['tender_method']) ? $post['tender_method'] == 'open_aboveThresholdEU' : false;}],
            [['code'], 'safe'],
            [['featureOf'], 'safe'],
            [['relatedItem'], 'safe'],
            [['description', 'description_en'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => \Yii::t('app','Код нецiнового критерiю'),
            'relatedItem' => \Yii::t('app','Сфера застосування показника'),
            'title' => \Yii::t('app','Назва показника'),
            'description' => \Yii::t('app','Пiдказка для користувача'),
            'title_en' => \Yii::t('app','Feature title_en'),
            'description_en' => \Yii::t('app','Feature description_en'),
        ];
    }

    public static function getFeatureTypes()
    {
        return
            [
                'tender' => \Yii::t('app','Оголошення'),
                'lot' => \Yii::t('app','Частина (лот) предмету закупівлі'),
                'item' => \Yii::t('app','Окрема частина або частина предмета закупівлі (лота)'),
            ];
    }
}
