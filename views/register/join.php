<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form ActiveForm */
?>
<div class="register-join">


    <?php $form = ActiveForm::begin([
        'id' => 'join-form',
    ]); ?>

    <?php if ($join->hasErrors()) { ?>

        <div class="alert alert-danger fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>Error!</strong>
            <?= $form->errorSummary([$join]) ?>
        </div>

    <?php } ?>

    <h1><?= Yii::t('app', 'User items to connection') ?></h1>

    <?= $form->field($join, 'username', [
        'validateOnType' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
    ]) ?>

<!--    --><?//= $form->field($join, 'password')->passwordInput() ?>
<!--    --><?//= $form->field($join, 'confirmPassword')->passwordInput() ?>
    <?= $form->field($join, 'fio') ?>
    <?= $form->field($join, 'phone') ?>
<!--    --><?//= $form->field($join, 'info1')->checkbox() ?>
<!--    --><?//= $form->field($join, 'info2')->checkbox() ?>
<!--    --><?//= $form->field($join, 'info3')->checkbox() ?>
<!--    --><?//= $form->field($join, 'subscribe_status')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- register-join -->


<script type="text/javascript">

    function fieldValidate2(event, attribute, messages) {

        if (attribute.id == 'userjoinrequests-username') {
            var data = {
                'User': {username: $('#userjoinrequests-username').val()},
                'ajax': 'join-form',
                '_csrf': yii.getCsrfToken()
            }
        }
        $.ajax({
            url: "<?= Yii::$app->urlManager->createAbsoluteUrl(['register'])?>",
            type: "POST",
            data: data,
            success: function (msgs) {
//                console.log(msgs);
                if (msgs.username !== undefined && msgs.username[0] !== undefined) {
                    var wrap = $('#userjoinrequests-username').closest('.form-group');
                    wrap.addClass('has-error');
                    wrap.find('.help-block').html(msgs.username[0]);
                }
            },
        });
    }
</script>

<?php //$this->registerJs('$("#registration-form").on("afterValidateAttribute", afterValidateAttribute);'); ?>
<?php $this->registerJs('$("#join-form").on("beforeValidateAttribute", fieldValidate2);'); ?>
