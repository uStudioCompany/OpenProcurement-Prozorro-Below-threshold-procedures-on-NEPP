<?php

namespace app\models\tenderModels;


class Qualifications extends BaseModel
{
    public $id;
    public $title;
    public $description;
    public $qualified;
    public $eligible;
    public $status;
    public $cause;
    public $bidID;
    public $lotID;
    public $complaints;
    public $documents;

    public function __construct($scenario='default')
    {
        $this->complaints      = ['iClass' => Complaint::className()];
        $this->documents       = ['iClass' => Document::className()];

        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            [['id','status','bidID', 'lotID'], 'safe'],
            [['qualified','eligible'], 'required', 'on'=>'eu_prequalification', 'requiredValue' => 1, 'message' =>'Необхiдно пiдтвердити', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['title','description'], 'required', 'on'=>'eu_prequalification', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }'],
            [['cause'], 'required', 'on'=>'eu_prequalification', 'message' =>'Необхiдно визначити','whenClient' => 'function (attribute, value) { return $(\'.cause\').is(":visible"); }'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('app', 'Назва'),
            'description' => \Yii::t('app', 'Причина дисквалiфiкацii'),
            'qualified' => \Yii::t('app', 'вiдповiдає квалiфiкацiйним критерiям, встановленим замовником в тендернiй документацiї'),
            'eligible' => \Yii::t('app', 'вiдсутнi пiдстави для вiдмови в участi згiдно  ст. 17 Закону України ”Про Публiчнi закупiвлi”'),
            'cause' => \Yii::t('app', 'Причина дисквалiфiкацii'),
            'complaints' => \Yii::t('app', 'Скарги'),
        ];
    }


    public static function getQualificationById($tender, $id){
        foreach ($tender->qualifications as $k => $qualification) {
            if ($qualification->id == $id) {
                return $qualification;
            }
        }
    }
}
