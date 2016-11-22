<?php
use yii\helpers\Html;
$fieldLabel = $feature->attributeLabels();
?>
<!--<h2>Нецiновi показники</h2>-->

<div class="feature">


    <div class="row one-row-style">
        <div class="col-md-3">   <?= $fieldLabel['title']; ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?= Html::encode($feature->title) ?>
                </i>
            </b>
            <br/>
            <b>
                <i>
                    <?= Html::encode($feature->title_en) ?>
                </i>
            </b>
        </div>
    </div>


    <div class="row one-row-style">
        <div class="col-md-3">   <?= $fieldLabel['description'] ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?= Html::encode($feature->description) ?>
                </i>
            </b>
            <br/>
            <b>
                <i>
                    <?= Html::encode($feature->description_en) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= $feature->getAttributeLabel('relatedItem') ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?php
                    $itemArr = '';
                    foreach ($tender->items AS $i => $val) {
                        if (is_object($val)) {
                            if ($feature->relatedItem != null && $feature->relatedItem == $val->id) {
                                $itemArr = $val->description;
                            }
                        }
                    }
                    $lotArr = '';
                    foreach ($tender->lots AS $l => $val) {

                        if (is_object($val)) {
                            if($l === '__EMPTY_LOT__') continue;
//                            Yii::$app->VarDumper->dump($val->id, 10, true);
//                            Yii::$app->VarDumper->dump($feature->relatedItem, 10, true);
                            if ($feature->relatedItem != null && $feature->relatedItem == $val->id) {
                                $lotArr = $val->description;
                            }
                        }
                    }

                    if ($feature->relatedItem == null) {
                        echo Yii::t('app', 'tender');
                    } else if ($itemArr != '') {
                        echo Html::encode($itemArr);
                    } else if ($lotArr != '') {
                        echo Html::encode($lotArr);
                    }

                    ?>
                </i>
            </b>
        </div>
    </div>


    <div class="enum_block margin_t">

        <?php foreach ($feature['enum'] as $key => $enum) {
            if ($key === 'iClass') continue;
            ?>
            <div class="enum margin_b_20">


                <div class="row one-row-style">
                    <div class="col-md-3"><b> Опцiя <?= $key + 1; ?></b></div>
                    <div class="col-md-6">
                        <b>
                            <i>

                            </i>
                        </b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3">   <?= $feature->enum[0]->getAttributeLabel('title') ?></div>
                    <div class="col-md-6">
                        <b>
                            <i>
                                <?= Html::encode($enum->title) ?>
                            </i>
                        </b>
                        <br/>
                        <b>
                            <i>
                                <?= Html::encode($enum->title_en) ?>
                            </i>
                        </b>
                    </div>
                </div>

                <div class="row one-row-style">
                    <div class="col-md-3">   <?= $feature->enum[0]->getAttributeLabel('value') ?> </div>
                    <div class="col-md-6">
                        <b>
                            <i>
                                <?= Html::encode($enum->value) ?>
                            </i>
                        </b>
                    </div>
                </div>

            </div>

        <?php } ?>

    </div>

</div>

<hr>


