<?php

use yii\helpers\Html;

?>
<div class="menu-update">

    <h1><?= Html::encode(Yii::t('app','Translate edit').' - '. Yii::$app->request->get('id')) ?></h1>

    <? \yii\widgets\ActiveForm::begin() ?>

    <?= Html::textarea('text', $data, ['class' => 'translate','spellcheck'=>'false']) ?>

    <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>

    <? \yii\widgets\ActiveForm::end() ?>

</div>