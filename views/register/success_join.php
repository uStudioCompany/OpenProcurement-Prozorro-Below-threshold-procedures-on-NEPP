<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
// use yii\helpers\ArrayHelper;
// use app\models\Countries;

/* @var $this yii\web\View */
/* @var $model app\models\Companies */
/* @var $form ActiveForm */

//$this->title = 'Вiтаємо з успiшною реєстрацiєю!';
//$persLabel = $persons->attributeLabels();
//$companyLabel = $company->attributeLabels();
?>
<div class="register-success">
    <?php

    if (Yii::$app->session->hasFlash('join_success')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('join_success')
        ]);
    }

    if (Yii::$app->session->hasFlash('invate_success')) {
        echo $this->render('../common/flash_success', [
            'data' => Yii::$app->session->getFlash('invate_success')
        ]);
    }

    ?>


</div><!-- register-success -->