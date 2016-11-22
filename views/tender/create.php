<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $tender app\models\tenderModels\Tender
 * @var $tenderId int
 * @var $tenders app\models\Tenders
 * @var $published bool
 */
$this->title = \app\models\Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $tender->attributeLabels();

if (!$tenderId) {
        $tenders = new stdClass();
        $tenders->tender_type = '';
        $tenders->tender_method = \app\models\Tenders::getTenderMethodDef();
}
?>

<div class="tender-preview m_create-wrap">

    <?php
    echo $this->render('/site/head', [
        'title' => $this->title,
        'descr' => ''
    ]);
    ?>

<input type="hidden" id="current_locale" value="<?=substr(Yii::$app->language, 0, 2)?>">

    <?php
    $form = ActiveForm::begin([
        'validateOnType' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
//        'enableClientValidation' => false,
//        'enableAjaxValidation' => true,
        'options' => [
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data'
        ],
        'id' => 'tender_simple_create',
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]);

    $template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";

    ?>

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example"><div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?></div></div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message_error')) { ?>
        <div class="bs-example"><div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_error'); ?></div></div>
    <?php } ?>

    <?php if (isset($tenders->status) && $tenders->status == 'cancelled') { ?>
        <div class="alert alert-danger"><?= Yii::t('app','Закупiвлю скасованно')?></div>
    <?php } ?>

    <div class="info-block m_options">

        <?php if ($tender->tenderID) { ?>
            <div class="row">
                <div class="col-md-3">TenderID</div>
                <div class="col-md-9"><b><?= @$tender->tenderID ?></b></div>
            </div>
            <div class="row">
                <div class="col-md-3">ID</div>
                <div class="col-md-9"><b><?= @$tender->id ?></b></div>
            </div>
        <?php } ?>

        <h2><?=Yii::t('app','Параметри закупiвлi')?></h2>

        <div class="alert alert-warning limited_message hide"><?= Yii::t('app','Зверніть увагу, що звіт буде опубліковано на веб-порталі Уповноваженого органу лише після внесення інформації по постачальнику і договору, накладення ЕЦП та завершення процедури')?></div>

        <div class="form-group tender_type">
            <label class="col-md-3 control-label"><?= Yii::t('app','Процедура оголошення')?></label>

            <div class="col-md-6">
                <?= Html::dropDownList('tender_method', $tenders->tender_method, \app\models\Tenders::getTenderMethodByCompanyId(Yii::$app->user->identity->company_id, $tenders->tender_method),
                    [
                        'class' => 'form-control tender_method_select',
                        ($published ? 'disabled' : '')=>($published ? 'disabled' : ''),
                    ]);
                if ($published) echo '<input type="hidden" value="'.$tenders->tender_method.'" name="tender_method">';
                ?>
            </div>
        </div>

        <div class="form-group tender_type">
            <label class="col-md-3 control-label"><?= Yii::t('app','Тип оголошення')?></label>
            <div class="col-md-6">
                <?php
                echo Html::dropDownList('tender_type', $tenders->tender_type, \app\models\Tenders::getTenderType(),
                    [
                        'class' => 'form-control tender_type_select',
                        ($published ? 'disabled' : '')=>($published ? 'disabled' : ''),
                    ]);
                if ($published) echo '<input type="hidden" value="'.$tenders->tender_type.'" name="tender_type">';
                ?>
            </div>
        </div>

        <?=Html::hiddenInput(null, $published, [
            'id'=>'is_published'
        ]);?>
