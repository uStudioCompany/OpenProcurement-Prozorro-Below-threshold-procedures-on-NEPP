<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName;

/**
 * @var $form yii\widgets\ActiveForm
 * @var $plan app\models\planModels\Plan
 * @var $id int
 * @var $published bool
 */
$fieldLabel = $plan->attributeLabels();

$template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";

?>
<div class="tender-preview m_create-wrap">

    <?= $this->render('/site/head', [
        'title' => $this->title,
        'descr' => ($id ? Yii::t('app', 'plan.EditPlan') : Yii::t('app', 'plan.CreatePlan'))]); ?>

    <?php if (Yii::$app->session->hasFlash('message')) { ?>
        <div class="bs-example">
            <div class="alert alert-success fade in"><a href="#" class="close"
                                                        data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
            </div>
        </div>
    <?php } ?>

    <?php if (Yii::$app->session->hasFlash('message_error')) { ?>
        <div class="bs-example">
            <div class="alert alert-danger fade in"><a href="#" class="close"
                                                       data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_error'); ?>
            </div>
        </div>
    <?php } ?>

    <?php
    $form = ActiveForm::begin([
        'validateOnType' => true,
        'options' => [
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data'
        ],
        'id' => 'plan_create',
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]);
    ?>

    <div class="info-block">
        <h4>Параметри плану <?= (($plan->budget->project->name) ? '"' . $plan->budget->project->name . '"' : '') ?></h4>

        <div class="form-group tender_type">
            <label class="col-md-3 control-label"><?= Yii::t('app', 'Тип процедури') ?></label>

            <div class="col-md-6">
                <?= Html::dropDownList('procurementMethod', $plan->tender->procurementMethod . '_' . $plan->tender->procurementMethodType, \app\models\Plans::getPlanProcurementMethod(),
                    [
                        'class' => 'form-control tender_method_select',
                    ]);
                //if ($published) echo '<input type="hidden" value="'.$tenders->tender_method.'" name="tender_method">';
                ?>
            </div>
        </div>

        <? //= $form->field($plan->budget->project, 'name', ['template' => $template])->textInput(['name' => 'Plan[budget][project][name]']) ?>
        <!--        <input type="hidden" name="Plan[budget][project][id]" value="-->
        <? //= ( $plan->budget->id ? $plan->budget->id : md5(Yii::$app->security->generateRandomString(32)) ) ?><!--">-->
        <input type="hidden" name="Plan[id]" id="plan_id" value="<?= $plan->id ?>">
        <input type="hidden" name="id" id="id" value="<?= $id ?>">
        <?= $form->field($plan->budget, 'description', ['template' => $template])->textarea(['name' => 'Plan[budget][description]']) ?>

        <?= $form->field($plan->budget, 'notes', ['template' => $template])->textarea(['name' => 'Plan[budget][notes]']) ?>

        <?php // plan:budget:amount
        //        $tmp_tpl = ''.'
        //            {label}
        //            <div class="col-md-3">{input}</div>
        //            <div class="col-md-3">'.
        //                Html::dropDownList('Plan[budget][valueAddedTaxIncluded]', $plan->budget->valueAddedTaxIncluded, [1=>Yii::t('app', 'plan.PDV.1'),0=>Yii::t('app', 'plan.PDV.0')], ['class' => 'form-control']) .'
        //            </div>
        //            <div class=\"col-md-3\">{error}</div>
        //            ';
        //        $tmp_tpl = ''.'
        //            {label}
        //            <div class="col-md-3">{input}</div>
        //            <div class=\"col-md-3\">{error}</div>
        //            ';
        echo $form->field($plan->budget, 'amount', ['template' => $template])
            ->textInput([
                'placeholder' => $plan->budget->currency,
                'name' => 'Plan[budget][amount]',
                'class' => 'form-control tender_full_amount'])
            ->label($fieldLabel['budget']);
        ?>


        <div class="form-group tender_type">
            <label class="col-md-3 control-label"><?= Yii::t('app', 'Валюта') ?></label>

            <div class="col-md-6">
                <?= Html::dropDownList('Plan[budget][currency]', $plan->budget->currency,
                    \app\models\tenderModels\Value::getCurrency(),
                    [
                        'class' => 'form-control',
                    ]);
                ?>
            </div>
        </div>


        <!--        <input type="hidden" name="Plan[budget][amountNet]" value="0">-->
        <!--        <input type="hidden" name="Plan[budget][currency]" value="UAH">-->


        <?php // plan:tender:tenderPeriod:startDate
        echo $form->field($plan->tender->tenderPeriod, 'startDate', ['template' => $template])
            ->textInput([
                'name' => 'Plan[tender][tenderPeriod][startDate]',
                'class' => 'form-control picker',
                'value' => $plan->tender->tenderPeriod->startDate ? date('d/Y', strtotime($plan->tender->tenderPeriod->startDate)) : ''
            ]);


        ?>

        <?= $form->field($plan->budget, 'year', ['template' => $template])
            ->textInput([
                'name' => 'Plan[budget][year]',
                'class' => 'form-control picker_year',]);
        ?>

        <?= $this->render('__classification', [    // plan:classification:id/description
            'k' => '', 'type' => 'cpv', 'form' => $form,
            'parentId' => '',
            'name' => 'classification',
            'no_head_select' => true,
            'classification' => $plan->classification,]);
        ?>


        <div class="additionalClassifications_block">
            <div class="form-group">
                <label
                    class="col-md-3 control-label"><?php echo Yii::t('app', 'additionalClassifications') ?></label>

                <div class="col-md-6">
                    <?php
                    $code = $plan->additionalClassifications[0]->scheme . '_' . mb_strtolower(\yii\helpers\BaseInflector::transliterate($plan->additionalClassifications[0]->scheme));
                    $selectItems = array_merge(['000' => Yii::t('app', 'undefined')], Yii::$app->params['DK_LIBS']);
                    echo Html::dropDownList('Plan[additionalClassifications][0][dkType]', $plan->additionalClassifications[0]->dkType ? $plan->additionalClassifications[0]->dkType : $code,
                        $selectItems,
                        [
                            'class' => 'form-control additionalClassifications_select',
                        ]);
                    ?>
                </div>
            </div>


            <div class="additionalClassifications_input">
                <?= $this->render('__dk_classification', [    // plan:additionalClassifications:0:id/description
                    'k' => '',
                    'type' => mb_strtolower(\yii\helpers\BaseInflector::transliterate($plan->additionalClassifications[0]->scheme)),
                    'form' => $form,
                    'parentId' => '',
                    'name' => 'additionalClassifications][0',
                    'classification' => $plan->additionalClassifications[0],]); ?>

            </div>


        </div>


        <?

