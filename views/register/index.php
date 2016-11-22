<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
//use Yii;
use yii\helpers\ArrayHelper;
use app\models\Countries;

/* @var $this yii\web\View */
/* @var $company app\models\Companies */
/* @var $form ActiveForm */

//if ($this->beginCache('register_form',['duration' => Yii::$app->params['cache_time']])) {

$this->title = Yii::t('app', 'Реєстрація на майданчику');
$companyLabel = $company->attributeLabels();
$personsLabel = $persons->attributeLabels();
$userLabel = $user->attributeLabels();
?>
    <style>

    </style>

    <div class="m_registration-wrap">

        <div class="info-hint-box">
            <h1><?= Yii::t('app', 'Реєстрація на майданчику') ?></h1>
            <div class="more-info-box">
                <?= Yii::t('app', 'registration more-info-box')?>
            </div>
        </div>

        <div class="register-index">
            <?= Yii::$app->session->getFlash('success'); ?>

            <?php $form = ActiveForm::begin([
                'validateOnType' => true,
                'enableAjaxValidation' => false,
                'id' => 'registration-form',

                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'labelOptions' => ['class' => 'col-md-3 control-label'],
                ],
            ]); ?>

            <?php if ($company->hasErrors() || $persons->hasErrors() || $user->hasErrors()) { ?>

                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Error!</strong>
                    <?= $form->errorSummary([$company, $persons, $user]) ?>
                </div>

            <?php } ?>

            <?= $form->field($company, 'is_seller', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
            ])->dropDownList([0 => Yii::t('app', 'Buyer'), 1 => Yii::t('app', 'Seller')], [
                'onchange' => 'if(this.value == "1"){ $(".field-companies-customer_type").hide()} else { $(".field-companies-customer_type").show() }'
            ]) ?>

            <div class="info-block">
                <h4><?= Yii::t('app', 'LOGIN AND PASSWORD TO LOGIN') ?></h4>
                <?= $form->field($user, 'username', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
//                'enableAjaxValidation' => true,
                    'validateOnType' => true,
                    'validateOnBlur' => true,
                    'validateOnChange' => true,
                ])->textInput(['placeholder' => $userLabel['username']])?>
                <?= $form->field($user, 'password', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->passwordInput(['placeholder' => $userLabel['password']]) ?>
                <?= $form->field($user, 'confirmPassword', ['template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"])
                    ->passwordInput(['placeholder' => Yii::t('app', 'confirmPassword')]) ?>
            </div>


            <div class="info-block">
                <h4><?= Yii::t('app', 'LEGAL ADDRESS') ?></h4>

                <?= $form->field($company, 'registrationCountryName', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->dropDownList(ArrayHelper::map(\app\models\Countries::find()->all(), 'id', 'name'), [
                    // 'option' => ['' => ['disabled' => "disabled"]],
                    // 'prompt' => $companyLabel['registrationCountryName'],

                    'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl(["register/getcountrysheme"]).'",{id:$(this).val()},function(data){
                var companiesCountryname = $("#companies-countryname").val();
                $("#companies-countryname").html(data);
                $("#companies-countryname option").each(function(){
                    if($(this).val() == companiesCountryname){
//                        $("#companies-legaltype").val(" ");
                        $("#companies-countryname").val(companiesCountryname).change();
                    }
//                    else{
//                        $("#companies-countryname").trigger("change");
//                    }
                });
            }),
            $.post( "'.Yii::$app->urlManager->createUrl(["register/getcountryregion"]).'",{id:$(this).val()},function(data){
                var companiesRegion = $("#companies-region").val();
                $("#companies-region").html(data);
                $("#companies-region option").each(function(){
                    if($(this).val() == companiesRegion){
                        $("#companies-region").val(companiesRegion).change();
                    }
                });
             })'
                ]) ?>

                <?= $form->field($company, 'region', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->dropDownList(ArrayHelper::map(\app\models\Regions::find()->all(), 'id', 'name')) ?>

                <?= $form->field($company, 'locality', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['locality']])  ?>

                <?= $form->field($company, 'streetAddress', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['streetAddress']])  ?>

                <?= $form->field($company, 'postalCode', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['postalCode']]) ?>
            </div>

            <div class="info-block">
                <h4><?= Yii::t('app', 'ABOUT PARTICIPANT') ?></h4>

                <?= $form->field($company, 'countryName', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->dropDownList(ArrayHelper::map((new \app\models\CountrySheme)->find()->where(['country_id' => 1])->all(), 'id', 'name'), [

                    'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl(["register/getshemetype"]).'",
            {
                ids: $("#companies-countryname option:selected").attr("type_ids"),
            },
            function(data){
                var companiesLegaltype = $("#companies-legaltype").val();
                $("#companies-legaltype").html(data);
                $(data).each(function(){
                    if($(this).val() == companiesLegaltype){
                        $("#companies-legaltype").val(companiesLegaltype);
                    }
                });
            })'
                ])->label(Yii::t('app', 'Схема реєстрації')) ?>

                <?= $form->field($company, 'LegalType', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->dropDownList(ArrayHelper::map(\app\models\CompanyType::find()->all(), 'id', 'name')) ?>
                <?= $form->field($company, 'legalName', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['legalName']]) ?>
                <?= $form->field($company, 'legalName_en', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['legalName_en']]) ?>
