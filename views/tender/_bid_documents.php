<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var $k int
 * @var $form yii\widgets\ActiveForm
 * @var $doc app\models\tenderModels\Document
 */

//if ($k === 0) {$doc->title='Название документа.PDF';}

?>
<tr>
    <td><a target="_blank" href="<?=$doc->url?>"><?=htmlspecialchars($doc->title)?></a></td>
    <td width="10%">
<!--        <a href="--><?//=$doc->url?><!--"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>-->
    </td>
</tr>

