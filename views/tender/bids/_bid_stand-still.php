<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="bids_block">
    <h1>Таблиця предквалiфiкацi</h1>

    <?php
    Yii::$app->VarDumper->dump($tender->qualifications, 10, true);die;
    ?>


    <? $form = ActiveForm::begin([
        'id' => 'bid_form'
    ]);

    if ($bid->status == 'invalid') { ?>
        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'В умовах тендеру щось змiнилось. Вам необхiдно пiдтвердити своъ пропозицiъ') ?>
            </div>
        </div>

        <?php
        $res['Bid']['lotValues'] = \yii\helpers\Json::decode($userBid)['data']['lotValues'];

        echo $this->render('_bid_confirm', [
            'form' => $form,
            'tender' => $tender,
            'tenders' => $tenders,
            'bid' => \app\modules\seller\helpers\HBid::update($res)
        ]);
        echo Html::submitButton(Yii::t('app', 'Пiдтвердити'), ['class' => 'btn btn-success margin_r_20', 'name' => 'bid_confirm']);

    } else {

        echo $this->render('_bid_bids', [
            'form' => $form,
            'tender' => $tender,
            'tenders' => $tenders,
            'bid' => $bid
        ]);


        echo '<div class="info-block document_block">';
        echo '<h4>ТЕНДЕРНА ДОКУМЕНТАЦIЯ</h4>';

        //выводим EMPTY_DOC как образец для клонирования
        foreach ($bid->documents as $d => $doc) {
            if ($d === '__EMPTY_DOC__') {
                echo '<div id="hidden_document_original"class="row margin23 panel-body" style="display: none">';
                echo $this->render('_bid_document', [
                    'form' => $form,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => ''
                ]);
                echo '</div>';
                unset($bid->documents[$d]);
            }
        }


        foreach (array_reverse($bid->documents) as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
            } else {
                $DocId = $doc->id;

                echo $this->render('_bid_document', [
                    'form' => $form,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => ''
                ]);
            }
        }


        foreach (array_reverse($bid->financialDocuments) as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
            } else {
                $DocId = $doc->id;

                echo $this->render('_bid_document', [
                    'form' => $form,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => ''
                ]);
            }
        }

        foreach (array_reverse($bid->eligibilityDocuments) as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
            } else {
                $DocId = $doc->id;

                echo $this->render('_bid_document', [
                    'form' => $form,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => ''
                ]);
            }
        }

        foreach (array_reverse($bid->qualificationDocuments) as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
                echo '<div class="margin_l_80"><s><b>' . $doc->title . '</b></s></div>';
            } else {
                $DocId = $doc->id;

                echo $this->render('_bid_document', [
                    'form' => $form,
                    'tender' => $tender,
                    'tenders' => $tenders,
                    'documents' => $doc,
                    'k' => $d,
                    'lot_items' => [],
                    'currentLotId' => ''
                ]);
            }
        }


        echo '</div><a role="button" class="btn btn-success col-md-2 uploadfile" href="javascript:void(0)">Yii::t("app", "add file")</a>
                        <div class="clearfix margin_b_20"></div>';




        echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success margin_r_20']);
        echo Html::submitButton(Yii::t('app', 'Delete'), [
            'onclick' => 'return confirm(\'Удалить ставки?\')',
            'class' => 'btn btn-danger',
            'name' => 'delete_bids'
        ]);
    }
    ActiveForm::end();
    //    echo base64_encode('5ec3a39fc7fd4fca833dd2946c66d439:');
    ?>


</div>