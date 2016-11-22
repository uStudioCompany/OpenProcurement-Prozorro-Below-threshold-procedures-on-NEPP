<div class="feature grey">
    <div class="row">
        <div class="col-md-9"><strong></strong></div>
        <div class="col-md-3">
            <?= \yii\helpers\Html::button(Yii::t('app', 'Видалити показник'), [
//                                            'data-confirm' => Yii::t("app", "tender_delete_confirm_message"),
                'class' => 'btn btn-default delete_feature'
            ]) ?>
        </div>
    </div>

    <?= $form->field($feature, '[' . $k . ']title', ['template' => $template])
        ->textInput([
            'name' => 'Tender[features][' . $k . '][title]',
            'class' => 'form-control feature_title',
            'enum_count' => count($feature['enum']) ? count($feature['enum']) : 0,
            'feature_count' => 0
        ]) ?>

    <?= $form->field($feature, '[' . $k . ']description', ['template' => $template])
        ->textInput(['name' => 'Tender[features][' . $k . '][description]']) ?>


    <div class="eu_procedure">
        <?= $form->field($feature, '[' . $k . ']title_en', ['template' => $template])
            ->textInput([
                'name' => 'Tender[features][' . $k . '][title_en]',
                'class' => 'form-control feature_title_en',
                'enum_count' => count($feature['enum']) ? count($feature['enum']) : 0,
                'feature_count' => 0
            ]) ?>

        <?= $form->field($feature, '[' . $k . ']description_en', ['template' => $template])
            ->textInput(['name' => 'Tender[features][' . $k . '][description_en]']) ?>
    </div>

    <div class="form-group">
        <label
            class="col-md-3 control-label file_original_name"><?= $feature->getAttributeLabel('relatedItem') ?></label>
        <div class="col-md-6 btn-group document_link">
            <input type="hidden" class="form-control related_id"
                   name="Tender[features][<?= $k ?>][relatedItem]"
                   value="<?= $feature['relatedItem'] ?>">

            <select class="form-control" name='Tender[features][<?= $k ?>][relatedItem]'>
                <?php
                $res = \app\models\tenderModels\Feature::getFeatureTypes();
                foreach ($res as $num => $row) {
                    echo '<optgroup rel="' . $num . '" label="' . $row . '">' . $row . '</optgroup>';
                }
                ?>
            </select>
        </div>
        <div class="help-block"></div>
    </div>


    <div class="enum_block">

        <?php foreach ($feature['enum'] as $key => $enum) {
            if ($key === 'iClass') continue;
            ?>
            <div class="enum">

                <div class="row">

                    <label class="col-md-3 control-label enum-name"><?= Yii::t('app', 'Опцiя') ?>
                        <span><?= $key + 1; ?></span>
                        <?php
                        $ph = $feature->enum[0]->getAttributeLabel('title');
                        ?>
                    </label>
                    <div class="col-md-3">
                    </div>
                </div>
                <?php
                echo $form->field($feature['enum'][$key], '[' . $k . '][' . $key . ']title', ['template' => $template])
                    ->textInput([
                        'name' => 'Tender[features][' . $k . '][enum][' . $key . '][title]',
                        'class' => 'form-control enum_title',
                        'e' => $key
                    ]);
                ?>
                <div class="eu_procedure">
                    <?= $form->field($feature['enum'][$key], '[' . $k . '][' . $key . ']title_en', ['template' => $template])
                        ->textInput([
                            'name' => 'Tender[features][' . $k . '][enum][' . $key . '][title_en]',
                            'class' => 'form-control enum_title',
//                            'e' => $key
                        ]);
                    ?>
                </div>

                <?= $form->field($feature['enum'][$key], '[' . $k . '][' . $key . ']value', ['template' => $template])
                    ->textInput([
                        'name' => 'Tender[features][' . $k . '][enum][' . $key . '][value]',
                        'class' => 'form-control feature_enum_input',
                        'placeholder' => '%'
                    ]);
                ?>
                <div class="form-group">
                    <div class="col-md-3 control-label">
                        <?= \yii\helpers\Html::button(Yii::t('app', 'Видалити значення'), [
//                                            'data-confirm' => Yii::t("app", "tender_delete_confirm_message"),
                            'class' => 'btn-primary btn-xs delete_feature_enum'
                        ]) ?>
                    </div>
                </div>
            </div>

        <?php } ?>


        <button type="button" class="btn btn-default add_feature_enum"><?= Yii::t('app', 'Додати опцiю') ?></button>
    </div>


</div>


