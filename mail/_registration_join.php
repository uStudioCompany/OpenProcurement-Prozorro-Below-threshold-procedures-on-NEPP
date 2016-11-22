<?php
use yii\helpers\Html;
?>


<div>
    <?=Yii::t('app','Користувач')?><br/>
    <?=Yii::t('app','Name')?>: <?=$user->fio?><br/>
    <?=Yii::t('app','Phone')?>:<?=$user->phone?><br/>
    <?=Yii::t('app','хоче приєднатися до Вашої команiї. Для пiдтвердження приєднання Вам необхiдно перейти по')?>
    <?=Html::a(' '. Yii::t('app','посиланню').' ',
        Yii::$app->urlManager->createAbsoluteUrl([
            '/register/joinconfirm',
            'activationcode' => $user->activationcode,
        ])) ?>
</div>