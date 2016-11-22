<?php

use yii\helpers\Html;
use app\modules\pages\Module;

/* @var $this yii\web\View */
/* @var $model app\modules\pages\models\Page */

$this->title = Module::t('CREATE');
$this->params['breadcrumbs'][] = ['label' => Module::t('MODULE_NAME'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<?= $this->render('_form', [
    'model' => $model,
    'module' => $module,
    'folders' => $folders
]); ?>
