<?php

namespace app\models\planModels;

class Organization extends BaseModel
{
    public $name;
    public $identifier;             // class Identifier

    public function __construct($data = [], $config = [], $stage)
    {
        $this->stage = $stage;
        $this->identifier = new Identifier($data, $config, $this->stage);
        parent::__construct($data, $config, $stage);
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'Коротка назва организацiї'),
            'identifier' => \Yii::t('app', 'iдентифiкатор цiєї органiзацiї'),
        ];
    }
}
