<?php
use yii\helpers\Html;
//Yii::$app->VarDumper->dump($documents->id, 10, true);
if($documents->is_old){ ?>
    <?=Yii::t('app','old_document')?> - <s><i><a href="<?= Html::encode($documents->url) ?>"><?= Html::encode($documents->title) ?></a></i></s><br>
<?php } else { ?>

<div class="document">
    <div class="row one-row-style">
        <div class="col-md-3"><?= Yii::t('app', 'Назва документу') ?></div>
        <div class="col-md-6">
            <?php
            if(isset($documents->id) && $documents->id != ''){ ?>

                <b> <i><a href="<?= Html::encode($documents->url) ?>"><?= Html::encode($documents->title) ?></a></i></b>

            <?php }else { ?>

                <b> <i><a href="<?= \yii\helpers\Url::to(['/web/uploads']) . DIRECTORY_SEPARATOR . $documents['realName'] ?>"><?= Html::encode($documents['title']) ?></a></i></b>

            <?php } ?>
        </div>
    </div>

    <?php
    $fileName = \app\models\DocumentType::findOne($documents->documentType);
    ?>


        <div class="row one-row-style">
            <div class="col-md-3"><?= Yii::t('app', 'Тип документу') ?></div>
            <div class="col-md-6">
                <b> <i><? echo $fileName ?  Html::encode($fileName['title']) : Yii::t('app','iншi'); ?></i>
                </b>
            </div>
        </div>


    <div class="row one-row-style">
        <div class="col-md-3"><?= Yii::t('app', 'Документ прив`язано до') ?></div>
        <div class="col-md-6">
            <b> <i>
                    <?php
                    $itemArr = '';
                    foreach ($tender->items AS $i => $val) {
                        if (is_object($val)) {
                            if ($documents->relatedItem != null && $documents->relatedItem == $val->id) {
                                $itemArr = $val->description;
                            }
                        }
                    }
                    $lotArr = '';
                    foreach ($tender->lots AS $l => $val) {
                        if (is_object($val)) {
                            if ($documents->relatedItem != null && $documents->relatedItem == $val->id) {
                                $lotArr = $val->description;
                            }
                        }
                    }

                    if ($documents->relatedItem == null || $documents->relatedItem == 'tender') {
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
</div>
<?php } ?>