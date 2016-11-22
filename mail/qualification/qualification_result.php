<?php
use yii\helpers\Html;

echo '<h3>' . Yii::t('app', 'Тендер'). ' ' . $tender['title'].' - '. $tender['id'].'</h3>';
if(isset($award['lotID'])){
    foreach ($tender['lots'] as $l=>$lot) {
        if($lot['id'] == $award['lotID']){
            echo '<h2>' . Yii::t('app', 'Лот'). ' ' . $lot['title'].'</h2>';
        }
    }
}

echo Yii::t('app', 'Результат калификации'). ' - '. $award['status'],'<br/>';
echo Yii::$app->VarDumper->dump($award['suppliers'], 10, true);
?>
