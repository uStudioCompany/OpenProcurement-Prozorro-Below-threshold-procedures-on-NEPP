<?php
//Yii::$app->VarDumper->dump($feature, 10, true);
?>
<div class="feature">
<div class="form-group row">
    <label class="col-md-3 control-label"><?= $feature->title ?></label>
    <label class="col-md-3 control-label"><?= $feature->description ?></label>
    <div class="col-md-3 btn-group">

        <select class="form-control bid_feature_select" name='parameter[<?= $k ?>][code]'>
            <?php
            echo '<option value="">' . Yii::t('app', 'Не вибрано') . '</option>';
            foreach ($feature->enum as $num => $row) {
                echo '<option value="' . $feature->code . '" rel="' . $row->value . '">' . $row->title . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="col-md-3 bid_feature_value">

        <?php

        if(!$bid->id) {

            echo $form->field($bid->parameters['__EMPTY_PARAMETERS__'], '[' . $k . ']value')
                ->textInput([
                    'name' => 'parameter[' . $k . '][value]',
                    'class' => 'form-control bid_feature_value',
                    'readonly'=>''
                ])->label(false);
        }else {

            echo $form->field($bid->parameters[$k], '[' . $k . ']value')
                ->textInput([
                    'name' => 'parameter[' . $k . '][value]',
                    'class' => 'form-control bid_feature_value',
                    'readonly'=>''
                ])->label(false);
        }
        ?>

    </div>
    <input type="hidden" class="form-control related_id" name="parameter[<?= $k ?>][related_id]" value="<?=@$lot->id?>">

</div>
</div>
