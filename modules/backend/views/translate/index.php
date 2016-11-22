<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Translate');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a(Yii::t('app', 'Українська'), ['update', 'id' => 'uk'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'English'), ['update', 'id' => 'en'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Русский'), ['update', 'id' => 'ru'], ['class' => 'btn btn-primary']) ?>

</div>
