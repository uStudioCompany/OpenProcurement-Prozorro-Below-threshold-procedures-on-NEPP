<?php
use yii\helpers\Html;

?>
<div class="panel panel-default">
    <div class="row">
        <div class="col-lg-9">
            <div class="panel-body"><h3><?= Html::encode($model->tender_cbd_id) ?></h3></div>
            <div class="panel-body"><h3><?= Html::encode($model->title) ?></h3></div>
            <div class="panel-body"><?= Html::encode($model->description) ?></div>
        </div>
        <div class="col-md-3">
            <div class="container">
                <p></p>
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/'.Yii::$app->session->get('businesType').'/tender/view', 'id' => $key]) ?>"
                   class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                <h5>
<!--                    --><?php
//                    $date = \app\models\Tenders::getTenderEnquiryEndDate($key);
//                    if(strtotime($date) > strtotime('now')  ){
//                        echo Yii::t('app', 'EnquiryEnd');
//                        echo $date;
//                    }
//                    ?>

                </h5>
                <h5><?=Yii::t('app', 'tender_'.$model->status)  ?></h5>
                <h5><?=Yii::t('app', $model->tender_method)  ?></h5>
            </div>
        </div>
    </div>
</div>
<div class='clearfix'></div>