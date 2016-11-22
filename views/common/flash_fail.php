<!--<div class="alert alert-danger fade in">-->
<!--    <a href="#" class="close" data-dismiss="alert">&times;</a>-->
<!--    <strong>Error!</strong>-->
<!--    <br/>-->
<!--    --><?////= $data ?>
<!--</div>-->

<?php
if (Yii::$app->session->hasFlash('Forbidden')) { ?>
    <div class="alert alert-danger fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::$app->session->getFlash('Forbidden'); ?>
    </div>
<?php } ?>