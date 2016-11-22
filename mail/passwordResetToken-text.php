<?php
/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<p><?= Yii::t('app', 'Привет').' '. $user->username .' '. Yii::t('app', 'Ну, что? Забыли пароль? Бывает.)')?><br/>
        <?= Yii::t('app', 'Эта ссылка поможет Вам. Просто введите новый пароль.')?>
    </p>
<br/>
<?= $resetLink ?>
