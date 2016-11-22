<?php
use yii\helpers\Html;


if ($tenders->tender_type == 2) {
    echo '<h4>ЦІНОВА ПРОПОЗИЦІЯ ПО ЛОТАХ</h4>';
    $count = 0;
//    Yii::$app->VarDumper->dump($tender->lots, 10, true);die;
    foreach ($tender->lots as $l => $lot) {
        if ($lot->status == 'cancelled') continue;
        $count++;
        ?>
        <div class="row margin_b_20 panel panel-default">
            <div class="panel-body">

                <?php
                echo 'Загальна сума лоту ' . $lot->value->amount . ' ' . $lot->value->currency;
                echo ' ' . Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$lot->value->valueAddedTaxIncluded]);
                \app\modules\seller\helpers\HBid::getOneBidFieldsConfirm($lot, $bid, $tenders, $form, $count);
                ?>
            </div>
        </div>



        <div class="feature">

                    <?php foreach ($tender->features as $f => $feature) {

//                            if ($f === '__EMPTY_FEATURE__') continue;
//                            Yii::$app->VarDumper->dump(\app\modules\seller\helpers\HBid::getLotItem($tender, $lot->id), 10, true);die;
            $lotItemsArr = \app\modules\seller\helpers\HBid::getLotItem($tender, $lot->id);

            if (($feature->relatedItem != '') && ($lot->id == $feature->relatedItem || in_array($feature->relatedItem, $lotItemsArr))) {
                echo $this->render('_bid_feature', [
                    'form' => $form,
                    'feature' => $feature,
                    'k' => $f,
                    'bid' => $bid,
                    'lot' => $lot
                ]);
            }
        }
                    ?>

</div>
<div class="clearfix"></div>
 <?php   }




} else {
//Yii::$app->VarDumper->dump($bid, 10, true);die;
    echo 'Загальна сума тендеру ';
    echo Html::encode($tender->value->amount . ' ' . $tender->value->currency);
    echo ' ' . Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]);

if (!in_array($tenders->tender_method,['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])) {
    echo $form->field($bid->value, 'amount')
        ->textInput([
            'name' => 'Bid[value][amount]',
            'class' => 'form-control bid_value',
            'disabled' => true
        ])->label(Yii::t('app', 'Цінова пропозиція'));
}

}



//                            выводим фучи тендера
foreach ($tender->features as $f => $feature) {

    if (($feature->featureOf == 'tenderer')) {
        echo '<h4>НЕЦIНОВI ПОКАЗНИКИ, ЩО ВIДНОСЯТЬСЯ ДО ВСЬОГО ТЕНДЕРУ</h4>';
        echo $this->render('_bid_feature', [
            'form' => $form,
            'feature' => $feature,
            'k' => $f,
            'bid' => $bid,
//                                    'lot'=>$lot
        ]);
    }
}
?>

