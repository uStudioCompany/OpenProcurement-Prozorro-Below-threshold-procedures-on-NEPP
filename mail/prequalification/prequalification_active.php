<?php
use yii\helpers\Html;

echo '<h3>' . Yii::t('app', 'Тендер').' ' . $tender['title'].'</h3>';
if(isset($qualification['lotID'])){
    foreach ($tender['lots'] as $l=>$lot) {
        if($lot['id'] == $qualification['lotID']){
            echo '<h2>' . Yii::t('app', 'Лот').' ' . $lot['title'].'</h2>';
        }
    }

}
echo Yii::t('app', 'Ваша ставка успешно прошла предквалификацию.');
?>

