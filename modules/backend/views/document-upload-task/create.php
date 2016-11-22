<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DocumentUploadTask */

$this->title = Yii::t('app', 'Create Document Upload Task');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document Upload Tasks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-upload-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
