<?php


if (isset($post['Question'])) {
?>
    <?php if($messageTo == 'seller'){?>
        <h1><?= Yii::t('app', 'The question by tender №') . ': ' . $tenderData['tenderId'] . '<br>' . $tenderData['title'] . '<br>' . $titleSelect ?></h1>
    <?php } else{?>
        <h1><?= Yii::t('app', 'The tender №') . ': ' . $tenderData['tenderId'] . '<br>' . $tenderData['title'] . '<br>' . $titleSelect . '<br>' . Yii::t('app', 'was asked.') ?></h1>
    <?php }?>
<?php

    echo '<b>Заголовок запитання:</b> ' . $post['Question']['title'] . '<br><br>';
    echo '<b>Зміст запитання:</b> ' . $post['Question']['description'];
}

//Вимоги та скарги
if (isset($post['Complaint'])) {
?>
    <?php if($messageTo == 'seller'){?>
        <h1><?= Yii::t('app', 'Відправлена ' . mb_strtolower($tenderData['messageStatus']) . " за тендером №").$tenderData['tenderId'] . '<br>' . $tenderData['title'] . '<br>' . $titleSelect ?></h1>
    <?php } else{?>
        <h1><?= Yii::t('app', 'Отримано ' . $tenderData['messageStatusPad']. " за тендером №").$tender->tenderID . '<br>' . $tenderData['title'] . '<br>' . $titleSelect ?></h1>
    <?php }?>
<?php

    echo '<b>Заголовок:</b> ' . $post['Complaint']['title'] . '<br><br>';
    echo '<b>Зміст:</b> ' . $post['Complaint']['description'];
}
?>