<!--        <div class="rationale_wrapper">-->
<!--            --><?//= $form->field($tender, 'procurementMethodRationale', ['template' => $template])->textarea(['name' => 'Tender[procurementMethodRationale]']) ?>
<!--        </div>-->

        <div class="negotiation_wrapper">
            <?= $form->field($tender, 'causeDescription', ['template' => $template])->textarea(['name' => 'Tender[causeDescription]']) ?>

            <?= $form->field($tender, 'cause',['template' => $template])->radioList(\app\models\Tenders::GetJustificationMethod()) ?>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label"><?= Yii::t('app','Максимальний бюджет')?></label>
            <div class="col-md-6">
                <?= Html::dropDownList('Tender[value][valueAddedTaxIncluded]', (int)$tender->value->valueAddedTaxIncluded,
                    ['0' => 'Без урахування ПДВ','1' => 'З урахуванням ПДВ'],
                    [
                        'class' => 'form-control',
                        'id' => "tender_type"
                    ]); ?>
            </div>
        </div>

        <div class="guarantee_needed">
            <div class="guarantee_block">
                <div class="form-group">
                    <label
                        class="col-md-3 control-label"><?= Yii::t('app', 'Вид забезпечення тендерних пропозицiй') ?></label>
                    <div class="col-md-6">
                        <?= Html::dropDownList(
                            null,
                            ($tender->guarantee->amount) != null ? 1 : 0,
                            ['0' => Yii::t('app', 'Вiдсутнє'), '1' => Yii::t('app', 'Електронна банкiвська гарантiя')],
                            [
                                'class' => 'form-control guarantee_select',
                            ]); ?>
                    </div>
                </div>

                <div class="guarantee_amount">
                    <?= $form->field($tender->guarantee, 'amount', ['template' => $template])
                        ->textInput(['name' => 'Tender[guarantee][amount]', 'class' => 'form-control'])
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?= Yii::t('app','Строк, на який укладається рамкова угода.')?></label>
                <div class="col-md-6">
                    <?=Html::textInput(null,null,[
                        'disabled'=>'disabled',
                        'class' => 'form-control'
                    ])?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?= Yii::t('app','Кiлькiсть учасникiв, з якими буде укладено рамкову угоду.')?></label>
                <div class="col-md-6">
                    <?=Html::textInput(null,null,[
                        'disabled'=>'disabled',
                        'class' => 'form-control'
                    ])?>
                </div>
            </div>


        </div>


        <div class="simple_only">

            <?= $form->field($tender->value, 'amount', ['template' => $template])
                ->textInput(['name' => 'Tender[value][amount]', 'class' => 'form-control tender_full_amount'])
//                ->widget(\yii\widgets\MaskedInput::className(), [
//                    'name' => 'tel',
//                    'mask' => '9{1,11}.9{2}',
//                    'options' => [
//                        'class' => 'form-control tel_input',
//                    ],
//                    'clientOptions' => [
//                        'clearIncomplete' => true
//                    ]
//                ])
                ->label($fieldLabel['value']);
            ?>



            <div class="amount_wrapper">
                <?
                $tenderLabels = $tender->attributeLabels();
                echo $form->field($tender->minimalStep, 'amount', ['template' => $template])
                    ->textInput(['placeholder' => $tender->value->currency, 'name' => 'Tender[minimalStep][amount]', 'class' => 'form-control tender_step_amount', 'validateOnType' => true, 'validateOnChange' => true, 'validateOnBlur' => true])
                    ->label($tenderLabels['minimalStep']);

                echo $form->field($tender->minimalStep, 'amountProcent', ['template' => $template])
                    ->textInput(['placeholder' => '%', 'class' => 'form-control tender_step_amount_procent', 'name'=>'']);
                ?>
            </div>

        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?= Yii::t('app','Валюта тендеру')?></label>
            <div class="col-md-6">
                <?= Html::dropDownList('Tender[value][currency]', $tender->minimalStep->currency,
                    \app\models\tenderModels\Value::getCurrency(),
                    [
                        'class' => 'form-control',
                    ]);
                ?>
            </div>
        </div>
    </div>

    <div class="info-block m_info">
        <h2><?= Yii::t('app','Загальна iнформацiя про закупiвлю')?></h2>
<!--        <ul class="nav nav-tabs eu_procedure">-->
<!--            <li class="active"><a href="#ua" data-toggle="tab">Українською</a></li>-->
<!--            <!--            <li><a href="#ru" data-toggle="tab">По-русски</a></li>-->
<!--            <li><a href="#en" data-toggle="tab">In English</a></li>-->
<!--        </ul>-->



        <div class="tab-content">

                <div class="tab-pane active" id="ua">
                    <?= $form->field($tender, 'title', ['template' => $template])->textInput(['name' => 'Tender[title]']) ?>
                    <?= $form->field($tender, 'description', ['template' => $template])->textarea(['name' => 'Tender[description]']) ?>
                </div>

                <!--            <div class="tab-pane" id="ru">-->
                <!---->
                <!--                --><? //= $form->field($tender, 'titleRu', ['template' => $template])->textInput(['name' => 'Tender[title_ru]']) ?>
                <!--                --><? //= $form->field($tender, 'descriptionRu', ['template' => $template])->textarea(['name' => 'Tender[description_ru]']) ?>
                <!---->
                <!--            </div>-->

                <div class="tab-pane eu_procedure" id="en">

                    <?= $form->field($tender, 'title_en', ['template' => $template])->textInput(['name' => 'Tender[title_en]']) ?>
                    <?= $form->field($tender, 'description_en', ['template' => $template])->textarea(['name' => 'Tender[description_en]']) ?>

                </div>



