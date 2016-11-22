<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


//Yii::$app->VarDumper->dump($contract, 10, true);die;
$fieldLabel = $contract->attributeLabels();
?>
<div class="tender-preview wrap-preview">

    <?php
    echo $this->render('/site/head', [
        'title' => $this->title,
        'descr' => ''
    ]);
    ?>

    <?php
    if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::$app->session->getFlash('message'); ?>
        </div>
    <?php } ?>

    <?php if ($contract->status == 'terminated' && (isset($contract->terminationDetails)&& $contract->terminationDetails)) { ?>
        <div class="alert alert-danger fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::t('app', 'Контракт припинено.'); ?>
            <?= Yii::$app->formatter->asDate($contract->dateModified); ?>
        </div>
    <?php } ?>

    <?php if ($contract->status == 'terminated' && !isset($contract->terminationDetails)) { ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= Yii::t('app', 'Контракт завершено.'); ?>
            <?= Yii::$app->formatter->asDate($contract->dateModified); ?>
        </div>
    <?php } ?>

    <hr/>
    <input type="hidden" id="current_locale" value="<?= substr(Yii::$app->language, 0, 2) ?>">

    <div class="info-block margin_b">

        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'ID') ?></div>
            <div class="col-md-9"><b tid="tenderID"><?= @$contract->id ?></b></div>
        </div>

        <div class="row tender-id-box">
            <div class="col-md-3"><?= Yii::t('app', 'ContractID') ?></div>
            <div class="col-md-9"><b tid="tenderID"><?= @$contract->contractID ?></b></div>
        </div>


        <h2><?= Yii::t('app', 'ОРГАНІЗАТОР ЗАКУПІВЛІ') ?></h2>

        <div class="row one-row-style">
            <div class="col-md-3"><?= Yii::t('app', 'Назва') ?></div>
            <div class="col-md-9"><b tid="procuringEntity.name"><?= @$contract->procuringEntity->name ?></b></div>
        </div>

        <div class="row one-row-style">
            <div class="col-md-3"><?= Yii::t('app', 'Код в ЄДРПОУ / ІПН') ?></div>
            <div class="col-md-9"><b tid="procuringEntity.name"><?= @$contract->procuringEntity->identifier->id ?></b>
            </div>
        </div>

        <div class="row one-row-style">
            <div class="col-md-3"><?= Yii::t('app', 'Юридична адреса') ?></div>
            <div class="col-md-9"><b>
                    <?= @$contract->procuringEntity->address->postalCode ?>
                    <?= @$contract->procuringEntity->address->countryName ?>
                    <?= @$contract->procuringEntity->address->streetAddress ?>
                    <?= @$contract->procuringEntity->address->region ?>
                    <?= @$contract->procuringEntity->address->locality ?>
                </b>
            </div>
        </div>


        <h2><?= Yii::t('app', 'Контактна особа') ?></h2>

        <div class="contact_group_wrapper">

            <div class="row one-row-style">
                <div class="col-md-3"><?= $contract->procuringEntity->contactPoint->getAttributeLabel('name') ?></div>
                <div class="col-md-6">
                    <b> <i>   <?= Html::encode($contract->procuringEntity->contactPoint->name) ?></i></b><br/>
                    <b> <i>   <?= Html::encode($contract->procuringEntity->contactPoint->name_en) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->procuringEntity->contactPoint->getAttributeLabel('email') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->procuringEntity->contactPoint->email) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->procuringEntity->contactPoint->getAttributeLabel('telephone') ?></div>
                <div class="col-md-6">
                    <b> <i> <?= Html::encode($contract->procuringEntity->contactPoint->telephone) ?></i></b>
                </div>
            </div>

        </div>


        <h2><?= Yii::t('app', 'РЕКВІЗИТИ КОНТРАКТУ') ?></h2>

        <div class="contact_group_wrapper">

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= Yii::t('app', 'Сума контракту') ?></div>
                <div class="col-md-6">
                    <b>
                        <i tid="value.amount">
                            <?= Html::encode($contract->value->amount . ' ' . $contract->value->currency) ?>
                            <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$contract->value->valueAddedTaxIncluded]) ?>
                        </i>
                    </b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= Yii::t('app', 'Сума оплати за договором') ?></div>
                <div class="col-md-6">
                    <b>
                        <i tid="value.amount">
                            <?= Html::encode($contract->amountPaid->amount . ' ' . $contract->value->currency) ?>
                            <?= Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$contract->value->valueAddedTaxIncluded]) ?>
                        </i>
                    </b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->getAttributeLabel('contractNumber') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->contractNumber) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= Yii::t('app', 'Дата пiдписання') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->dateSigned)) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= Yii::t('app', 'Дата початку дiї') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->startDate)) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= Yii::t('app', 'Дата завершення дiї') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode(Yii::$app->formatter->asDatetime($contract->period->endDate)) ?></i></b>
                </div>
            </div>

        </div>


        <h2><?= Yii::t('app', 'ІНФОРМАЦІЯ ПРО ПОСТАЧАЛЬНИКА') ?></h2>

        <div class="contact_group_wrapper">

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->suppliers[0]->getAttributeLabel('name') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->suppliers[0]->name) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->suppliers[0]->identifier->getAttributeLabel('id') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->suppliers[0]->identifier->id) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div class="col-md-3"><?= Yii::t('app', 'Юридична адреса') ?></div>
                <div class="col-md-9"><b>
                        <?= @$contract->suppliers[0]->address->postalCode ?>
                        <?= @$contract->suppliers[0]->address->countryName ?>
                        <?= @$contract->suppliers[0]->address->streetAddress ?>
                        <?= @$contract->suppliers[0]->address->region ?>
                        <?= @$contract->suppliers[0]->address->locality ?>
                    </b>
                </div>
            </div>

        </div>


        <h2><?= Yii::t('app', 'КОНТАКТНА ОСОБА ПОСТАЧАЛЬНИКА') ?></h2>

        <div class="contact_group_wrapper">

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->suppliers[0]->contactPoint->getAttributeLabel('name') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->suppliers[0]->contactPoint->name) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->suppliers[0]->contactPoint->getAttributeLabel('telephone') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->suppliers[0]->contactPoint->telephone) ?></i></b>
                </div>
            </div>

            <div class="row one-row-style">
                <div
                    class="col-md-3"><?= $contract->suppliers[0]->contactPoint->getAttributeLabel('email') ?></div>
                <div class="col-md-6">
                    <b> <i>  <?= Html::encode($contract->suppliers[0]->contactPoint->email) ?></i></b>
                </div>
            </div>

        </div>


        <h2><?= Yii::t('app', 'ДОКУМЕНТИ КОНТРАКТУ/ЗМІН') ?></h2>

        <div class="contact_group_wrapper">
            <?php

            //формируем массив из последних версий файлов.
            $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($contract->documents);

            foreach ($realDocuments as $d => $doc) {
                if ($d === 'iClass') continue;
                if ($d === '__EMPTY_DOC__') continue;


                echo $this->render('view/_document', [
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => '',
                    'tender' => $contract
                ]);
            }

            ?>

        </div>

        <h2><?= Yii::t('app', 'Специфiкацiя закупiвлi') ?></h2>
        <div class="contact_group_wrapper">

            <?php
            foreach ($contract->items as $i => $item) {
                if ($i === '__EMPTY_ITEM__') continue;

                echo $this->render('view/_item', [
                    'k' => $i,
                    'item' => $item,
                    'currentLotId' => null
                ]);
            } ?>
        </div>


        <h2><?= Yii::t('app', 'ЗМІНИ ДО ДОГОВОРУ') ?></h2>
        <div class="contact_group_wrapper">

            <?php
            //            Yii::$app->VarDumper->dump($contract, 10, true, true);
            foreach ($contract->changes as $i => $change) {


                echo $this->render('view/_change', [
                    'change' => $change,
                ]);
            } ?>
        </div>

        <?php
        if(\app\models\Companies::checkCompanyIsContractOwner($contracts->id) && $contract->status != 'terminated') { ?>

            <div class="col-md-9 c-buttons">
                <?php
                echo Html::a(Yii::t('app', 'Зміни до договору'),
                    Yii::$app->urlManager->createAbsoluteUrl([
                        \app\models\Companies::getCompanyBusinesType() . '/contracting/update',
                        'id' => $contracts->id,
                    ]), [
                        'class' => 'btn btn-info',
                        'role' => 'button',
//                        'target'=>'_blank'
                    ]);


                echo Html::a(Yii::t('app', 'Виконання договору'),
                    Yii::$app->urlManager->createAbsoluteUrl([
                        \app\models\Companies::getCompanyBusinesType() . '/contracting/terminate',
                        'id' => $contracts->id,
                    ]), [
                        'class' => 'btn btn-danger',
                        'role' => 'button',
//                        'target'=>'_blank'
                    ]);
                ?>
            </div>
            <div class="col-md-12" id="sign_block">
                <?php
                if ($contracts->ecp == 0 && (\app\models\contractModels\Contract::getContractUploadedDocument($contracts->id) == 0)) {
                    if (\app\models\contractModels\Changes::isAllActive($contract->changes)) {

                        echo Html::button(Yii::t('app', 'Накласти ЕЦП'), [
                            'class' => 'sign_btn_contracting btn btn-warning',
                            'cid' => $contracts->id,
                            'contract_id' => $contracts->contract_id,
                            'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')
                        ]);
                    } elseif(count($changes) > 0) {
                        echo Yii::t('app', 'Накладання ЕЦП буде доступне пiсля завантаження усiх документiв.');
                    }
                }
                ?>
            </div>

            <?php
        }
        //        Yii::$app->VarDumper->dump($contract, 10, true);die;
        ?>
    </div>
</div>