<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\pages\Module;
use vova07\imperavi\Widget as Imperavi;
use yii\helpers\Url;
use app\modules\pages\models\PagesTree;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\Page */
/* @var $form yii\widgets\ActiveForm */
/* @var $folders array app\modules\pages\models\PagesTree */

$form = ActiveForm::begin();
foreach ($folders as $folder) {
    $tab = '-';
    for ($i = 0; $i < $folder->depth; $i++){
        $tab .= $tab;
    }
    $folder->name = $tab . $folder->name;
}
$foldersDD = ArrayHelper::map($folders, 'id', 'name');



echo $form->field($model, 'pt_id')->dropDownList($foldersDD);
echo $form->field($model, 'title')->textInput(['maxlength' => 255]);

echo $form->field($model, 'alias')->textInput(['maxlength' => 255])->label(Yii::t('app', 'URL'));

echo $form->field($model, 'published')->checkbox();

$settings = [
    'minHeight' => 200,
    'plugins' => [
        'fullscreen',
    ],
];
if ($module->imperaviLanguage) {
    $settings['lang'] = $module->imperaviLanguage;
}
if ($module->addImage || $module->uploadImage) {
    $settings['plugins'][] = 'imagemanager';
}
if ($module->addImage) {
    $settings['imageManagerJson'] = Url::to(['images-get']);
}
if ($module->uploadImage) {
    $settings['imageUpload'] = Url::to(['image-upload']);
}
if ($module->addFile || $module->uploadFile) {
    $settings['plugins'][] = 'filemanager';
}
if ($module->addFile) {
    $settings['fileManagerJson'] = Url::to(['files-get']);
}
if ($module->uploadFile) {
    $settings['fileUpload'] = Url::to(['file-upload']);
}
echo $form->field($model, 'content')->widget(Imperavi::className(), [
    'settings' => $settings,
]);

echo $form->field($model, 'content_en')->widget(Imperavi::className(), [
    'settings' => $settings,
]);

echo $form->field($model, 'content_ru')->widget(Imperavi::className(), [
    'settings' => $settings,
]);

echo $form->field($model, 'title_browser')->textInput(['maxlength' => 255]);

//echo $form->field($model, 'meta_keywords')->textInput(['maxlength' => 200]);

echo $form->field($model, 'meta_description')->textInput(['maxlength' => 160]);
?>
<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? Module::t('CREATE') : Module::t('UPDATE'), [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
    ]); ?>
</div>
<?php ActiveForm::end(); ?>
