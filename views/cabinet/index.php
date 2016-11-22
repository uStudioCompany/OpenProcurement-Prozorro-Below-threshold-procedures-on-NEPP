<?php

use yii\helpers\Html;
use app\models\Companies;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PersonsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$module_name = Yii::$app->controller->module->id;

//$this->title = Yii::t('app', 'Cabinet_'.$module_name);
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (Yii::$app->session->hasFlash('register_success')) { ?>
    <div class="bs-example">
        <div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('register_success'); ?>
        </div>
    </div>
<?php } ?>

<?php
if (Yii::$app->session->hasFlash('Forbidden')) {
    echo $this->render('../common/flash_fail', [
        'data' => Yii::$app->session->getFlash('Forbidden'),
    ]);
}
echo Html::checkbox('auction-mode', \app\components\HTender::checkMode(), [
    'onclick' => 'SetAuctionMode(this)',
    'label' => Yii::t('app', 'Тестовий режим')
]);
?>


<div class="persons-index">

    <h1><?= Html::encode($this->title) ?></h1>

</div>
<?php
echo $this->render('_modalEvents');
$this->registerJsFile(Url::to('@web/js/nav_block.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile(Url::to('@web/js/cabinet.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
?>


