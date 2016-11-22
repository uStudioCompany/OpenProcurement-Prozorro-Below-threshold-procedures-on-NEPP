<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Invoice */
/* @var $form yii\widgets\ActiveForm */

$readOnly = \app\models\Companies::getCompanyByUserLogin(Yii::$app->user->identity->username)->status ? false : true;
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'id' => 'user-update-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label unfix'],
        ],
    ]) ?>


    <?php
    if (!$readOnly) {
        echo $form->field($model, 'amount', [
            'template' => "{label}\n<div class='col-md-6'>{input}{error}</div>\n<div class='col-md-3'>" . Html::submitButton(Yii::t('app', 'Сформувати'), ['class' => 'btn btn-success']) . "</div>",
        ])->label(Yii::t('app', 'Сума поповнення'))->textInput();
    }
    ?>

    <?php
    if (is_object($companyModel)):?>
        <?= $this->render('_formBankDetails', [
            'companyModel' => $companyModel,
            'form' => $form,
        ]) ?>
    <?php endif; ?>
    <?php
    if ($readOnly) {
        echo Html::hiddenInput("Invoice[amount]", 10.00);
        echo "<div class='pull-right'>" . Html::submitButton(Yii::t('app', 'Сформувати'), ['class' => 'btn btn-success']) . "</div>";
    }
    ?>
    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('$(\'#invoice-amount\').prop(\'hidden\', true);');