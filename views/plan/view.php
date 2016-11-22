<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

//Yii::$app->VarDumper->dump($plan->items, 10, true);die;
/**
 * @var $plan app\models\tenderModels\Tender
 * @var $planId int
 * @var $plans app\models\Tenders
 * @var $published bool
 */

//$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $plan->attributeLabels();
?>
<div class="tender-preview wrap-preview">

    <?php
    echo $this->render('/site/head', [
        'title' => $this->title,
        'descr' => ''
    ]);
    ?>


    <?php if (md5($plans->signed_data) != md5($plans->response)) {

        if (\app\models\Companies::checkCompanyIsPlanOwner($plans->id)) { ?>
            <div class="bs-example">
                <div class="alert alert-warning fade in"><a href="#" class="close"
                                                            data-dismiss="alert">&times;</a><?= Yii::t('app', 'Вам необхiдно пiдписати план.') ?>
                </div>
            </div>
        <?php } else { ?>

            <div class="bs-example">
                <div class="alert alert-warning fade in"><a href="#" class="close"
                                                            data-dismiss="alert">&times;</a><?= Yii::t('app', 'Електронний цифровий підпис відсутній') ?>
                </div>
            </div>

        <?php }
    } else { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in"><a href="#" class="close"
                                                        data-dismiss="alert">&times;</a><?= Yii::t('app', 'Скрiплено цифровим підписом') ?>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
            </div>
        </div>
    <?php } ?>


    <hr/>
    <input type="hidden" id="current_locale" value="<?= substr(Yii::$app->language, 0, 2) ?>">

    <div class="info-block margin_b">

        <div class="row tender-id-box">
            <div class="col-md-3">ID</div>
            <div class="col-md-9"><b tid="PlanID"><?= @$plan->id ?></b></div>
        </div>
        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'PlanID') ?></div>
            <div class="col-md-9"><b tid="PlanID"><?= @$plan->planID ?></b></div>
        </div>


        <h2><?= Yii::t('app', 'Замовник') ?></h2>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Назва замовника') ?></div>
            <div class="col-md-9"><b tid="procuringEntity.name"><?= @$plan->procuringEntity->name ?></b></div>
        </div>

        <h2><?= Yii::t('app', 'Параметри закупiвлi') ?></h2>


        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Назва плану') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= $plan->budget->description; ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Бюджет') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= Html::encode($plan->budget->amount . ' ' . $plan->budget->currency) ?>
                        <? //= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$plan->budget->valueAddedTaxIncluded]) ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Валюта') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= $plan->budget->currency; ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Рiк') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= $plan->budget->year; ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Планова дата старту процедури') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= date('m/Y', strtotime(str_replace('/', '.', $plan->tender->tenderPeriod->startDate))); ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Примiтки') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?= $plan->budget->notes; ?>
                    </i>
                </b>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Тип процедури') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?php
                        if ($plan->tender->procurementMethod == '') {
                            echo \app\models\Plans::getPlanProcurementMethod()[''];
                        } else {
                            echo \app\models\Plans::getPlanProcurementMethod()[$plan->tender->procurementMethod . '_' . $plan->tender->procurementMethodType];
                        }
                        ?>
                    </i>
                </b>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Класифiкатор') ?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?//= $plan->classification->scheme ?>
                        <?= $plan->classification->id ?>
                        <?= $plan->classification->description ?>
                    </i>
                </b>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><?= Yii::t('app', 'Додаткова Класифiкцiя')?></div>
            <div class="col-md-6">
                <b>
                    <i>
                        <?php
                        foreach ($plan->additionalClassifications as $a => $additionalClassification) {
                        if ($a === 'iClass') continue;
                            if($additionalClassification->id == '000'){
                                echo '<p>'. $additionalClassification->description . '</p>';
                            }
                            else{
                                echo '<p>Код за '. Yii::t('app','dkcode_'.$additionalClassification->scheme) . ' - ' . $additionalClassification->id . ' - ' . $additionalClassification->description . '</p>';
                            }
                        }
                        ?>
                    </i>
                </b>
            </div>
        </div>

        <?php

        unset($plan->items['iClass']);
        unset($plan->items['__EMPTY_ITEM__']);
        if ($plan->items[0]->id == null) unset($plan->items[0]);
        //        Yii::$app->VarDumper->dump($plan->items, 10, true);die;
        if (count($plan->items)) {
            foreach ($plan->items as $i => $item) {
                if ($i === 'iClass') continue;
                if ($i === '__EMPTY_ITEM__') continue;
                ?>

                <div class="item no_border">

                    <div class="row">
                        <div class="col-md-9 margin_b"><h3><?= Yii::t('app', 'Предмет закупiвлi') ?></h3></div>
                        <div class="col-md-3">

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">  <?= Yii::t('app', 'Предмет закупiвлi') ?></div>
                        <div class="col-md-6">
                            <b>
                                <?= Html::encode($item->description) ?>
                            </b>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-3"> <?= Yii::t('app', 'Кiлькiсть') ?> </div>
                        <div class="col-md-6">
                            <b>
                                <i tid="items.quantity">
                                    <?= Html::encode($item->quantity) ?>
                                    <?
                                    if (isset($item->unit->code)) {
                                        $res = \app\models\Unit::findOne(['id' => $item->unit->code])->name;
                                        if ($res) {
                                            echo Html::encode($res);
                                        }
                                    }
                                    ?>
                                </i>
                            </b>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-3"
                             tid="items.classification.scheme">  <?= $item->getAttributeLabel('classification') ?></div>
                        <div class="col-md-6">
                            <b>
                                <i tid="items.classification.id">
                                    <?= Html::encode($item->classification->id) ?>
                                </i> -
                                <i tid="items.classification.description">
                                    <?= Html::encode($item->classification->description) ?>
                                </i>

                            </b>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3"
                             tid="items.additionalClassifications.scheme">  <?= $item->getAttributeLabel('additionalClassifications') ?> </div>
                        <div class="col-md-6">
                            <b>
                                <i tid="items.additionalClassifications.id">
                                    <?// Yii::$app->VarDumper->dump($item->additionalClassifications[0]->scheme, 10, true);die;?>
                                    <?= '<p>Код за '. Yii::t('app','dkcode_'.$item->additionalClassifications[0]->scheme) . ' - ';?>
                                    <?= Html::encode($item->additionalClassifications[0]->id) ?>
                                </i> -
                                <i tid="items.additionalClassifications.description">
                                    <?= Html::encode($item->additionalClassifications[0]->description) ?>
                                </i>
                            </b>
                        </div>
                    </div>


                    <!--                <div class="row">-->
                    <!--                    <div class="col-md-3">  -->
                    <?//= $item->deliveryDate->getAttributeLabel('endDate') ?><!--</div>-->
                    <!--                    <div class="col-md-6">-->
                    <!--                        <b>-->
                    <!--                            <i tid="items.deliveryDate.endDate">-->
                    <!--                                --><?//= $item->deliveryDate->endDate ? Html::encode($item->deliveryDate->endDate) : '' ?>
                    <!--                            </i>-->
                    <!--                        </b>-->
                    <!--                    </div>-->
                    <!--                </div>-->


                    <hr/>
                </div>
            <?php }
        }
        ?>

        <?php if (\app\models\Companies::checkCompanyIsPlanOwner($plans->id)) { ?>
        <div class="row buttons">
            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/plan/update', 'id' => $plans->id]) ?>"
               class="btn btn-success" role="button"><?= Yii::t('app', 'Edit plan') ?></a>
            <?php if ($plans->status == 'draft') { ?>
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/plan/delete', 'id' => $plans->id]) ?>"
                   class="btn btn-danger" role="button"><?= Yii::t('app', 'Delete plan draft') ?></a>
            <?php }
            if ((isset($plan->id) && $plan->id) && (md5($plans->signed_data) != md5($plans->response))) {
                echo Html::button(Yii::t('app', 'Накласти ЕЦП'), ['class' => 'sign_plan_btn btn btn-warning', 'tid' => $plan->id, 'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')]);
            }
            } ?>

            <div id="e_sign_block"></div>
        </div>
    </div>


</div>

<style>
    .form-horizontal .control-label {
        margin-bottom: 0;
        padding-top: 0;
        text-align: left;
    }
</style>

