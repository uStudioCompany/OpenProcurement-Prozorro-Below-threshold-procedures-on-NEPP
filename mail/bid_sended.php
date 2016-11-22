<?= $tender['data']['title']; ?> - <?= $tender['data']['description']; ?>
<br/>
<?php
if($action == 'create'){
    echo Yii::t('app', 'Пропозицiю створено ');
}elseif($action == 'update'){
    echo Yii::t('app', 'Пропозицiю вiдредаговано ');
}
?>

<br/>
<?php echo $data['value']['amount'] . ' ' . $data['value']['currency']; ?>
<br/>
<?php echo 'id - ' . $data['id'] ?>
<br/>
<?php echo Yii::t('app', 'Дата') . ' - ' . Yii::$app->formatter->asDatetime($data['date']) ?>
<br/>
Посилання на аукціон буде доступне в день аукціону прямо на сторінці оголошення.
<?php
echo \yii\helpers\Url::toRoute('/seller/tender/view/' . $tenders->id, true);
?>
