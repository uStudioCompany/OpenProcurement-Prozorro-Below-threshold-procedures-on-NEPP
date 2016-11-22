<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="bids_block">
    <h1><?= Yii::t('app', 'Ваша пропозиція')?></h1>




    <? $form = ActiveForm::begin([
        'id' => 'bid_form'
    ]);
//Yii::$app->VarDumper->dump($bid, 10, true);die;
    if ($bid->status == 'invalid') { ?>
        <div class="bs-example">
            <div class="alert alert-warning fade in">
                <a href="#" class="close"
                   data-dismiss="alert">&times;</a><?= Yii::t('app', 'В умовах тендеру щось змiнилось. Вам необхiдно пiдтвердити свої пропозицiї') ?>
            </div>
        </div>

        <?php
//        Yii::$app->VarDumper->dump(\yii\helpers\Json::decode($userBid)['data'], 10, true);die;

        if($tenders->tender_type == 2) {
            $res['Bid']['lotValues'] = \yii\helpers\Json::decode($userBid)['data']['lotValues'];
        }else{
            $res['Bid'] = json_decode($userBid,1)['data'];
//            var_dump($res);die;
//            Yii::$app->VarDumper->dump($res, 10, true);die;
//            Yii::$app->VarDumper->dump(\app\modules\seller\helpers\HBid::update($res), 10, true);die;
        }

        echo $this->render('_bid_confirm', [
            'form' => $form,
            'tender' => $tender,
            'tenders' => $tenders,
            'bid' => \app\modules\seller\helpers\HBid::update($res)
        ]);
        ?>
        <div class="row submit-buttons">
            <?= Html::submitButton(Yii::t('app', 'Пiдтвердити'), ['class' => 'btn btn-success margin_r_20', 'name' => 'bid_confirm']); ?>
        </div>

<?php
    } else {

        echo $this->render('_bid_bids', [
            'form' => $form,
            'tender' => $tender,
            'tenders' => $tenders,
            'bid' => $bid
        ]);


        echo '<div class="info-block bids_document_block">';
        echo '<h4>' . mb_strtoupper(Yii::t('app', 'Тендерна документація'),'UTF-8') . '</h4>';

        echo \app\models\DocumentUploadTask::GetUploadedDoc($tenders->id, 'bid', ['tender','lot'], [], $tenders->tender_id.'/bids/'.$bid->id);

        //выводим EMPTY_DOC как образец для клонирования
        foreach ($bid->documents as $d => $doc) {
            if ($d === '__EMPTY_DOC__') {
                echo '<div id="hidden_document_original" class="row margin23 panel-body" style="display: none">';
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

        //формируем массив из последних версий файлов.
        $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($bid->documents);
        foreach ($realDocuments as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
//                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
                echo '<div class="row bid_file_wrap oldfile panel panel-default margin_t_20">'. Yii::t('app','old_document').' - <s><i><a href="'. Html::encode($doc->url).'">'. Html::encode($doc->title).'</a></i></s></div>';
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

        //формируем массив из последних версий файлов.
        $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($bid->financialDocuments);
        foreach ($realDocuments as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
//                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
                echo Yii::t('app','old_document').' - <s><i><a href="'. Html::encode($doc->url).'">'. Html::encode($doc->title).'</a></i></s><br/>';
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

        //формируем массив из последних версий файлов.
        $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($bid->eligibilityDocuments);
        foreach ($realDocuments as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
//                echo '<div class=""><s><b>' . $doc->title . '</b></s></div>';
                echo Yii::t('app','old_document').' - <s><i><a href="'. Html::encode($doc->url).'">'. Html::encode($doc->title).'</a></i></s><br/>';
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

        //формируем массив из последних версий файлов.
        $realDocuments = \app\models\tenderModels\Document::getLastVersionDocuments($bid->qualificationDocuments);
        foreach ($realDocuments as $d => $doc) {

            if (isset($DocId) && $DocId == $doc->id) {
//                echo '<div class="margin_l_80"><s><b>' . $doc->title . '</b></s></div>';
                echo Yii::t('app','old_document').' - <s><i><a href="'. Html::encode($doc->url).'">'. Html::encode($doc->title).'</a></i></s><br/>';
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


        ?>
	<div class="row submit-buttons">
	<?
        echo Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success margin_r_20', 'id' => 'submit_bid']);
        if(isset($bid->id) && $bid->id){
            echo Html::submitButton(Yii::t('app', 'Delete'), [
                'onclick' => 'return confirm(\'Удалить ставки?\')',
                'class' => 'btn btn-danger',
                'name' => 'delete_bids'
            ]);
        }
	?>
	</div>
    <div class="spinner"></div>
	<?
    }
    ActiveForm::end();
    //    echo base64_encode('5ec3a39fc7fd4fca833dd2946c66d439:');
    ?>


</div>