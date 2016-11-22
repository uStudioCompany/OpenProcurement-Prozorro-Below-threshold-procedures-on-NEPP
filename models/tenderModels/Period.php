<?php

namespace app\models\tenderModels;

use Yii;

class Period extends BaseModel
{
    public $startDate;  // Формат даты: ISO 8601.
    public $endDate;

//    public function __construct($scenario='default')
//    {
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['startDate'], 'required',
                'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible") && !$(attribute.input).is(":disabled"); }',
                'when' => function ($model) {
                    $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
                    return !in_array($post['tender_method'], Yii::$app->params['2stage.tender']);
                },
                'except' => 'limitedavards','message'=>'Будь ласка, вкажіть дату початку перiоду, коли подаються пропозицiї'],
            [['endDate'], 'required','whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'except' => 'limitedavards','message'=>'Будь ласка, вкажіть дату кiнця перiоду, коли подаються пропозицiї"'],
            [['startDate', 'endDate'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i', 'except' => 'limitedavards'],

            [['startDate'], \app\validators\PeriodStartValidator::className(), 'except' => 'limitedavards'],
            [['endDate'], \app\validators\PeriodEndValidator::className(), 'except' => 'limitedavards'],


//            [['endDate'], \app\validators\OpenUaPeriodValidator::className(), 'except' => 'limitedavards'],
//            [['endDate'], \app\validators\OpenEuPeriodValidator::className(), 'on' => 'default'],
//            [['endDate'], \app\validators\OpenUaDefencePeriodValidator::className(), 'on' => 'default'],

//            [['startDate', 'endDate'], 'safe', 'on' => 'limitedavards'],

            [['startDate'], 'safe', 'on' => 'open'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => \Yii::t('app','Дата початку перiоду, коли подаються пропозицiї'),
            'endDate' => \Yii::t('app','Дата кiнця перiоду, коли подаються пропозицiї'),
        ];
    }
}
