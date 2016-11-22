<h1><?= $tender['title'] ?> <?= $tender['tenderID']?></h1>
<br/>
<p><?= \yii\helpers\Html::a($tender['title'] ,$link) ?></p>

<?php

if (isset($tender['questions'])) {

    echo '<h3>' . Yii::t('app', 'Ви задавали запитання до зазначеного тендеру') . '</h3><br/>';

    foreach ($tender['questions'] as $q => $v) {

        if ($v['questionOf'] == 'tender') {

            echo Yii::t('app', 'Запитання до тендеру') . ': ' . $tender['title'] . '<br/>' . Yii::t('app', 'Запитання') . ': ' . $v['title'] . '<br/>' . Yii::t('app', 'Зміст') . ': ' . $v['description'] . '<br/>';

        } else if ($v['questionOf'] == 'lot') {

            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $v['relatedItem']);
            echo Yii::t('app', 'Запитання до лоту') . ': ' . $lotModel['title'] . '<br/>' . Yii::t('app', 'Запитання') . ': ' . $v['title'] . '<br/>' .Yii::t('app', 'Зміст') . ': ' . $v['description'] . '<br/>';

        } else if ($v['questionOf'] == 'item') {

            $itemModel = \app\models\tenderModels\Item::getItemById($tender, $v['relatedItem'], 'array');
            echo Yii::t('app', 'Запитання до товару') . ': ' . $lotModel['title'] . '<br/>' . Yii::t('app', 'Зміст') . ': ' . $v['description'] . '<br/><br/>';

        }
        echo '<br/><br/>';
    }
}

//Вимоги та скарги
if (isset($tender['complaints'])) {
    echo '<h3>' . Yii::t('app', 'Ви подавали вимоги/скарги до зазначеного тендеру') . '</h3><br/>';
    foreach ($tender['complaints'] as $c => $complaint) {
        if ($complaint['status'] == 'draft') continue;
        if (isset($complaint['relatedLot']) && $complaint['relatedLot'] !='tender') {
            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $complaint['relatedLot']);
            echo  Yii::t('app', $complaint['status']) . ' ' . Yii::t('app', 'до лоту') . ' ' . $lotModel['title'] . '<br/>' . Yii::t('app', 'Зміст') . ': ' . $complaint['description'] . '<br/><br/>';
        } else {
            echo  Yii::t('app', $complaint['status']) . ' ' . Yii::t('app', 'до тендеру') . ' ' . $tender['title'] . '<br/>' . Yii::t('app', 'Зміст') . ': ' .$complaint['description'] . '<br/><br/>';
        }
    }
}
if (is_string($canceledTender)) {
    echo '<h3>' . Yii::t('app', 'Повідомляємо про скасування тендара') . ': ' . $tender['title'] . '</h3><br/>';
}
if ((isset($canceledC) || isset($canceledQ)) && !isset($canceledTender)) {
    echo '<h3>' . Yii::t('app', 'Повідомляємо про скасування') . '</h3><br/>';
    foreach ($tender['questions'] as $q => $v) {
        if (($v['questionOf'] == 'lot') && in_array($v['relatedItem'], $canceledQ)) {
            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $v['relatedItem']);
            echo Yii::t('app', 'Лоту') . ': ' . $lotModel['title'] . '<br/>' . Yii::t('app', 'Опис лоту') . ': ' . $lotModel['description'] . '<br/>';
        }
    }
    foreach ($tender['complaints'] as $q => $v) {
        if (isset($v['relatedItem']) && in_array($v['relatedItem'], $canceledC)) {
            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $v['relatedItem']);
            echo Yii::t('app', 'Лоту') . ': ' . $lotModel['title'] . '<br/>' . Yii::t('app', 'Опис лоту') . ': ' . $lotModel['description'] . '<br/>';
        }
    }

}

?>


