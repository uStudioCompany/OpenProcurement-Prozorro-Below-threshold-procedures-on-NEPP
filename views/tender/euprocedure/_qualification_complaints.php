<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;



?>
<h4><?= Yii::t('app', 'Оскарження результатів предкваліфікації') ?></h4>
<table class="pq-claim">
<!--
    <thead>
    <tr>
<!--        <th width="60%">--><?//= Yii::t('app', 'Змiст скарги') ?><!--</th>-->
<!--        <th width="20%">--><?//= Yii::t('app', 'Документи скарги') ?><!--</th>-->
<!--        <th width="20%">--><?//= Yii::t('app', 'Статус скарги') ?><!--</th>-->
<!--    </tr>-->
<!--    </thead>-->
    <?php foreach ($qualification->complaints as $c => $complaint) { ?>
        <tr>
            <td class="pq-clam-item"><?= Html::encode($complaint->description) ?>
                <?php
                // эта фича вызывается при отмене квалификации при удовлетворенной жалобе
                if ($complaint->status == 'satisfied') {
                    $form = ActiveForm::begin();

                    echo $form->field($complaint, '[' . $c . ']tendererAction')->textarea();
                    echo Html::hiddenInput('Complaint[' . $c . '][qualification_id]', $qualification['id']);
                    echo Html::hiddenInput('Complaint[' . $c . '][complaint_id]', $complaint['id']);

                    echo Html::submitButton(Yii::t('app', 'Вiдповiсти'), ['class' => 'btn btn-danger btn-submitform', 'name' => 'send_precvalification_complain_resolved_answer']);

                    ActiveForm::end();
                }
                ?>
		<?php

                if (isset($complaint->documents)) {
                    foreach ($complaint->documents as $document) {
                        echo '<p class="pq-clam-item-doc">'. Html::a($document->title, $document->url).'</p>' ;
                    }
                }
                ?>
            </td>
<!--
            <td>
                <?php
                
                if (isset($complaint->documents)) {
                    foreach ($complaint->documents as $document) {
                        echo Html::a($document->title, $document->url) . '<br/>';
                    }
                }
                ?>
            </td>
-->
            <td class="pq-clam-status">
                <?= Yii::t('app', 'qualification_'.$complaint->status) ?><br>
                <?= Html::a('Show',['/seller/tender/prequalification-complaints?id='.$tenders->id.'&prequalification='.$currentQualificationId]) ?>
            </td>
        </tr>
    <?php } ?>
</table>