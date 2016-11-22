<?php

namespace app\modules\seller\models\BidModels;

use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use app\components\HTender;

class BaseModel extends Model
{
    protected $no_empty_fields = [];

    public function __construct($scenario='default')
    {
        $this->scenario = $scenario;
        //$this->setScenario($scenario);

        //print_r($scenario); die();
//        if (!empty($data)) {
//            $this->load($data);
//        } else {
//            foreach ($this->attributes as $field => $value) {
//
//                if (is_array($value)) {
//                    switch ($this->stage) {
//                        case 'create':
//                        case 'update':
//
//                            // Создаем пустые заготовки для формы
//                            // Пустой лот, пустой итем, прочие поля...
//                            if ($this->isNeedEmpty($value['iClass'])) {
//                                $value[] = new $value['iClass']([], [], $this->stage, $scenario); }
//
//                            if ($this->stage === 'create') {
//                                unset($value['iClass']); }
//
//                            $this->setAttributes([$field => $value], false);
//                            break;
//
//                        case 'load':
//                            break;
//                    }
//                }
//            }
//        }
        parent::__construct();
    }

//    private function isNeedEmpty($name) {
////        foreach ($this->no_empty_fields AS $key) {
////            if (isset($arr[$key])) {
////                return false; }
////        }
//        if (in_array($name,$this->no_empty_fields)) {
//            return false;
//        }
//
//        return true;
//    }

//    public function getStage() {
//        //return $this->stage;
//    }

    public function rules()
    {
        // Здесь нельзя описывать сложные аттрибуты, такие как объекты и массивы объектов
        return [];
    }

    public function validate($attributeNames = NULL, $clearErrors = true)
    {
        $validated = parent::validate();
        foreach ($this->attributes as $field => $value) {

            if (is_array($value)) {
                foreach ($value as $k => $val) {
                    if (is_int($k) && is_object($val)) {
                        $validated = $validated && $val->validate();
                        $validated = $validated && !$val->hasErrors();
                    }
                }
            }

            if (is_object($value)) {
                $value->validate();
                $validated = $validated && !$value->hasErrors();
            }
        }
        return $validated;
    }

    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {

            if ($formName !== null) {
                $data = $data[$formName];
            } else if (isset($data[$this->formName()])) {
                $data = $data[$this->formName()];
            }

            foreach ($this->attributes as $field => $value) {
                if (isset($data[$field])) {
                    if (is_object($value)) {
                        $value->load($data, $field);
                    }

                    if (is_array($value)) {
                        foreach ($data[$field] as $key => $objData) {
                            $obj = new $value['iClass']($this->scenario);
                            $obj->load($data[$field], $key);
                            $value[$key] = $obj;
                        }
                    }
                }

                if (is_array($value)) {
                    //unset($value['iClass']);
                }

                $this->setAttributes([$field => $value], false);
            }
            return true;
        }
        //die('aaaaaaaaaaa');
        return false;
    }

    public function fill(&$param,$pName=false) {

        if (!isset($param['iClass'])) { //echo '<pre>'; print_r($param); die(); die('ssssssssssss');
            return;
        }

        $tmp = new $param['iClass']($this->scenario);
        if (is_object($tmp)) {
            foreach ($tmp AS $key=>$val) {
                if (is_array($val)) {
                    $tmp->fill($tmp->$key);
                }
            }
        }

        if ($pName) {
            $param[$pName] = $tmp;
        } else {
            $param[] = $tmp;
        }

        //unset($param['iClass']);
    }
}
