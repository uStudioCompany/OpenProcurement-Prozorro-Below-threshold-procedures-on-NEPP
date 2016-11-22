<?php

namespace app\models\tenderModels;


class ContactPoint extends BaseModel
{
    public $fio;
    public $name;
    public $name_en;
    public $availableLanguage;
//    public $userSurname;
//    public $userPatronymic;
    public $email;
    public $telephone;
    public $faxNumber;
    public $url;

//    public function __construct($data = [], $config = [], $stage, $scenario)
//    {
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['name'], 'required', 'when' => function ($model) {
                return $model->fio === '';
            },
                'whenClient' => "function (attribute, value) {
                    return $('.contact_person option:selected').val() == '' || $('.contact_person').length == 0;
                }",
                'message'=>'Будь ласка, введіть iм`я контактної особи'],

            [['email'], 'required', 'when' => function ($model) {
                return $model->fio === '';
            },
                'whenClient' => "function (attribute, value) {
                    return $('.contact_person option:selected').val() == '' || $('.contact_person').length == 0;
                }",
                'message'=>'Будь ласка, введіть електронну скриньку'],

            [['telephone'], 'required', 'when' => function ($model) {
                return $model->fio === '';
            },
                'whenClient' => "function (attribute, value) {
                    return $('.contact_person option:selected').val() == '' || $('.contact_person').length == 0;
                }",
                'message'=>'Будь ласка, введіть номер телефону'],


            //европейская процедура
            [['name_en', 'availableLanguage'], 'required', 'when' => function ($model) {
                $post = is_a(\Yii::$app, 'yii\console\Application') ? [] : \Yii::$app->request->post();
//                return $model->fio === '' && $post['procurementMethod']['method_type'] == 'aboveThresholdEU';
                return $model->fio === '' && $post['tender_method'] == 'open_aboveThresholdEU';
            },
                'whenClient' => "function (attribute, value) {
                    return ($('.contact_person option:selected').val() == '' || $('.contact_person').length == 0) && $('.tender_method_select').val() == 'open_aboveThresholdEU';
                }"],

            ['fio', 'string', 'max'=>100],


            [['name',], 'required', 'on'=>['limitedavards', 'eu_prequalification'] ,'message'=>'Будь ласка, введіть iм`я контактної особи'],
            [['email'], 'required', 'on'=>'limitedavards','message'=>'Будь ласка, введіть електронну скриньку'],
            [['telephone'], 'required', 'on'=>'limitedavards','message'=>'Будь ласка, введіть номер телефону'],
            [['name', 'email', 'telephone'], 'string', 'max'=>100, 'on'=>'limitedavards'],
            [['email'], 'email','message'=>'Будь ласка, введіть коректну електронну скриньку'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fio' => \Yii::t('app', "iм'я контактної особи"),
            'name' => \Yii::t('app', "ПiБ контактної особи"),
            'name_en' => \Yii::t('app', "Contact name"),
            'email' => \Yii::t('app', 'Адреса електронної пошти '),
            'telephone' => \Yii::t('app', 'Номер телефону'),
            'faxNumber' => \Yii::t('app', 'Номер факсу'),
            'url' => \Yii::t('app', 'Веб адреса'),
            'availableLanguage' => \Yii::t('app', 'availableLanguage'),
        ];
    }
}
