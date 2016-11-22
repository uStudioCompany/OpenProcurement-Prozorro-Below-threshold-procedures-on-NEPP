<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = \app\models\Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->legalName;
?>
<div class="tender-request">

    <?=$this->render('/site/head', [
            'title' => $this->title, 
            'descr' => 'Для отримання доступу до редагування ранiше оголошеної на одному з майданчикiв системи ПРОЗОРРО закупiвлi, вам необхдiно надати наступнi даннi'
        ]); 
    ?>

    <?php $form = ActiveForm::begin([
            'validateOnType' => true,
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'labelOptions' => ['class' => 'col-md-3 control-label'],
            ],
        ]); 
    ?>
    <div class="info-block">
        <h4><?=Yii::t('app','Запит на отримання доступу до редагування ранiше оголошеної закупiвлi')?></h4>
        <?php 
            echo $form->field($request, 'tenderID', [
                    'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ]);
            echo $form->field($request, 'migrationCode', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ]);
            echo $form->field($request, 'descr', [
                'template' => "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>"
                ])->textarea(['row' => 6]);            
        ?> 
    </div>
    <div class="form-group">
        <div class="col-md-offset-3 col-md-6">
            <?= Html::submitButton('Запросити доступ', ['class' => 'btn btn-default btn-submitform']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?> 
</div><!-- tender-request -->