<?php
use yii\helpers\Html;
?>
<div class="panel panel-default">
    <div class="row">
        <div class="col-lg-9">
            <div class="panel-body"><h3><?= Html::encode($model->contract_cbd_id) ?></h3></div>
            <div class="panel-body"><h3><?= Html::encode($model->title) ?></h3></div>
            <div class="panel-body"><?= Html::encode($model->description) ?></div>
        </div>
        <div class="col-md-3">
            <div class="container">
                <p></p>
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/'.Yii::$app->session->get('businesType').'/contracting/view', 'id' => $key]) ?>"
                   class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                <br>
                <h5><?=Yii::t('app', 'contract_'.$model->status) ?></h5>
            </div>
        </div>
    </div>
</div>
<div class='clearfix'></div>