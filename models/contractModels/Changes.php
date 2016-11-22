<?php

namespace app\models\contractModels;

use app\validators\ContractingPeriodValidator;

class Changes extends BaseModel
{
    public $id;
    public $rationale;
    public $rationaleTypes;
    public $date;
    public $contractNumber;
    public $status;
    public $dateSigned;

//    public function __construct($data = [], $config = [], $stage, $scenario)
//    {
//
//        //$this->rationaleTypes = [];// new RationaleTypes($scenario);
//        parent::__construct($scenario);
//    }

    public function rules()
    {
        return [

            [['id', 'status', 'date'], 'safe',],
            [['rationaleTypes','dateSigned'], 'required',],
            [['rationale', 'contractNumber'], 'required',],
            [['dateSigned'], 'match', 'pattern' => '/^\d{2}\/\d{2}\/\d{4} *?\d{0,2}[:]{0,1}\d{0,2}$/i'],
            [['dateSigned'], ContractingPeriodValidator::className()],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', "iм'я контактної особи"),
            'rationale' => \Yii::t('app', "Опис причини змін договору"),
            'rationaleTypes' => \Yii::t('app', "Причини додання змін до договору"),
            'date' => \Yii::t('app', 'Адреса електронної пошти '),
            'contractNumber' => \Yii::t('app', 'Номер додаткової угоди'),
            'status' => \Yii::t('app', 'Номер факсу'),
            'dateSigned' => \Yii::t('app', 'Дата змiни'),
        ];
    }

    public static function getChangesById($contract, $changeId)
    {

        foreach ($contract->changes as $k => $change) {
//            \Yii::$app->VarDumper->dump($changeId, 10, true, true);
            if ($change->id == $changeId) {
                return $change;
            }
        }
    }

    public static function isAllActive($changes)
    {
        if(count($changes) == 0) return false;
        foreach ($changes as $k => $change) {
            if ($change->status == 'pending') {
                return false;
            }
        }
        return true;
    }
}
