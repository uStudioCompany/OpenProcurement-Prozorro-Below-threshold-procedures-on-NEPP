<?
/**
 *
 * @var string $link
 * @var string $cbdId
 * @var string $type
 * @var array  $date
 */

?>

<h3><?= Yii::t('app','Hello'); ?></h3>

<p><?= Yii::t('app','tender_auction_info_'.$type, ['date'=>$date['date'],'time'=>$date['time']]); ?></p>

<p><?= \yii\helpers\Html::a($link,$link) ?></p>

<p><?= Yii::t('app','need_auth'); ?></p>