<!--                --><?//= $form->field($company, 'customer_type', [
//                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
//                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
//                ])->dropDownList(ArrayHelper::map(\app\models\CompanyCustomerType::find()->all(), 'id', 'name')) ?>

                <?= Html::hiddenInput('Companies[customer_type]', 'general'); ?>
                <?= $form->field($company, 'identifier', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
//                'enableAjaxValidation' => true,
                    'validateOnType' => true,
                    'validateOnBlur' => true,
                    'validateOnChange' => true,
                ])->textInput(['placeholder' => $companyLabel['identifier']]) ?>
            </div>

            <div class="info-block">
                <h4><?= Yii::t('app', 'БАНКІВСЬКІ РЕКВІЗИТИ') ?></h4>
                <?= $form->field($company, 'mfo', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['mfo']]) ?>
                <?= $form->field($company, 'bank_account', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['bank_account']]) ?>
                <?= $form->field($company, 'bank_branch', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['bank_branch']]) ?>
            </div>

            <div class="info-block show_is_seller hidden">
                <h4><?= Yii::t('app', 'ДОДАТКОВІ ВІДОМОСТІ') ?></h4>
                <div id="ur_os">
                    <?= $form->field($company, 'haveFinLicense', [
                        'labelOptions' => [
                            'class' => 'col-md-3 control-label unfix',
                            'style' => 'padding-top: 0',
                        ],
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                    ])->radioList([1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')]) ?>
                    <?php
                    $unsuccessfulClass = ($company->fin_license)? '' : ' unsuccessful';
                    echo $form->field($company, 'fin_license', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                        'options' => [
                            'class' => 'form-group' .  $unsuccessfulClass,
                        ]
                    ])->textInput(['placeholder' => $companyLabel['fin_license']])?>
                </div>
                <div id="pdv_pay">
                    <?= $form->field($company, 'payer_pdv', [
                        'labelOptions' => [
                            'class' => 'col-md-3 control-label unfix',
                            'style' => 'padding-top: 0',
                        ],
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                    ])->radioList([1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')])->label(Yii::t('app', 'Платник ПДВ')) ?>

                    <?php
                    $unsuccessfulClass = ($company->payer_pdv)? '' : ' unsuccessful';
                    ?>
                    <div id="parentBlock" class="<?=$unsuccessfulClass?>">

                        <?= $form->field($company, 'isDaughter', [
                            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                        ])->radioList([0 => Yii::t('app', 'My company pays VAT on the basis of personal certificates of VAT'), 1 => Yii::t('app', 'My company pays VAT under the VAT certificates of Parent Company')])->label("") ?>

                        <?php
                        $unsuccessfulClass = ($company->isDaughter)? '' : ' unsuccessful';
                        ?>

                        <?= $form->field($company, 'parent_identifier', [
                            'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                            'validateOnType' => true,
                            'validateOnBlur' => true,
                            'validateOnChange' => true,
                            'options' => [
                                'class' => 'form-group' .  $unsuccessfulClass,
                            ]
                        ])->textInput(['placeholder' => $companyLabel['parent_identifier']])?>

                    </div>
                    <?php
                    $unsuccessfulClass = ($company->payer_pdv)? '' : ' unsuccessful';
                    ?>
                    <?= $form->field($company, 'ipn_id', [
                        'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>",
                        'validateOnType' => true,
                        'validateOnBlur' => true,
                        'validateOnChange' => true,
                        'options' => [
                            'class' => 'form-group' .  $unsuccessfulClass,
                        ]
                    ])->textInput(['placeholder' => $companyLabel['ipn_id']])?>
                </div>
                <?= $form->field($company, 'passport_data', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['passport_data']]) ?>
            </div>
            <div class="info-block">
                <h4><?= Yii::t('app', 'AUTHORIZED PERSON DATA (FOR CELEBRATION OF CONTRACT)') ?></h4>
                <?= $form->field($company, 'fio', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['fio']]) ?>
                <?= $form->field($company, 'userPosition', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['userPosition']]) ?>
                <?= $form->field($company, 'userDirectionDoc', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $companyLabel['userDirectionDoc']]) ?>
            </div>


            <div class="info-block">
                <h4><?= Yii::t('app', 'AUTHORIZED CONTACT PERSON') ?></h4>
                <?= $form->field($persons, 'userName', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['userName']]) ?>
                <?= $form->field($persons, 'userSurname', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['userSurname']]) ?>
                <?= $form->field($persons, 'userPatronymic', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['userPatronymic']]) ?>

                <?= $form->field($persons, 'userName_en', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['userName_en']]) ?>
                <?= $form->field($persons, 'userSurname_en', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['userSurname_en']]) ?>

                <?= $form->field($persons, 'email', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['email']]) ?>

                <?= $form->field($persons, 'telephone', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+38(999)999-99-99',
                    'options' => [
                        'class' => 'form-control tel_input',
                    ],
                    'clientOptions' => [
                        'clearIncomplete' => true
                    ]
                ])->textInput(['placeholder' => $personsLabel['telephone']]) ?>

                <?= $form->field($persons, 'faxNumber', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+38(999)999-99-99',
                    'options' => [
                        'class' => 'form-control tel_input',
                    ],
                    'clientOptions' => [
                        'clearIncomplete' => true
                    ]
                ])->textInput(['placeholder' => $personsLabel['faxNumber']]) ?>

                <?= $form->field($persons, 'mobile', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '+38(999)999-99-99',
                    'options' => [
                        'class' => 'form-control tel_input',
                        'placeholder'=>''
                    ],
                    'clientOptions' => [
                        'clearIncomplete' => true
                    ]
                ])->textInput(['placeholder' => $personsLabel['mobile']]) ?>

                <?= $form->field($persons, 'url', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textInput(['placeholder' => $personsLabel['url']]) ?>
                <?= $form->field($persons, 'availableLanguage', [
                    'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->dropDownList(ArrayHelper::map(\app\models\Languages::find()->orderBy(['id' => SORT_DESC])->all(), 'id', 'name')) ?>

            </div>

            <div class="info-block">
                <?= $form->field($user, 'info1', [
                    'template' => "{label}\n<div class=\"col-md-offset-3 col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->checkbox() ?>
                <?= $form->field($user, 'info2', [
                    'template' => "{label}\n<div class=\"col-md-offset-3 col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->checkbox() ?>
                <?= $form->field($user, 'info3', [
                    'template' => "{label}\n<div class=\"col-md-offset-3 col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->checkbox() ?>
                <?= $form->field($user, 'subscribe_status', [
                    'template' => "{label}\n<div class=\"col-md-offset-3 col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->checkbox() ?>
            </div>


            <div class="form-group">
                <div class="col-md-offset-3 col-md-6">
                    <?= Html::submitButton(Yii::t('app', 'Register'), ['class' => 'btn btn-default btn-submitform']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div><!-- register-index -->


    <!--    Форма для предзаполнения данных для присоеденения к организации-->

    <form method="post" action="<?= Yii::$app->urlManager->createAbsoluteUrl(['/register/join']) ?>" id="join_form">
        <input id="key_csrf" type="hidden" value="" name="_csrf">
        <input type="hidden" name="UserJoinRequests[username]">
        <input type="hidden" name="UserJoinRequests[password]" class="form-control">
        <input type="hidden" name="UserJoinRequests[confirmPassword]" class="form-control">
        <input type="hidden" name="UserJoinRequests[_joinToIdentifier]">
    </form>


    <script type="text/javascript">

        function fieldValidate(event, attribute, messages) {
            if (attribute.id == 'companies-identifier') {
                $('.joinToCompanyWrap').remove();
                var reqField = true;
                var data = {
                    'Companies': {identifier: $('#companies-identifier').val(), is_seller: $('#companies-is_seller').val()},
//                    'Companies': {is_seller: $('#companies-is_seller').val()},
                    'ajax': 'registration-form',
                    '_csrf': yii.getCsrfToken()
                }
            } else if (attribute.id == 'user-username') {
                var reqField = true;
                var data = {
                    'User': {username: $('#user-username').val()},
                    'ajax': 'registration-form',
                    '_csrf': yii.getCsrfToken()
                }
            }
            if (reqField) {
                $.ajax({
                    url: $('#registration-form').attr('action'),
                    type: "POST",
                    data: data,
                    success: function (msgs) {
                        if (msgs.identifier !== undefined && msgs.identifier[0] !== undefined) {
                            var wrap = $('#companies-identifier').closest('.form-group');
                            wrap.addClass('has-error');
                            wrap.find('.help-block').html(msgs.identifier[0]);

                            var val = $('#registration-form input#companies-identifier').val();
                            console.log($('#companies-legaltype option:selected').attr('code_length'));
                            if (val.length == $('#companies-legaltype option:selected').attr('code_length') && val.match(/^\d+$/)) {
                                $('.field-companies-identifier').find('.help-block').prepend('<button class="btn btn-default btn-submitform joinToCompanyWrap" type="button" id="joinToCompany"><?=Yii::t('app', 'Send a request to join') ?></button>');
                            }

                        } else if (msgs.username !== undefined && msgs.username[0] !== undefined) {
                            var wrap = $('#user-username').closest('.form-group');
                            wrap.addClass('has-error');
                            wrap.find('.help-block').html(msgs.username[0]);
                        }
                    },
                });
            }
        }

    </script>


<?php $this->registerJs('function toggleFinLicense(){
    if($(this).val() == 1){
        $(".field-companies-fin_license").slideDown({\'duration\': 300});
    } else {
        $(".field-companies-fin_license").slideUp({\'duration\': 300});
    }
}'); ?>
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
<?php $this->registerJs('function hideFin(){
    //3 = фізична особа
    companiesLegalType = $("#companies-legaltype").val();
    var $radios = $(\'input:radio[name="Companies[haveFinLicense]"]\');
    if(companiesLegalType == 3 || companiesLegalType == 2){
        if($radios.is(\':checked\') === false) {
            $radios.filter(\'[value=0]\').prop(\'checked\', true);
        }
        $(".field-companies-havefinlicense").slideUp({\'duration\': 300});
        $(".field-companies-fin_license").slideUp({\'duration\': 300});
        $(".field-companies-passport_data").slideDown({\'duration\': 300});
        $(\'input[name="Companies[identifier]"\').attr("placeholder", "ІПН");
        $(\'input[name="Companies[legalName]"\').attr("placeholder", "ПІБ Особи");
        $(\'input[name="Companies[legalName_en]"\').attr("placeholder", "Personal name");
    } else {
        $(\'input:radio[name="Companies[haveFinLicense]"]\').prop(\'checked\', false);
        $(".field-companies-havefinlicense").slideDown({\'duration\': 300});
        $(".field-companies-passport_data").slideUp({\'duration\': 300});
        $radios.filter(\'[value=0]\').prop(\'checked\', false);
        $(\'input[name="Companies[identifier]"\').attr("placeholder", "Код ЄРДПОУ");
        $(\'input[name="Companies[legalName]"\').attr("placeholder", "Назва юридичної особи");
        $(\'input[name="Companies[legalName_en]"\').attr("placeholder", "Company name in English");
    }
    if(companiesLegalType == 3){
        $(".field-companies-payer_pdv").slideUp({\'duration\': 300});
    } else {
        $(".field-companies-payer_pdv").slideDown({\'duration\': 300});
    }
    
}'); ?>
<?php //$this->registerJs('$("#registration-form").on("afterValidateAttribute", afterValidateAttribute);'); ?>
<?php $this->registerJs('$("#companies-havefinlicense input[type=radio]").bind("change", toggleFinLicense);'); ?>
<?php $this->registerJs('$("#registration-form").on("beforeValidateAttribute", fieldValidate); $("#companies-registrationcountryname").trigger("change");'); ?>
<?php $this->registerJs('$("#companies-is_seller").on("change", function(){if ($(this).val() == 0) {$(".show_is_seller").addClass("hidden");} else { $(".show_is_seller").removeClass("hidden"); hideFin();}}); '); ?>
<?php $this->registerJsFile(Url::to('@web/js/project.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']); ?>
<?php $this->registerJs('$("#companies-payer_pdv input[type=radio]").bind("change", toggleIpnId);'); ?>
<?php $this->registerJs('$("#companies-isdaughter input[type=radio]").bind("change", parentIdentifier);'); ?>
<?php $this->registerJs('$("body").on("change", "#companies-legaltype", function(){hideFin()});'); ?>
<?php $this->registerJs('hideFin()'); ?>


<?php
//$this->endCache();  }
?>