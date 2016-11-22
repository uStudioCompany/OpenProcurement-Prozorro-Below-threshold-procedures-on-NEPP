<?php

namespace app\models;

use yii\base\Model;
use yii\validators\Validator;
use Yii;
use yii\helpers\VarDumper;
use app\components\SimpleTenderModel;

class CustomValidator extends Model
{

    public function __set($name, $value)
    {
        return $this->$name = $value;
    }
}