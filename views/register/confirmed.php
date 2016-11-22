<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app','Органiзацiю iдентифiковано!');
?>
<div class="register-confirmed">

    <?=$this->render('/site/head', [
            'title' => $this->title, 
            'descr' => Yii::t('app','Теперь ви маєте можливiсть працювати iз ранiше створенними тендерами та подавати новi оголошення про закупiвлi')
        ]); 
    ?>

    <div class="col-md-6"> 
        <div class="btn btn-default main-action">
            <a href="<?=Url::to('/tender/request') ?>"><?=Yii::t('app','Запросити доступ до оголошеної закупiвлi')?></a>
        </div>
    </div>
    <div class="col-md-6"> 
        <div class="btn btn-default  main-action">
            <a class="center-block" href="<?=Url::to('/tender/create') ?>"><?=Yii::t('app','Оголосити нову закупiвлю')?></a>
        </div>
    </div>
</div><!-- register-confirmed -->