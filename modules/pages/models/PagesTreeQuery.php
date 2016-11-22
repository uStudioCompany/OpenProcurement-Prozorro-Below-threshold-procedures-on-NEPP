<?php
/**
 * Created by PhpStorm.
 * User: fix
 * Date: 09.11.2016
 * Time: 11:37
 */

namespace app\modules\pages\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii;

class PagesTreeQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}