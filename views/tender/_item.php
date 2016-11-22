<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
?>

<?php //if ($currentLotId == $item->relatedLot) { ?>

    <div class="item no_border">

        <div class="row">
            <div class="col-md-9"><strong><?=Yii::t('app','Предмет закупiвлi')?></strong></div>
            <div class="col-md-3">
                <?= Html::button(Yii::t('app','Видалити товар'), ['class' => 'btn btn-default delete_item']) ?>
            </div>
        </div>


        <?= $form->field($item, '[' . $k . ']relatedLot')
            ->hiddenInput([
                'name' => 'Tender[items][' . $k . '][relatedLot]',
                'value' => $item->relatedLot ? $item->relatedLot : '',
                'rel' => 'hiddenid', 'class' => 'item_related_lot'])->label(false)
        ?>
        <?= $form->field($item, '[' . $k . ']id')
            ->hiddenInput([
                'name' => 'Tender[items][' . $k . '][id]',
                'value' => $item->id ? $item->id : md5(Yii::$app->security->generateRandomString(32)),
                'rel' => 'hiddenid', 'class' => 'item_id'])->label(false)
        ?>

        <?= $form->field($item, '[' . $k . ']description', ['template' => $template])
            ->textarea(['name' => 'Tender[items][' . $k . '][description]', 'class' => 'form-control item-description']) ?>

        <div class="eu_procedure">
        <?= $form->field($item, '[' . $k . ']description_en', ['template' => $template])
            ->textarea(['name' => 'Tender[items][' . $k . '][description_en]', 'class' => 'form-control item-descriptionen']) ?>
        </div>

        <?//= $form->field($item, '[' . $k . ']descriptionRu', ['template' => $template])
            //->textarea(['name' => 'Tender[items][' . $k . '][description_ru]', 'class' => 'form-control item-descriptionru']) ?>



        <?php
        $sel = '<div class="col-md-3">' .
            Html::dropDownList('Tender[items][' . $k . '][unit][code]', isset($item->unit->code) ? $item->unit->code : null, ArrayHelper::map(\app\models\Unit::find()->all(), 'id', ['name']), ['class' => 'form-control unit_select','id'=>'item-'. strtolower($k) .'-unit-code']).
            Html::activeInput('hidden', $item->unit, 'name', ['name' => 'Tender[items][' . $k . '][unit][name]', 'class' => 'hidden_unit_name'])
            . '</div>';
        echo $form->field($item, '[' . $k . ']quantity', [
            'template' => "{label}\n<div class=\"col-md-3\">{input}</div>\n" . $sel . "\n<div class=\"col-md-3\">{error}</div>"
        ])->textInput(['name' => 'Tender[items][' . $k . '][quantity]']) ?>

        <div class="row">
            <div class="col-md-9">
                <strong>
                    <?php echo $item->getAttributeLabel('classification') ?>
                </strong>
            </div>
        </div>
        <?php
        $hidden = Html::activeInput('hidden', $item->classification, 'id', ['name' => 'Tender[items][' . $k . '][classification][id]', 'class' => 'hidden_classification']);
        $templ = "{label}\n<div class=\"col-md-6\">{input}" . $hidden . "</div>\n<div class=\"col-md-3\">{error}</div>";
        echo $form->field($item->classification, '[' . $k . ']description', ['template' => $templ])
            ->textInput(['name' => 'Tender[items][' . $k . '][classification][description]', 'class' => 'form-control classificator-input', 'url' => Url::to(['classificator/cpv'])]);
        ?>


        <div class="additionalClassifications_block">

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo $item->getAttributeLabel('additionalClassifications') ?></label>

                <div class="col-md-6">
                    <?php
                    $code = $item->additionalClassifications[0]->scheme.'_'. mb_strtolower(\yii\helpers\BaseInflector::transliterate($item->additionalClassifications[0]->scheme));
                    $selectItems = array_merge(['000' => Yii::t('app','undefined')],Yii::$app->params['DK_LIBS']);
                    echo Html::dropDownList('Tender[items][' . $k . '][additionalClassifications][0][dkType]', $item->additionalClassifications[0]->dkType ? $item->additionalClassifications[0]->dkType : $code,
                        $selectItems,
                        [
                            'class' => 'form-control additionalClassifications_select',
                            'id'=>'additionalclassifications-' . strtolower($k) . '-dkType'
                        ]);
                    ?>
                </div>
            </div>

            <div class="additionalClassifications_input">
                <div class="row">
                    <div class="col-md-9">
                        <strong>
                            <?php echo $item->getAttributeLabel('additionalClassifications') ?>
                        </strong>
                    </div>
                </div>
                <?php
                $hidden = Html::activeInput('hidden', $item->additionalClassifications[0], 'id', ['name' => 'Tender[items][' . $k . '][additionalClassifications][0][id]', 'class' => 'hidden_additionalclassification']);
                $templ = "{label}\n<div class=\"col-md-6\">{input}" . $hidden . "</div>\n<div class=\"col-md-3\">{error}</div>";
                echo $form->field($item->additionalClassifications[0], '[' . $k . ']description', ['template' => $templ])
                    ->textInput(['name' => 'Tender[items][' . $k . '][additionalClassifications][0][description]', 'class' => 'form-control classificator-input', 'url' => Url::to(['classificator/dkpp'])]);
                ?>
            </div>


        </div>




        <div class="row">
            <div class="col-md-3"><strong><?=Yii::t('app','Доставка') ?></strong></div>
        </div>

        <?= $form->field($item->deliveryAddress, '[' . $k . ']countryName', ['template' => $template])
            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][countryName]']) ?>
        <?= $form->field($item->deliveryAddress, '[' . $k . ']region', ['template' => $template])
            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][region]']) ?>
        <?= $form->field($item->deliveryAddress, '[' . $k . ']locality', ['template' => $template])
            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][locality]']) ?>
        <?= $form->field($item->deliveryAddress, '[' . $k . ']streetAddress', ['template' => $template])
            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][streetAddress]']) ?>
        <?= $form->field($item->deliveryAddress, '[' . $k . ']postalCode', ['template' => $template])
            ->textInput(['name' => 'Tender[items][' . $k . '][deliveryAddress][postalCode]']) ?>

        <?= $form->field($item->deliveryDate, '[' . $k . ']startDate', ['template' => $template])
            ->textInput([
                'name' => 'Tender[items][' . $k . '][deliveryDate][startDate]',
                'class' => 'form-control picker itemdeliverydate-startdate'
            ]) ?>

        <?= $form->field($item->deliveryDate, '[' . $k . ']endDate', ['template' => $template])
            ->textInput([
                'name' => 'Tender[items][' . $k . '][deliveryDate][endDate]',
                'class' => 'form-control picker itemdeliverydate-enddate'
            ]) ?>
        <hr/>
    </div>

<?php //} ?>