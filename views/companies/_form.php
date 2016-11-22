<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Companies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="companies-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'id' => 'user-update-form',
        'options' => [
            'class' => 'form-horizontal',
        ],
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]); ?>

    <?=$form->field($model, 'is_seller')->hiddenInput()->label(false);?>

    <div class="info-block">
        <h4><?= Yii::t('app', 'LEGAL ADDRESS') ?></h4>

        <?= $form->field($model, 'registrationCountryName', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ])->dropDownList(ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name'),[
            'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl(["register/getcountrysheme"]).'",{id:$(this).val()},function(data){
                var companiesCountryname = $("#companies-countryname").val();
                $("#companies-countryname").html(data);
                $("#companies-countryname").val(companiesCountryname);
                $("#companies-countryname").trigger("change");
                }),
                $.post( "'.Yii::$app->urlManager->createUrl(["register/getcountryregion"]).'",{id:$(this).val()},function(data){
                var companiesRegion = $("#companies-region").val();
                $("#companies-region").html(data);
                $("#companies-region").val(companiesRegion);
                })'
        ]) ?>

        <?= $form->field($model, 'region', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ])->dropDownList(ArrayHelper::map(\app\models\Regions::find()->all(), 'id', 'name')) ?>

        <?= $form->field($model, 'locality', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'streetAddress', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'postalCode', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>
    </div>

    <div class="info-block">
        <h4><?= Yii::t('app', 'ABOUT PARTICIPANT') ?></h4>

        <?= $form->field($model, 'countryName', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
            'inputOptions' => [
                'class' => 'form-control',
                'disabled' => 'disabled',
            ],
        ])->dropDownList(ArrayHelper::map((new \app\models\CountrySheme)->find()->where(['country_id'=>1])->all(), 'id', 'name'),[
            'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl(["register/getshemetype"]).'",{ids:$("option:selected",this).attr("type_ids")},function(data){
                var companiesLegaltype = $("#companies-legaltype").val();
                $("#companies-legaltype").html(data);
                $("#companies-legaltype").val(companiesLegaltype);
                })'
        ]) ?>

        <?= $form->field($model, 'LegalType', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
            'inputOptions' => [
                'class' => 'form-control',
                'disabled' => 'disabled',
            ],
        ])->dropDownList(ArrayHelper::map(\app\models\CompanyType::find()->all(), 'id', 'name')) ?>

        <?= $form->field($model, 'customer_type', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ])->dropDownList(ArrayHelper::map(\app\models\CompanyCustomerType::find()->all(), 'id', 'name')) ?>

        <?= $form->field($model, 'legalName', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'legalName_en', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>


        <?= $form->field($model, 'identifier', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
            'validateOnType' => true,
            'validateOnBlur' => true,
            'validateOnChange' => true,
            'inputOptions' => [
                'class' => 'form-control',
                'disabled' => 'disabled',
            ],
        ]) ?>

        <?= $form->field($model, 'mfo', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'bank_account', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'bank_branch', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>

        <?= $form->field($model, 'payer_pdv', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ])->radioList([1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')]) ?>

        <?php
        $model->isDaughter = ($model->parent_identifier) ? 1 : 0;
        $unsuccessfulClass = ($model->payer_pdv)? '' : ' unsuccessful';
        ?>
        <div id="parentBlock" class="<?=$unsuccessfulClass?>">

            <?= $form->field($model, 'isDaughter', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
            ])->radioList([0 => Yii::t('app', 'My company pays VAT on the basis of personal certificates of VAT'), 1 => Yii::t('app', 'My company pays VAT under the VAT certificates of Parent Company')])->label("") ?>

            <?php
            $unsuccessfulClass = ($model->isDaughter)? '' : ' unsuccessful';
            ?>

            <?= $form->field($model, 'parent_identifier', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                'validateOnType' => true,
                'validateOnBlur' => true,
                'validateOnChange' => true,
                'options' => [
                    'class' => 'form-group' .  $unsuccessfulClass,
                ]
            ])?>

        </div>
        <?php
        $unsuccessfulClass = ($model->payer_pdv)? '' : ' unsuccessful';
        ?>
        <?= $form->field($model, 'ipn_id', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
            'validateOnType' => true,
            'validateOnBlur' => true,
            'validateOnChange' => true,
            'options' => [
                'class' => 'form-group' .  $unsuccessfulClass,
            ]
        ])?>
    </div>



    <div class="info-block">
        <h4><?= Yii::t('app', 'AUTHORIZED PERSON DATA (FOR CELEBRATION OF CONTRACT)') ?></h4>
        <?= $form->field($model, 'fio', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>
        <?= $form->field($model, 'userPosition', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>
        <?= $form->field($model, 'userDirectionDoc', [
            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
        ]) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php $this->registerJs('$("body input[type=text]").bind("change", function(){
                            $(this).val($.trim($(this).val()));
                        });'); ?>

<?php $this->registerJs('$("#companies-registrationcountryname").trigger("change");'); ?>
<?php $this->registerJs('function toggleIpnId(){
    if($(this).val() == 1){
        $(".field-companies-ipn_id").slideDown({\'duration\': 300});
        $("#parentBlock").slideDown({\'duration\': 300});
    }
    else{
        $(".field-companies-ipn_id").slideUp({\'duration\': 300});
        $("#parentBlock").slideUp({\'duration\': 300});
    }
}'); ?>
<?php $this->registerJs('function parentIdentifier(){
    if($(this).val() == 1){
        $(".field-companies-parent_identifier").slideDown({\'duration\': 300});
    }
    else{
        $(".field-companies-parent_identifier").slideUp({\'duration\': 300});
    }
}'); ?>
<?php $this->registerJs('$("#companies-payer_pdv input[type=radio]").bind("change", toggleIpnId);'); ?>
<?php $this->registerJs('$("#companies-isdaughter input[type=radio]").bind("change", parentIdentifier);'); ?>