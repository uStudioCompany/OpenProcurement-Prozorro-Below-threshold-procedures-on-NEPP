
<h1><?= $tender['title'] ?></h1>
<br/>


<?php

if (isset($tender['questions'])) {

    echo '<h3>'.Yii::t('app', 'Вопросы без ответов') . '</h3><br/>';

    foreach ($tender['questions'] as $q => $v) {
        if (isset($v['answer']) && $v['answer']) continue;

        if ($v['questionOf'] == 'tender') {

            echo Yii::t('app', 'Запитання до тендеру') . ' ';
            echo $v['description'] . '<br/>';

        } else if ($v['questionOf'] == 'lot') {

            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $v['relatedItem']);
            echo Yii::t('app', 'Запитання до лоту') . ' ' . $lotModel->title . ': <br/>.' . $v['description'] . '<br/><br/>';

        } else if ($v['questionOf'] == 'item') {

            $itemModel = \app\models\tenderModels\Item::getItemById($tender, $v['relatedItem']);
            echo Yii::t('app', 'Запитання до товару') . ' ' . $itemModel->title . ': <br/>.' . $v['description'] . '<br/><br/>';

        }
        echo '<br/><br/>';
    }
}

//Вимоги та скарги
if (isset($tender['complaints'])) {
    echo '<h3>'.Yii::t('app', 'Вимоги та скарги') . '</h3><br/>';
    foreach ($tender['complaints'] as $c => $complaint) {
        if ($complaint['status'] == 'draft') continue;
        if (isset($complaint['relatedLot'])) {
            $lotModel = \app\models\tenderModels\Lot::getLotById($tender, $complaint['relatedLot']);
            echo Yii::t('app', 'Жалоба к лоту') . ' ' . $lotModel->title . ': <br/>.' . $complaint['description'] . '  -- ' . $complaint['status'] . '<br/><br/>';
        } else {
            echo Yii::t('app', 'Замечание к тендеру'). ' ' . $complaint['description'] . '  -- '  . $complaint['status'] . '<br/><br/>';
        }
    }
}

//жалобы на предквалификацию
if (isset($tender['qualifications']['complaints'])) {
    echo '<h3>'.Yii::t('app', 'Скарги на предквалификацию') . '</h3><br/>';
    foreach ($tender['qualifications']['complaints'] as $c => $complaint) {
        if ($complaint['status'] == 'draft') continue;
        echo $complaint['title'] . ': <br/>.' . $complaint['description'] . ' -- ' . $complaint['status'] . '<br/><br/>';
    }
}


//жалобы на квалификацию
if (isset($tender['awards'])) {
    echo '<h3>'.Yii::t('app', 'Скарги на квалификацию') . '</h3><br/>';
    foreach ($tender['awards'] as $a => $award) {
        if (($award['status'] == 'active' || $award['status'] == 'unsuccessful') && isset($award['complaints'])) {
            foreach ($award['complaints'] as $c => $complaint) {
                if ($complaint['status'] == 'draft') continue;
                echo $complaint['title'] . ': <br/>.' . $complaint['description'] . ' -- ' . $complaint['status'] . '<br/><br/>';
            }
        }
    }
}
?>


