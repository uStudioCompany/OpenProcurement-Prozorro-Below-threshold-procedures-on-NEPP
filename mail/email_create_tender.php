<h1><?= Yii::t('app', "You have created a tender") ?>.</h1>

<?php
echo $date = \Yii::$app->formatter->asDate($tenders->created_at, 'php:d F, Y ' . Yii::t('app', 'date in') . ' H:i:s ');
echo Yii::t('app', 'You have created a tender name:') . ' ' . $tenders->title . '<br>' .
    Yii::t('app', 'Tender description:') . ' ' . $tenders->description . '<br>' .
    Yii::t('app', 'Transfer Token:') . ' ' . $tenders->transfer_token;
?>

