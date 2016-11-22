<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\grid\GridView;

/**
 * User: Nik
 * Date: 29.01.2016
 * Time: 13:10
 *
 * @var $form yii\widgets\ActiveForm
 * @var $tender app\models\tenderModels\Tender
 * @var $tendersId int
 * @var $tenders app\models\Tenders
 * @var $company app\models\Companies
 *
 */

//$this->title = \app\models\Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $tender->attributeLabels();
?>
<div class="tender-preview m_table-wrap">
    <input type="hidden" id="current_locale" value="<?=substr(Yii::$app->language, 0, 2)?>">
<?php

echo $this->render('/site/head', [
    'title' => $this->title,
    'descr' => 'Квалiфiкацiя учасникiв'
]);
?>
    <div class="row">
        <div class="col-md-9">
    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example"><div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?></div></div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('award_complaint_send')) { ?>
        <div class="bs-example"><div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('award_complaint_send'); ?></div></div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message_error')) { ?>
        <div class="bs-example"><div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_error'); ?></div></div>
    <?php } ?>

    <div class="info-block">
        <h4 class="hidden"><?=Yii::t('app','Закупiвля')?></h4>

        <div class="row m_lead">
            <div class="col-md-3">TenderID</div>
            <div class="col-md-9"><b><?=htmlspecialchars(@$tender->tenderID)?></b></div>
        </div><div class="row m_lead">
            <div class="col-md-3">ID</div>
            <div class="col-md-9"><b><?=htmlspecialchars(@$tender->id)?></b></div>
        </div>

        <hr />

        <input type="hidden" id="tenders_id" value="<?=$tendersId?>" name="Tenders[id]">
        <input type="hidden" id="tenders_method" value="<?=$tenders->tender_method ?>">

        <h4><?=Yii::t('app','Загальна iнформацiя про закупiвлю')?></h4>

        <div class="row">
            <div class="col-md-3"><?=Yii::t('app','Загальна назва закупiвлi')?></div>
            <div class="col-md-9"><b><?=htmlspecialchars(@$tender->title)?></b></div>
        </div><div class="row">
            <div class="col-md-3"><?=Yii::t('app','Описова частина')?></div>
            <div class="col-md-9"><b><?=htmlspecialchars(@$tender->description)?></b></div>
        </div><div class="row">
            <div class="col-md-3"><?=Yii::t('app','Загальний бюджет закупiвлi')?></div>
            <div class="col-md-9"><b><?=htmlspecialchars(@$tender->value->amount) .' '. Yii::t('app',@$tender->value->currency) .' '. (@$tender->value->valueAddedTaxIncluded ? Yii::t('app','З ПДВ') : Yii::t('app','Без ПДВ')) ?></b></div>
        </div>

        <hr />

        <div class="info-block">
            <h4><?=Yii::t('app','Квалiфiкацiя учасникiв')?></h4>
            <?
            if ($tenders->tender_type === 1) {

                echo $this->render('_award_lot', [
                    'k' => 0,
                    'lot' => null,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'title' => $tender->title,
                    'lot_status' => $tender->status,
                    'description' => $tender->description,
                    'tendersId' => $tendersId]);

            } else if ($tenders->tender_type === 2) {

                foreach ($tender->lots AS $key=>$lot) {
                    if($lot->status == 'unsuccessful'){
                        echo '<h3>'.Yii::t('app','Торги за лотом: {title} не вiдбулися',['title'=>$lot->title]).'</h3>';
                        continue;
                    }
                    //@TODO: Добавить проверку, что есть ставки на лот и авард
                    if ($key === 'iClass' || $key === '__EMPTY_LOT__') continue;

                    echo $this->render('_award_lot', [
                        'k' => $key,
                        'lot' => $lot,
                        'tender' => $tender,
                        'tenders' => $tenders,
                        'title' => $lot->title,
                        'lot_status' => $lot->status,
                        'description' => $lot->description,
                        'tendersId' => $tendersId,]);

                }


            } ?>
        </div>

    </div>
        </div>
        <div class="col-md-3">
            <?= $this->render('view/_nav_block', [
                    'tender' => $tender,
                    'tenders' => $tenders
                ]);?>
        </div>
    </div>

    <!-- Modal "Модальная ajax форма" -->
    <div class="modal fade" id="form-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sw">
            <div class="modal-content">
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <div class="modal-body">
                    ...
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="complaints-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sw">
            <div class="modal-content">
                <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <div class="modal-body">
                    ...
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="contract-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sw">
            <div class="modal-content">
                <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                <div class="modal-body">
                    ...
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

</div>
    <?

$this->registerJsFile(Url::to('@web/js/award.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
//$this->registerJsFile(Url::to('@web/js/prequalification.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

$this->registerJs(
    'var FilesCount = 0;' //. /*count($tender->cancellations->documents)*/ .';'
    , yii\web\View::POS_END);

//