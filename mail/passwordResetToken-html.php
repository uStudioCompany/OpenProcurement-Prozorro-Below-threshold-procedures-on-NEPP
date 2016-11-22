<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p><?= Yii::t('app', 'Привет').' '. $user->username .' '. Yii::t('app', 'Ну, что? Забыли пароль? Бывает.)')?><br/>
        <?= Yii::t('app', 'Эта ссылка поможет Вам. Просто введите новый пароль.')?>
    </p>
    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p style="color: red;"><?= Yii::t('app', 'И еще. Если Вы не просили востановить свой пароль, то просто закройте это письмо.')?></p>
</div>
