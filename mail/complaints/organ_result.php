<?php
use yii\helpers\Html;

echo '<h3>' . Yii::t('app', 'Тендер') .' '. $tender['title'].' - '. $tender['id'].'</h3>';
if(isset($complaint['lotID'])){
    foreach ($tender['lots'] as $l=>$lot) {
        if($lot['id'] == $complaint['lotID']){
            echo '<h2>' . Yii::t('app', 'Лот').' ' . $lot['title'].'</h2>';
        }
    }

}

echo Yii::t('app', 'Органом оскарження изменен статус Вашей жалобы').' - '.$complaint['title'] .' - '. $complaint['status'],'<br/>';
?>

