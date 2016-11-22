<?php

namespace app\models\contractModels;


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

            [['fio','name','name_en','availableLanguage', 'email', 'telephone'], 'safe',],
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
