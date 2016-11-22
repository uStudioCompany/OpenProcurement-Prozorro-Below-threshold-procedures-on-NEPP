<?php

namespace app\components;

use yii\helpers\BaseVarDumper;
use Yii;

/**
 * Exception represents a generic exception for all purposes.
 *
 */
class MyVarDumper extends BaseVarDumper
{
    public static function dump($var, $depth = 10, $highlight = false, $die= false)
    {
        if (in_array(Yii::$app->request->getUserIP(), ['91.225.165.4', '94.154.232.2', '127.0.0.1'])) {
            echo static::dumpAsString($var, $depth, $highlight);
            if($die){
                die;
            }
        }

    }
}