<!--            --><?// if ($published) { ?>
<!--            <div class="info-block">-->
<!--                <h2>Скасування закупiвлi</h2>-->
<!--                <div class="info-block  cancellations_block">-->
<!--                    --><?//
//                    unset($tender->cancellations['iClass']);
//                    unset($tender->cancellations['__EMPTY_CANCEL__']);
//                    foreach ($tender->cancellations as $c => $cancellation) {
//                        if ($cancellation->cancellationOf && $cancellation->cancellationOf == 'lot') continue;
//
//                        echo $this->render('_cancellation', [
//                            'form' => $form,
//                            'template' => $template,
//                            'cancellation' => $cancellation,
//                            'cancellation_of' => 'tender',
//                            'related_lot' => '',
//                            'k' => $c
//                        ]);
//                    }
//                    ?>
<!--                </div>-->
<!--            </div>-->
<!--            --><?// } ?>




            <h2><?= Yii::t('app','Тендерна документацiя')?></h2>
            <div class="info-block document_block">

                <?php
                //формируем массив из последних версий файлов.
                $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($tender->documents);
                $tmp['__EMPTY_DOC__'] = new \app\models\tenderModels\Document();
                if ($tenders->status == 'draft'){
                    $realDocuments = $tender->documents;
                }
                $realDocuments = array_merge($tmp, $realDocuments);

                if ($tenders->tender_type == 1) {


                    foreach ($realDocuments as $d => $doc) {
                        if ($d === '__EMPTY_DOC__') {echo '<div id="hidden_document_original" class="row margin23 panel-body" style="display: none">';}
                        else {echo '<div class="row margin23 panel-body">';}

                        echo $this->render('_document', [
                            'form' => $form,
                            'template' => $template,
                            'documents' => $doc,
                            'k' => $d,
                            'lot_items' => [],
                            'currentLotId' => ''
                        ]);
                        if ($d === '__EMPTY_DOC__') {echo '</div>';}
                        else {echo '</div>';}
                    }


                } else if ($tenders->tender_type == 2) {

                    foreach ($realDocuments as $d => $doc) {
                        if ($d === '__EMPTY_DOC__') {echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';}
                        elseif ($doc->documentOf == 'tender') {echo '<div class="row margin23 panel-body">';}
                        if ($d === '__EMPTY_DOC__' || $doc->documentOf == 'tender') {


                            echo $this->render('_document', [
                                'form' => $form,
                                'template' => $template,
                                'documents' => $doc,
                                'k' => $d,
                                'lot_items' => [],
                                'currentLotId' => ''
                            ]);
                        }
                        if ($d === '__EMPTY_DOC__') {echo '</div>';}
                        elseif ($doc->documentOf == 'tender') {echo '</div>';}
                    }

                } else {
                    foreach ($realDocuments as $d => $doc) {
                        if ($d === '__EMPTY_DOC__') {echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';}
                        else {echo '<div class="row margin23 panel-body">';}

                        echo $this->render('_document', [
                            'form' => $form,
                            'template' => $template,
                            'documents' => $doc,
                            'k' => $d,
                            'lot_items' => [],
                            'currentLotId' => ''
                        ]);
                        if ($d === '__EMPTY_DOC__') {echo '</div>';}
                        else {echo '</div>';}
                    }
                }
                unset($tender->documents['iClass']);
                unset($tender->documents['__EMPTY_DOC__']);

                ?>

            </div>
                <a role="button" class="btn btn-success col-md-2 uploadfile" href="javascript:void(0)"><?= Yii::t('app', 'add file') ?></a>

            <div class="clearfix"></div>









            <div class="info-block">
                <h2><?= Yii::t('app','Специфiкацiя закупiвлi')?></h2>

                <p><?= Yii::t('app','Надайте iнформацiю щодо предметiв закупiлi, якi Ви маєте намiр прибдати в рамках даного оголошення')?></p>


                <div class="info-block lots_block">

                    <?php
//                    Yii::$app->VarDumper->dump($tender->lots, 10, true);die;
                    foreach ($tender->lots as $k => $lot) {
                    if ($k === 'iClass') continue;
                    if ($k === '__EMPTY_LOT__') echo '<div id="hidden_lot_original" style="display: none;">';

                    echo $this->render('_lot', [
                        'k' => $k,
                        'lot' => $lot,
                        'form' => $form,
                        'template' => $template,
                        'items' => $tender->items,
                        'features'=>$tender->features,
                        'documents'=>$tender->documents,
                        'tenderType'=>$tenders->tender_type,
                        'cancellations'=>$tender->cancellations,
                        'published'=>$published

                    ]);
                        if ($k === '__EMPTY_LOT__') echo '</div>';
                    } ?>
                </div>
                <button type="button" class="btn btn-default add_lot"><?= Yii::t('app','Додати лот')?></button>




                <div class="features_wrapper">
                <h2><?= Yii::t('app','Нецiновi показники тендеру')?></h2>

                <div class="info-block features_rules">
                    <div class="col-md-9">
                        <?= Yii::t('app','Сформулюйте перелiк нецiнових (якiсних) критерiїв, що будуть розглядатись i враховуватись вами як Замовником при визначеннi переможця редукцiону')?>

                    </div>

                    <div class="col-md-3 btn-group document_type">
                        <?= Yii::t('app','Зауважте, що загальна вага нецiнових показникiв не повинна перевищувати 30%')?>

                    </div>
                    <div class="clearfix"></div>
                </div>



                <div class="info-block  features_block">

                    <?php


                    if ($tenders->tender_type == 2) {


                        foreach ($tender->features as $f => $feature) {

                            if ($f === 'iClass') continue;
                            if ($f === '__EMPTY_FEATURE__') echo '<div id="hidden_feature_original"class="row margin23" style="display: none">';

                            if ($f === '__EMPTY_FEATURE__' || $feature->relatedItem == '' || $feature->featureOf =='tenderer' || $feature->featureOf =='tender') {
                                echo $this->render('_feature', [
                                    'form' => $form,
                                    'template' => $template,
                                    'feature' => $feature,
                                    'k' => $f
                                ]);
                            }
                            if ($f === '__EMPTY_FEATURE__') echo '</div>';
                        }


                    }else{

                        foreach ($tender->features as $f => $feature) {

                            if ($f === 'iClass') continue;
                            if ($f === '__EMPTY_FEATURE__') echo '<div id="hidden_feature_original"class="row margin23" style="display: none">';


                            echo $this->render('_feature', [
                                'form' => $form,
                                'template' => $template,
                                'feature' => $feature,
                                'k' => $f
                            ]);
                            if ($f === '__EMPTY_FEATURE__') echo '</div>';
                        }

                    }
                    ?>
                    <button type="button" class="btn btn-default add_feature"><?= Yii::t('app','Додати показник')?></button>
                </div>
                </div>



            </div>






                <div class="info-block periods_wrapper">
                    <h2><?= Yii::t('app','Дати та термiни')?></h2>
                    <?php

                    echo $form->field($tender->enquiryPeriod, 'startDate', ['template' => $template])
                        ->textInput([
                            'name' => 'Tender[enquiryPeriod][startDate]',
                            'class' => 'form-control picker',
                        ]);

                    echo $form->field($tender->enquiryPeriod, 'endDate', ['template' => $template])
                        ->textInput([
                            'name' => 'Tender[enquiryPeriod][endDate]',
                            'class' => 'form-control picker',
                        ]);


                    echo $form->field($tender->tenderPeriod, 'startDate', ['template' => $template])
                        ->textInput([
                            'name' => 'Tender[tenderPeriod][startDate]',
                            'class' => 'form-control picker'
                        ]);
                    echo $form->field($tender->tenderPeriod, 'endDate', ['template' => $template])
                        ->textInput([
                            'name' => 'Tender[tenderPeriod][endDate]',
                            'class' => 'form-control picker'
                        ]);
                    ?>
                </div>


                <div class="info-block contact_block">
                    <h2><?= Yii::t('app','Контактна особа')?></h2>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?= Yii::t('app','Оберiть')?></label>

                        <div class="col-md-6">

                            <?= Html::dropDownList('Tender[procuringEntity][contactPoint][fio]', $tender->procuringEntity->contactPoint->fio,
                                ArrayHelper::map(\app\models\Persons::find()->where(['company_id'=>Yii::$app->user->identity->company_id])->all(),
                                    'id',
                                    function ($model, $defaultValue) {
                                        return $model->userName . ' ' . $model->userSurname . ' ' . $model->userPatronymic;
                                    }
                                ),
                                ['class' => 'form-control contact_person', 'prompt' => 'Не вибрано']);
                            ?>
                        </div>
                    </div>
                    <div class="contact_group_wrapper">
                        <?= $form->field($tender->procuringEntity->contactPoint, 'name', ['template' => $template])
                            ->textInput(['name' => 'Tender[procuringEntity][contactPoint][name]']);
                        ?>
                        <?= $form->field($tender->procuringEntity->contactPoint, 'email', ['template' => $template])
                            ->textInput(['name' => 'Tender[procuringEntity][contactPoint][email]']);
                        ?>
                        <?= $form->field($tender->procuringEntity->contactPoint, 'telephone', ['template' => $template])
                            ->widget(\yii\widgets\MaskedInput::className(), [
                                'mask' => '+38(999)999-99-99',
                                'options' => [
                                    'class' => 'form-control tel_input',
                                ],
                                'clientOptions' => [
                                    'clearIncomplete' => true
                                ]
                            ])
                            ->textInput(['name' => 'Tender[procuringEntity][contactPoint][telephone]']);
                        ?>
                        <div class="eu_procedure additional_person">
                            <?= $form->field($tender->procuringEntity->contactPoint, 'name_en', ['template' => $template])
                                ->textInput(['name' => 'Tender[procuringEntity][contactPoint][name_en]']);
                            ?>

                            <div class="form-group">
                                <label class="col-md-3 control-label"><?= Yii::t('app','Мова спiлкування')?></label>

                                <div class="col-md-6">

                                    <?= Html::dropDownList('Tender[procuringEntity][contactPoint][availableLanguage]', $tender->procuringEntity->contactPoint->availableLanguage,
                                        [
                                            'uk' => 'Украинский',
                                            'en' => 'English',
                                            'ru' => 'Русский',
                                        ],
                                        ['class' => 'form-control contact_point_available_language']);
                                    ?>
                                </div>
                            </div>

                            <?php
//                            Yii::$app->VarDumper->dump($tender->procuringEntity, 10, true, true);
                            ?>
                            <?= $this->render('_additionalPersons',[
                                'additionalContacts'=>$tender->procuringEntity->additionalContactPoints
                            ]); ?>

                        </div>
                    </div>


                </div>
            </div>


        </div>
    </div>
    <div class="col-md-offset-3 col-md-9">
        <?php
            if (! (isset($tenders->status) && in_array( $tenders->status,['cancelled','complete','unsuccessful'] ))) {
                echo Html::submitButton(Yii::t('app', 'Зберегти та перейти до публiкацiї'), ['class' => 'btn btn-default btn_submit_form']);
            }
        ?>

        <input type="hidden" value="" name="simple_submit">
        <?= (!$published ? Html::submitButton(Yii::t('app', 'Зберегти до чернетки'), ['class' => 'btn btn-default drafts_submit', 'name'=>'drafts']) : '') ?>

        <input type="hidden" id="tender_id" value="<?= isset($tenderId) ? $tenderId : '' ?>" name="Tender[tenderId]">

        <?php
        if (isset($tenders->tender_id) && $tenders->ecp != 1) {
            echo Html::button(Yii::t('app', 'Накласти ЕЦП'), ['class' => 'sign_btn btn btn-warning', 'tid' => $tenders->tender_id, 'data-loading-text' => '<i class=\'fa fa-spinner fa-spin \'></i>' . Yii::t('app', ' Зачекайте')]);
            if($tenders->tender_method == 'open_belowThreshold'){
                echo Html::checkbox('need_tender_esign',false,['class'=>'need_tender_esign', 'label'=>Yii::t('app','Я хочу пiдписати')]);
            }
        }
        ?>

<!--        --><?//= Html::checkbox('need_tender_esign',false,['class'=>'need_tender_esign', 'label'=>Yii::t('app','Я хочу пiдписати')]);?>
    </div>


    <?php ActiveForm::end(); ?>
</div><!-- tender-create -->

<div id="sign_block"></div>
<?php



echo $this->render('classificator_modal');
$this->registerJsFile(Url::to('@web/js/project.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

$this->registerJs(
    'var CancelCount = '. count($tender->cancellations) .';'
    , yii\web\View::POS_END);
?>


