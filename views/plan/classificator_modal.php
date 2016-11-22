<?php
use yii\helpers\Url;

$this->registerJsFile(Url::to('@web/js/classificator.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile(Url::to('@web/css/classificator.css'));
$this->registerCssFile(Url::to('@web/css/spinner.css'));
?>

<!-- classificator modal -->
<div id="classificator-modal"class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="spinner"><div></div></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('app','Класифiкатор')?></h4>
            </div>
            <div class="modal-search">
                <input id="search" class="form-control" type="text" placeholder="Поиск по названию">
            </div>
            <div id="mbody" class="modal-body"></div>
            <div class="modal-footer">
                <div id="selected-item">
                    <p class="selected-code"></p>
                    <p class="selected-name"></p>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=Yii::t('app','close')?></button>
                <button id="btn-ok" type="button" class="btn btn-primary"><?=Yii::t('app','OK')?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->