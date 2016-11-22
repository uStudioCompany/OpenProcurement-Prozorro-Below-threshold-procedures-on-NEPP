<?php

/**
 * @var $companyModel \app\models\Companies
 */
//use app\models\TaxPayerType;
?>


<?= !isset($companyModel->mfo) ? $form->field($companyModel, 'mfo', [
    'template' => "{label}\n<div class=\"col-md-6\">{input}{error}</div>\n"
]) : ''?>

<?= !isset($companyModel->bank_account) ? $form->field($companyModel, 'bank_account', [
    'template' => "{label}\n<div class=\"col-md-6\">{input}{error}</div>\n",
]) : ''?>

<?= !isset($companyModel->bank_branch) || $companyModel->bank_branch == '' ? $form->field($companyModel, 'bank_branch', [
    'template' => "{label}\n<div class=\"col-md-6\">{input}{error}</div>\n",
]) : ''?>
<?= $showVATInfo = $companyModel->isNeedCheckPayerVAT() ? !isset($companyModel->payer_pdv) ? $form->field($companyModel, 'payer_pdv')->radioList([1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')]) : '' : ''?>

<?php
$companyModel->isDaughter = ($companyModel->parent_identifier) ? 1 : 0;
$unsuccessfulClass = ($companyModel->payer_pdv)? '' : ' unsuccessful';
if  ($showVATInfo != '') {
    ?>
    <div id="parentBlock" class="form-group<?= $unsuccessfulClass ?>">

        <?= $form->field($companyModel, 'isDaughter', [
            'options' => [
                'class' => '',
            ]
        ])->radioList([
            0 => Yii::t('app', 'My company pays VAT on the basis of personal certificates of VAT'),
            1 => Yii::t('app', 'My company pays VAT under the VAT certificates of Parent Company'),
        ])->label(false) ?>

        <?php
        $unsuccessfulClass = ($companyModel->isDaughter) ? '' : ' unsuccessful';
        ?>
        <div class="clearfix"></div>
        <?= $form->field($companyModel, 'parent_identifier', [
            'validateOnType' => true,
            'validateOnBlur' => true,
            'validateOnChange' => true,
            'options' => [
                'class' => $unsuccessfulClass,
            ],
            'template' => "{label}\n<div class=\"col-md-6\">{input}{error}</div>\n",
        ]) ?>

    </div>
    <?php
    $unsuccessfulClass = ($companyModel->payer_pdv) ? '' : ' unsuccessful';
    ?>
    <?echo $form->field($companyModel, 'ipn_id', [
        'validateOnType' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'options' => [
            'class' => 'form-group' . $unsuccessfulClass,
        ],
        'template' => "{label}\n<div class=\"col-md-6\">{input}{error}</div>\n",
    ]);
    }?>


<?php $this->registerJs('$("body input[type=text]").bind("change", function(){
                            $(this).val($.trim($(this).val()));
                        });'); ?>

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