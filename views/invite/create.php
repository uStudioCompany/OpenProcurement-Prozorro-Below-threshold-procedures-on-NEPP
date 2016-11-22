<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Invite */

$this->title = Yii::t('app', 'Create Invite');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
