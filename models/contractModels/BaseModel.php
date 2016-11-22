<?php

namespace app\models\contractModels;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class BaseModel extends Model
{
    // базовый класс для работы со структурами данных тендера
    protected $stage;

    public function __construct($data = [], $config = [], $stage='update')
    {
        $this->stage = $stage;
        if (!empty($data)) {
            $this->load($data);
        } else {
            foreach ($this->attributes as $field => $value) {
                if (is_array($value)) {
                    $value[] = new $value['iClass']($data, $config, $this->stage);
                    $this->setAttributes([$field => $value], false);
                }
            }
        }

        parent::__construct($config);
    }

    public function getStage() {
        return $this->stage;
    }
    public function boo()
    {
        echo '<br /> boo -> ' . $this->className();
    }

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
                    if ($k !== 'iClass' && $k !== '__EMPTY_ITEM__') {
                        if (is_object($val)) {
                            $val->validate();
                            $validated = $validated && !$val->hasErrors();
                        }
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
//        Yii::$app->VarDumper->dump($formName, 10, true);die;
        if (parent::load($data, $formName)) {

            if ($formName !== null) {
                $data = $data[$formName];
            }
            foreach ($this->attributes as $field => $value) {
                if (isset($data[$field])) {
                    if (is_object($value)) {
                        $value->load($data, $field);
                    }
                    if (ArrayHelper::isAssociative($value)) {
                        foreach ($data[$field] as $key => $objData) {
                            $obj = new $value['iClass']([],[],$this->stage);
                            $obj->load($data[$field], $key);
                            $value[$key] = $obj;
                        }
                    }
                    $this->setAttributes([$field => $value], false);
                }
            }
            return true;
        }
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
