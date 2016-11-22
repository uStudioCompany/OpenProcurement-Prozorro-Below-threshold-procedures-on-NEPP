<?php
use Yii;
use yii\helpers\Html;
?>
<div>
    <?=Yii::t('app','Перейдiть по');?>
    <?=Html::a(' '. Yii::t('app','посиланню').' ',
        Yii::$app->urlManager->createAbsoluteUrl([
            '/register/confirm',
            'activationcode' => $user->activationcode
        ])) ?>
    , <?=Yii::t('app','щоб пiдтвердити реєстрацiю на сайтi');?>
</div>