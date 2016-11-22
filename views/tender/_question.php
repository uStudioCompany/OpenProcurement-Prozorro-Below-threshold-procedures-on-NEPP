<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = isset(Yii::$app->user->identity) ? \app\models\Companies::findOne(['id' => Yii::$app->user->identity->company_id])->legalName : Yii::t('app', 'questions');
echo $this->render('small_info_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
$isTenderOwner = \app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders);
?>




    <div class="tender-questions wrap-questions">

        <?php
        echo $this->render('/site/head', [
//            'title' => Html::encode($this->title),
            'descr' => 'Питання'
        ]);
        $template = "{label}\n<div class=\"col-md-6\">{input}</div>{error}";
        ?>

        <?php if (Yii::$app->session->hasFlash('message')) { ?>
            <div class="bs-example">
                <div class="alert alert-success fade in">
                    <a href="#" class="close"
                       data-dismiss="alert">&times;</a><?= Yii::$app->session->getFlash('message'); ?>
                </div>
            </div>
        <?php } ?>


        <?php
        if (\app\models\Tenders::CheckAllowedQuestionStatus($tenders->status, $tenders->tender_method) && (strtotime(str_replace('/','.',$tender->enquiryPeriod->endDate)) > strtotime('now')) && !$isTenderOwner && \app\models\Companies::getCompanyBusinesType() != '') {
            $form = ActiveForm::begin([

                'options' => [
                    'class' => 'form-horizontal',
                    'enctype' => 'multipart/form-data'
                ],
                'fieldConfig' => [
                    'labelOptions' => ['class' => 'col-md-3 control-label'],
                ],
            ]);

            echo $form->field($question, 'title', ['template' => $template])->textInput();
            echo $form->field($question, 'description', ['template' => $template])->textarea();
            echo $form->field($question, 'questionOf', ['template' => $template])->dropDownList(\app\models\tenderModels\Question::getSellerQuestionOf($tender),
                ['class' => 'form-control']);
            ?>

            <div class="col-md-offset-3 col-md-6">

                <?= Html::submitButton(Yii::t('app', 'Подати запитання'), [
                    'class' => 'btn btn-default btn_submit_question',
                    'name' => 'question_submit'
                ]);

                ActiveForm::end();

                ?>

            </div>
        <?php } elseif (!$isTenderOwner) { ?>
            <div class="bs-example">
                <div class="alert alert-warning fade in">
                    <a href="#" class="close"
                       data-dismiss="alert">&times;</a><?= Yii::t('app', 'Прийняття запитань заблоковано.') ?>
                </div>
            </div>

        <?php } ?>

        <div class="clearfix margin_b"></div>


        <?php foreach ($tender->questions as $q => $question) {
            if ($q === 'iClass') continue;
            if ($question->title == '') continue;
            $form = ActiveForm::begin([

                'options' => [
                    'class' => 'form-horizontal',
                    'enctype' => 'multipart/form-data'
                ],
                'fieldConfig' => [
                    'labelOptions' => ['class' => 'col-md-3 control-label'],
                ],
            ]);

            ?>
            <div class="questions margin_b panel panel-default" tid="questions.date">
                <?= $question->date ?> -


                <i><?=Yii::t('app','Запитання до ')?>
                    <?php


                    if ($question->questionOf == 'tender') {
                        echo ' '.Yii::t('app','закупiвлi').': ';
                    } else if ($question->questionOf == 'lot') {
                        foreach ($tender['lots'] as $l => $lot) {
                            if ($l === 'iClass') continue;
                            if ($l === '__EMPTY_LOT__') continue;
                            if ($question->relatedItem == $lot['id']) {
                                echo ' лоту ' . $lot['title'] . ': ';
                            }
                        }
                    } else if ($question->questionOf == 'item') {
                        foreach ($tender['items'] as $i => $item) {
                            if ($i === 'iClass') continue;
                            if ($i === '__EMPTY_ITEM__') continue;
                            if ($question->relatedItem == $item['id']) {
                                echo ' товару ' . $item['description'] . ': ';
                            }
                        }
                    }
                    ?>
                </i>


                <h4 tid="questions.title"><?= Html::encode($question->title) ?></h4>
                <i tid="questions.description"><?= Html::encode($question->description) ?></i>

                <?php if (\app\models\Tenders::CheckAllowedAnwerStatus($question->answer, $tenders->tender_method, $tenders->status)) {

                    if (\app\models\Companies::getCompanyBusinesType() == 'buyer' && $isTenderOwner) {


                        echo $form->field($question, '[' . $q . ']answer', ['template' => $template])->textarea(
                            [
                                'name' => 'Tender[0][answer]'
                            ])->label(false);

                        echo $form->field($question, '[' . $q . ']id', ['template' => $template])->hiddenInput(
                            [
                                'name' => 'Tender[0][id]'
                            ])->label(false);

                        echo Html::submitButton(Yii::t('app', 'Надати вiдповiдь'), [
                            'class' => 'btn btn-default btn_submit_question',
                            'name' => 'answer_question_submit'
                        ]);
                    }

                } else { ?>

                    <div class="answer">
                        <h4><?= Yii::t('app', 'Вiдповiдь:') ?></h4>
                        <h4 tid="questions.answer"><?= Html::encode($question->answer) ?></h4>
                    </div>

                <?php } ?>
            </div>


            <?php
            ActiveForm::end();
        }
        ?>

    </div>

<?php

echo $this->render('view/_nav_block', [
    'tender' => $tender,
    'tenders' => $tenders
]);
?>