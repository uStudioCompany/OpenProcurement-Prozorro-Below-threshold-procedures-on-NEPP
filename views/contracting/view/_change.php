<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<?php
$fieldLabel = $change->attributeLabels();
?>
<div class="item no_border">

    <div id="<?=$change->id?>" class="row one-row-style color-47">
        <div class="col-md-9 margin_b"><h3>
                <?= Yii::t('app', 'ЗМІНА ДО ДОГОВОРУ №') .
$change->contractNumber.' ВІД '. Yii::$app->formatter->asDatetime($change->date) ?>
            </h3></div>
        <div class="col-md-3">

        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= Yii::t('app', 'Причини змін до договору') ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?php
                    foreach ($change->rationaleTypes as $k => $rationaleType) {
                        $res[] = \app\models\contractModels\Contract::getContractChangesValue()[$rationaleType];
                    }
                    echo implode(', ', $res);
                    ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= Yii::t('app', 'Опис причини змін') ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?= Html::encode($change->rationale) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= Yii::t('app', 'Дата змiни') ?></div>
        <div class="col-md-6">
            <b>
                <i>
                    <?= Yii::$app->formatter->asDatetime($change->dateSigned) ?>
                </i>
            </b>
        </div>
    </div>

    <hr/>
</div>