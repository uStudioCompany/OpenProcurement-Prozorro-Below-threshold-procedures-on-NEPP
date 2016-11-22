<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

$this->title = 'Завод iменi Пупкiна';
$fieldLabel = $tender->attributeLabels();
?>
<div class="tender-preview">

    <?php
    echo $this->render('/site/head', [
        'title' => $this->title,
        'descr' => 'Створення нової закупiвлi'
    ]);
    ?>



    <?php

    //    VarDumper::dump($tender, 10, true);

    $form = ActiveForm::begin([
        'validateOnType' => true,
        'options' => ['class' => 'form-horizontal'],
        'id' => 'tender_simple_create',
//        'enableClientValidation' => false,
//        'enableAjaxValidation' => false,
        'fieldConfig' => [
            'labelOptions' => ['class' => 'col-md-3 control-label'],
        ],
    ]);

    //echo $form->errorSummary([$tender, $tender->enquiryPeriod, $tender->tenderPeriod, $tender->items[0]]);


    $template = "{label}\n<div class=\"col-md-6\">{input}</div>\n<div class=\"col-md-3\">{error}</div>";

    ?>

    <div class="info-block">
        <h4>Параметри закупiвлi</h4>

        <div class="form-group">
            <label class="col-md-3 control-label">Тип оголошення</label>

            <div class="col-md-6">
                <select class="form-control">
                    <option selected value="Простая закупка">Простая закупка</option>
                    <!--                    <option value="Многопредметная закупка">Многопредметная закупка</option>-->
                    <!--                    <option value="Мультилотовая закупка<">Мультилотовая закупка</option>-->
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">Нецiновi показники</label>

            <div class="col-md-6">
                <select class="form-control">
                    <option value="Да">Да</option>
                    <option selected value="Нет">Нет</option>
                </select>
            </div>
        </div>

        <?php
        $sel = '
            <div class="col-md-3">
                <select id="tender_type" class="form-control" name="Tender[value][valueAddedTaxIncluded]">
                            <option value="1">З урахуванням ПДВ</option>
                            <option value="0">Без урахування ПДВ</option>
                        </select>
            </div>';
        echo $form->field($tender->value, 'amount', [
            'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n" . $sel . "\n<div class=\"col-md-3\">{error}</div>"])
            ->textInput(['placeholder' => $tender->value->currency, 'name' => 'Tender[value][amount]'])
            ->label($fieldLabel['value']); ?>
        <input type="hidden" name="Tender[value][currency]" value="UAH">
        <?php
        $tenderLabels = $tender->attributeLabels();
        echo $form->field($tender->minimalStep, 'amount', ['template' => $template])
            ->textInput(['placeholder' => $tender->value->currency, 'name' => 'Tender[minimalStep][amount]'])
            ->label($tenderLabels['minimalStep']);
        ?>
    </div>

    <div class="info-block">
        <h4>Загальна iнформацiя про закупiвлю</h4>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#ua" data-toggle="tab">Українською</a></li>
            <li><a href="#ru" data-toggle="tab">По-русски</a></li>
            <li><a href="#en" data-toggle="tab">In English</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="ua">
                <?= $form->field($tender, 'title', ['template' => $template])->textInput(['name' => 'Tender[title]']) ?>
                <?= $form->field($tender, 'description', ['template' => $template])->textarea(['name' => 'Tender[description]']) ?>


                <div class="info-block">
                    <h4>Специфiкацiя закупiвлi</h4>

                    <p>Надайте iнформацiю щодо предметiв закупiлi, якi Ви маєте намiр прибдати в рамках даного
                        оголошення</p>


                    <div class="info-block lot-item">

                        <div class="row">
                            <div class="col-md-3"><strong>Предмет закупiвлi</strong></div>
                        </div>


                        <?php
                        foreach ($tender->items as $k => $item) {
                            if ($k === 'iClass') continue;
                            ?>



                            <?= $form->field($tender->items[$k], 'description', ['template' => $template])
                                ->textarea(['name' => 'Tender[items][' . $k . '][description]']) ?>

                            <?php
                            $sel = '
                            <div class="col-md-3">' .
                                Html::dropDownList('Tender[items][' . $k . '][unit][code]', null, ArrayHelper::map(\app\models\Unit::find()->all(), 'id', ['name']), ['class' => 'form-control'])
                                . '

                            </div>';
                            echo $form->field($tender->items[$k], 'quantity', [
                                'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n" . $sel . "\n<div class=\"col-md-3\">{error}</div>"
                            ])->textInput(['name' => 'Tender[items][' . $k . '][quantity]']) ?>

                            <div class="row">
                                <div class="col-md-3"><span>Класифiкацiя згiдно CPV</span></div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><span>Класифiкацiя згiдно ДКПП</span></div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><strong>Доставка</strong></div>
                            </div>

                            <?= $form->field($tender->items[$k]->deliveryAddress, 'countryName', ['template' => $template])
                            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][countryName]']) ?>
                            <?= $form->field($tender->items[$k]->deliveryAddress, 'region', ['template' => $template])
                            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][region]']) ?>
                            <?= $form->field($tender->items[$k]->deliveryAddress, 'locality', ['template' => $template])
                            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][locality]']) ?>
                            <?= $form->field($tender->items[$k]->deliveryAddress, 'streetAddress', ['template' => $template])
                            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][streetAddress]']) ?>
                            <?= $form->field($tender->items[$k]->deliveryAddress, 'postalCode', ['template' => $template])
                            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][postalCode]']) ?>

                            <?= $form->field($tender->items[$k]->deliveryDate, 'endDate', ['template' => $template])
                            ->textInput([
                                'name' => 'Tender[items][' . $k . '][deliveryDate][endDate]',
                                'class' => 'form-control picker'
                            ]) ?>
                            <hr/>
                        <?php } ?>
                        <!--                        <a href="#">Добавить позицию</a>-->
                    </div>
                    <hr/>


                    <div class="info-block">
                        <h4>Дати та термiни</h4>
                        <?php
                        //echo '<div class="row"><div class="col-md-9"><strong>' . $fieldLabel['enquiryPeriod'] . '</strong></div></div>';
                        echo $form->field($tender->enquiryPeriod, 'startDate', ['template' => $template])
                            ->textInput([
                                'name' => 'Tender[enquiryPeriod][startDate]',
                                'class' => 'form-control picker'
                            ]);

                        echo $form->field($tender->enquiryPeriod, 'endDate', ['template' => $template])
                            ->textInput([
                                'name' => 'Tender[enquiryPeriod][endDate]',
                                'class' => 'form-control picker'
                            ]);

                        //echo '<div class="row"><div class="col-md-9"><strong>' . $fieldLabel['tenderPeriod'] . '</strong></div></div>';

                        echo $form->field($tender->tenderPeriod, 'startDate', ['template' => $template])
                            ->textInput([
                                'name' => 'Tender[tenderPeriod][startDate]',
                                'class' => 'form-control picker'
                            ]);
                        echo $form->field($tender->tenderPeriod, 'endDate', ['template' => $template])
                            ->textInput([
                                'name' => 'Tender[tenderPeriod][endDate]',
                                'class' => 'form-control picker'
                            ]);

                        ?>
                    </div>
                    <div class="info-block">
                        <h4>Контактна особа</h4>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Оберiть</label>

                            <div class="col-md-6">

                                <?= Html::dropDownList('Tender[procuringEntity][contactPoint][fio]', null,
                                    ArrayHelper::map(\app\models\Persons::find()->all(),
                                        'id',
                                        function ($model, $defaultValue) {

                                            return $model->userName . ' ' . $model->userSurname . '' . $model->userPatronymic;
                                        }
                                    ),
                                    ['class' => 'form-control contact_person', 'prompt' => 'Не выбрано']); ?>
                            </div>
                        </div>
                        <div class="contact_group_wrapper">
                            <?= $form->field($tender->procuringEntity->contactPoint, 'name', ['template' => $template])
                                ->textInput(['name' => 'Tender[procuringEntity][contactPoint][name]']);
                            ?>
                            <?= $form->field($tender->procuringEntity->contactPoint, 'email', ['template' => $template])
                                ->textInput(['name' => 'Tender[procuringEntity][contactPoint][email]']);
                            ?>
                            <?= $form->field($tender->procuringEntity->contactPoint, 'telephone', ['template' => $template])
                                ->textInput(['name' => 'Tender[procuringEntity][contactPoint][telephone]']);
                            ?>
                        </div>
                    </div>
                </div>


            </div>
            <div class="tab-pane" id="ru">...</div>
            <div class="tab-pane" id="en">...</div>
        </div>
    </div>
    <div class="col-md-offset-3 col-md-6">
        <!--        <button type="submit" name="submit_update" class="btn btn-default">Зберегти та перейти до публiкацiї</button>-->
        <!--        <button type="submit" name="drafts_update" class="btn btn-default drafts_submit">Зберегти до чернетки</button>-->
    </div>
    <?php ActiveForm::end(); ?>
</div><!-- tender-create -->