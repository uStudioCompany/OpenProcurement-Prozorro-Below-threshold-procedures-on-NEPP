<?php
use yii\helpers\Html;

?>
<div class="panel panel-default plans-view">
    <div class="row">
        <div class="col-lg-9">
            <div class="panel-body"><h3><?= Html::encode($model->plan_cbd_id) ?></h3></div>
            <div class="panel-body">
                <h3><?= Html::encode(Yii::$app->formatter->asDatetime($model->date_modified)) ?></h3></div>
            <div class="panel-body"><?= Html::encode($model->description) ?></div>
        </div>
        <div class="col-md-3">
            <div class="container">
                <?php if (\yii\helpers\Json::decode($model->response)['data']['budget']['amount'] == '0') { ?>
                    <h5><?= Yii::t('app', 'Canceled') ?></h5>
                    <?php if (\app\models\Companies::checkCompanyIsPlanOwner($model->id)) { ?>
                        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/plan/view', 'id' => $key]) ?>"
                           class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                    <?php }
                } else { ?>
                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/plan/view', 'id' => $key]) ?>"
                       class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                <?php } ?>
                <br>
                <h5><?= Yii::t('app', $model->status) ?></h5>
            </div>
        </div>
    </div>
</div>
<div class='clearfix'></div>