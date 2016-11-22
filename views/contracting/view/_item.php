<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
?>
<?php
$fieldLabel = $item->attributeLabels();
?>
<div class="item no_border">

    <div class="row one-row-style color-47">
        <div class="col-md-9 margin_b"><h3><?=Yii::t('app','Предмет закупiвлi')?></h3></div>
        <div class="col-md-3">

        </div>
    </div>

<!--    <input type="hidden" value="--><?//=$item->relatedLot ? $item->relatedLot : '' ?><!--" class="item_related_lot">-->
    <input type="hidden" value="<?=$item->id ? $item->id : '' ?>" class="item_related_lot">


    <div class="row one-row-style">
        <div class="col-md-3">  <?= $fieldLabel['description'] ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.description">
                    <?= Html::encode($item->description) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3"> <?= $fieldLabel['quantity'] ?> </div>
        <div class="col-md-6">
            <b>
                <i tid="items.quantity">
                    <?= Html::encode($item->quantity) ?>
                    <?
                    if(isset($item->unit->code)){
                        $res =  \app\models\Unit::findOne(['id'=>$item->unit->code])->name;
                        if($res){
                            echo Html::encode($res);
                        }
                    }
                    ?>
                </i>
            </b>
        </div>
    </div>


    <div class="row one-row-style">
        <div class="col-md-3" tid="items.classification.scheme">  <?= $item->getAttributeLabel('classification') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.classification.id">
                    <?= Html::encode($item->classification->id) ?>
                </i> -
                <i tid="items.classification.description">
                    <?= Html::encode($item->classification->description) ?>
                </i>

            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3" tid="items.additionalClassifications.scheme">  <?= $item->getAttributeLabel('additionalClassifications') ?> </div>
        <div class="col-md-6">
            <b>
                <i tid="items.additionalClassifications.id">
                    <?= Html::encode($item->additionalClassifications[0]->id) ?>
                </i> -
                <i tid="items.additionalClassifications.description">
                    <?= Html::encode($item->additionalClassifications[0]->description) ?>
                </i>
            </b>
        </div>
    </div>





    <div class="row one-row-style">
        <div class="col-md-3 margin_t"><strong>Доставка</strong></div>
    </div>


    <div class="row one-row-style">
        <div class="col-md-3">   <?= $item->deliveryAddress->getAttributeLabel('countryName') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryAddress.countryName">
                    <?= Html::encode($item->deliveryAddress->countryName) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">   <?= $item->deliveryAddress->getAttributeLabel('region') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryAddress.region">
                    <?= Html::encode($item->deliveryAddress->region) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= $item->deliveryAddress->getAttributeLabel('locality') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryAddress.locality">
                    <?= Html::encode($item->deliveryAddress->locality) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">   <?= $item->deliveryAddress->getAttributeLabel('streetAddress') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryAddress.streetAddress">
                    <?= Html::encode($item->deliveryAddress->streetAddress) ?>
                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= $item->deliveryAddress->getAttributeLabel('postalCode') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryAddress.postalCode">
                    <?= Html::encode($item->deliveryAddress->postalCode) ?>

                </i>
            </b>
        </div>
    </div>

    <div class="row one-row-style">
        <div class="col-md-3">  <?= $item->deliveryDate->getAttributeLabel('endDate') ?></div>
        <div class="col-md-6">
            <b>
                <i tid="items.deliveryDate.endDate">
                    <?= $item->deliveryDate->endDate ? Html::encode(Yii::$app->formatter->asDate($item->deliveryDate->endDate)) : '' ?>
                </i>
            </b>
        </div>
    </div>


    <hr/>
</div>