//Yii::$app->VarDumper->dump($plan->additionalClassifications, 10, true);die;
        for ($i = 1; $i < 4; $i++) {
            if($plan->additionalClassifications[$i]->id == null && $i > 0) echo '<div class="hide">';
            echo $this->render('__classification', [
                'k' => $i, 'type' => 'kekv', 'form' => $form,
                'parentId' => '',
                'name' => 'additionalClassifications]['.$i,
                'classification' => $plan->additionalClassifications[$i]
            ]);
            if($plan->additionalClassifications[$i]->id == null && $i > 0) echo '</div>';
        }




//        $this->render('__classification', [    // plan:additionalClassifications:1:id/description
//            'k' => '', 'type' => 'kekv', 'form' => $form,
//            'parentId' => '',
//            'name' => 'additionalClassifications][1',
//            'classification' => $plan->additionalClassifications[1],]);
        ?>
        <button type="button" class="btn btn-default add_kekv_plan"><span
                class="glyphicon glyphicon-plus-sign"></span> <?= Yii::t('app', 'Add') ?>  <?= Yii::t('app', 'kekv') ?>
        </button>
        <div style="clear:both;"></div>

        <div class="info-block items_block">
            <h2><?= Yii::t('app', 'Специфiкацiя плану') ?></h2>
            <p><?= Yii::t('app', 'Надайте iнформацiю щодо предметiв закупiлi, якi Ви маєте намiр прибдати в рамках даного плану') ?></p>
            <?php
            foreach ($plan->items as $k => $item) {
                if ($k === 'iClass') continue;
                if ($k === '__EMPTY_ITEM__') echo '<div id="item_new_element" style="display: none;">';
                if ($item['id'] == '') echo '<div class="hide">';
                else $k++;

                //include '_item.php';
                echo $this->render('_item', [
                    'k' => $k,
                    'item' => $item,
                    'form' => $form,
                    'typedk' => mb_strtolower(\yii\helpers\BaseInflector::transliterate($plan->additionalClassifications[0]->scheme)),
                ]);
                if ($k === '__EMPTY_ITEM__') echo '</div>';
                if ($item['id'] == '') echo '</div>';
            } ?>
        </div>
        <button type="button" class="btn btn-default add_item_plan"><span
                class="glyphicon glyphicon-plus-sign"></span> <?= Yii::t('app', 'Add') ?>  <?= Yii::t('app', 'plan.item') ?>
        </button>
    </div>
    <div id="sign_block"></div>
    <div class="col-md-offset-3 col-md-6 clearfix">
        <!--<button type="submit" name="submit" class="btn btn-default btn_submit_form">Зберегти та перейти до публiкацiї</button>
        <button type="submit" name="drafts" class="btn btn-default drafts_submit">Зберегти до черновика</button>-->
        <?= Html::submitButton(Yii::t('app', 'Cancel'), [
            'class' => 'btn btn-danger',
            'name' => 'cancel',
            'id' => 'plan_cancel',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to cancel this plan?'),
                'method' => 'post',
                'params' => [
                    'cancel' => 'cancel',
                ]
            ],
        ]) ?>
        <?= ($published ? '' : Html::submitButton(Yii::t('app', 'Save to draft'), ['class' => 'btn btn-primary', 'name' => 'drafts', 'id' => 'plan_drafts'])) ?>
        <?= Html::submitButton(Yii::t('app', 'Save and publish'), ['class' => 'btn btn-default', 'name' => 'publish']) ?>
<!--        --><?php
//        if ((isset($plan->id) && $plan->id) && (md5($plans->signed_data) != md5($plans->response))) {
//            echo Html::button(Yii::t('app', 'Накласти ЕЦП'), ['class' => 'sign_plan_btn btn btn-warning', 'tid' => $plan->id]);
//        }
//        ?>
    </div>
    <?php ActiveForm::end(); ?>

    <div id="e_sign_block"></div>
    <?php
    //    Yii::$app->VarDumper->dump($plans, 10, true);die;
    //    echo md5($plans->prev_response);
    //    echo '<br/>';
    //    echo md5($plans->response);
    ?>

</div><!-- plan-create -->

<?php

echo $this->render('classificator_modal');
//$this->registerJsFile(Url::to('@web/js/features.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile(Url::to('@web/js/plan.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);

$this->registerJs('

var ItemCount = ' . (count($plan->items) - 2) . ';
var curLocale = "' . substr(Yii::$app->language, 0, 2) . '";
var AutoSaveTimer;

', yii\web\View::POS_END);
?>


