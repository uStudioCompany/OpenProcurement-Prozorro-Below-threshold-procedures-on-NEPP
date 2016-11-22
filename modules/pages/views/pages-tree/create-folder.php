<?php

use yii\helpers\Html;
use app\modules\pages\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\PagesTree */
/* @var $folders array app\modules\pages\models\PagesTree */

$this->title = Yii::t('app', 'Create folder');
$this->params['breadcrumbs'][] = ['label' => Module::t('MODULE_NAME'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<?= $this->render('_form-folder', [
    'model' => $model,
    'folders' => $folders,
    'pt_id' => $pt_id,
    'action' => $action
]); ?>
