<?php

namespace app\modules\seller\models\BidModels;


class AdditionalContactPoints extends BaseModel
{
    public $name;
    public $name_en;
//    public $email;
    public $telephone;
    public $url;
    public $availableLanguage;

//    public function __construct($data = [], $config = [], $stage, $scenario)
//    {
//        $this->scenario = $scenario;
//    }

    public function rules()
    {
        return [
            [['name','name_en', 'telephone','url','availableLanguage'], 'required'],
            [['name','name_en', 'telephone','url','availableLanguage'], 'string', 'max'=>100],
//            [['email'], 'email'],
            [['url'], 'url'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', "ФiО контактної особи"),
            'name_en' => Yii::t('app', "iм'я контактної особи"),
//            'email' => 'Адреса електронної пошти ',
            'telephone' => Yii::t('app', 'Номер телефону'),
            'url' => Yii::t('app', 'Веб адреса'),
            'availableLanguage'=> Yii::t('app', 'Мова спiлкування')
        ];
    }
}
