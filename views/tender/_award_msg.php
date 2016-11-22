<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $tender app\models\tenderModels\Tender
 * @var $tendersId int
 * @var $tenders app\models\Tenders
 * @var $type string
 * @var $award array - Award
 * @var $status string
 * @var $bid app\models\tenderModels\Bid
 */

$tender = json_decode($tenders->response,1);
$msg = '';
if ($type === 'unsuccessful')
    $msg = sprintf(
        Yii::t('app','Unsuccessful Award Sucses'),
        $award['suppliers'][0]['name'],
        $tender['data']['tenderID']);

if ($type === 'active')
    $msg = sprintf(
        Yii::t('app','Active Award Sucses'),
        $award['suppliers'][0]['name'],
        $tender['data']['tenderID'],
        $tender['data']['tenderID']);

if ($type === 'cancelled')
    $msg = sprintf(
        Yii::t('app','Cancelled Award Sucses'),
        $award['suppliers'][0]['name'],
        $tender['data']['tenderID']) .
        '<script>setTimeout(\'document.location.reload()\',3000);</script>';

echo $msg;
?>
<!--<pre>--><?// print_r($award) ?><!--</pre>-->
