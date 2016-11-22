<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

if (Yii::$app->session->hasFlash('message_bid_error')) { ?>
    <div class="bs-example">
        <div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message_bid_error'); ?></div>
    </div>
<?php }

if ($tenders->tender_type == 2) {
    echo '<h4>ЦІНОВА ПРОПОЗИЦІЯ ПО ЛОТАХ</h4>';
    $count = 0;

    foreach ($tender->lots as $l => $lot) {
        if ($lot->status == 'cancelled') continue;
        $count++;
        if(in_array($tenders->tender_method, Yii::$app->params['2stage.tender']) && !\app\models\BidsSearch::checkFirstStageOnBidByCompany($tenders, $lot->id)) {
            continue;
        }
        ?>
        <div class="row margin_b_20 panel panel-default">
            <div class="panel-body">

                <?php
                echo 'Загальна сума лоту ' . $lot->value->amount . ' ' . $lot->value->currency;
                echo ' ' . Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$lot->value->valueAddedTaxIncluded]);

        if(!in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])) {

            if ($bid['id'] != '') {//типа редактирование

                \app\modules\seller\helpers\HBid::getOneBidFields($lot, $bid, $tenders, $form, $count);


            } else {
                //echo '<pre>'; print_r($bid); echo '</pre>';
                echo $form->field($bid->lotValues['__EMPTY_LV__']->value, '[' . $lot->id . ']amount')
                    ->textInput([
                        'name' => 'Bid[lotValues][' . $lot->id . '][value][amount]',
                        'class' => 'form-control bid_value',
                        'data-lid' => $lot->id,
                        'data-tid' => $tenders->id,
                        'data-max' => $lot->value->amount,
                        'data-prev' => $bid->lotValues['__EMPTY_LV__']->value->amount,
                        'data-error' => 0,
                        'data-need_check' => 1,
                    ])
                    ->label($count . Yii::t('app', '. Цінова пропозиція до лоту ') . $lot->title);


                echo '<hr/>';

            }
        }elseif(in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])) {
            echo '<div>';
            echo Html::checkbox('Bid[lotValues][' . $lot->id . '][competitive_lot]',
                \app\modules\seller\helpers\HBid::getOneCompetentiveBidFields($lot, $bid) ? true : false,
                [
                    'label' => Yii::t('app', 'Приймаю участь')
                ]);
            echo '</div>';

        }?>

                <div class="feature">

                    <?php foreach ($tender->features as $f => $feature) {

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

            </div>
        </div>
        <?php
    }


//                            выводим фучи тендера
    foreach ($tender->features as $f => $feature) {

        if (($feature->featureOf == 'tenderer')) {
            echo '<h4>'.Yii::t('app','НЕЦIНОВI ПОКАЗНИКИ, ЩО ВIДНОСЯТЬСЯ ДО ВСЬОГО ТЕНДЕРУ').'</h4>';
            echo $this->render('_bid_feature', [
                'form' => $form,
                'feature' => $feature,
                'k' => $f,
                'bid' => $bid,
//                                    'lot'=>$lot
            ]);
        }
    }


} else {

    echo 'Загальна сума тендеру ';
    echo Html::encode($tender->value->amount . ' ' . $tender->value->currency);
    echo ' ' . Html::encode(\app\models\tenderModels\Value::getPDV()[(int)$tender->value->valueAddedTaxIncluded]);

//    Yii::$app->VarDumper->dump($bid, 10, true, true);
    if(!in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])){
        echo $form->field($bid->value, 'amount')
            ->textInput([
                'name' => 'Bid[value][amount]',
                'class' => 'form-control bid_value',
                'data-lid'=>$bid->id,
                'data-tid'=>$tenders->id,
                'data-max'=>$tender->value->amount,
                'data-prev'=>$bid->value->amount,
                'data-error'=>0,
                'data-need_check'=>1,
            ])->label(Yii::t('app', 'Цінова пропозиція'));


    }elseif(in_array($tenders->tender_method, ['open_competitiveDialogueUA', 'open_competitiveDialogueEU'])){
        echo '<div>';
        echo Html::checkbox('Bid[value][amount]',
            $bid->id != '' && $bid->status == 'pending' ? true : false,
            [
                'label' => Yii::t('app', 'Приймаю участь')
            ]);
        echo '</div>';
    }


    foreach ($tender->features as $f => $feature) {
        if($f == '__EMPTY_PARAMETERS__') continue;

        echo '<h4>НЕЦIНОВI ПОКАЗНИКИ</h4>';
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

