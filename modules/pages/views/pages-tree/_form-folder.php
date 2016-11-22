<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\pages\models\PagesTree;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\PagesTree */
/* @var $folders array app\modules\pages\models\PagesTree */
/* @var $form yii\widgets\ActiveForm */
/* @var $pt_id integer ID of parent node*/
/* @var $action string*/
$form = ActiveForm::begin();
foreach ($folders as $folder) {
    $tab = '-';
    for ($i = 0; $i < $folder->depth; $i++) {
        $tab .= $tab;
    }
    $folder->name = $tab . $folder->name;
}
$foldersDD = ArrayHelper::map($folders, 'id', 'name');
?>

<div class="tender-preview m_create-wrap">
    <? if ($action == 'create') : ?>
    <div class="info-block">
        <div class="form-group">
            <label for="pt_id" class="col-md-2"><?= Yii::t('app', 'Folder') ?></label>
            <div class="col-md-10">
                <?= Html::dropDownList('pt_id', $pt_id ? $pt_id : null, $foldersDD, ['id' => 'pt_id', 'class' => 'form-control']); ?>
            </div>
        </div>
    </div>
    <br>
    <? endif; ?>
    <div class="info-block">
        <?= $form->field($model, 'name', ['template' => "<div class=\"col-md-2\">{label}</div><div class=\"col-md-10\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"])->textInput(['maxlength' => 40])->label(Yii::t('app', 'Назва')); ?>
    </div>

    <div class="form-group">
        <div class="col-md-offset-6 col-md-6">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            ]); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
