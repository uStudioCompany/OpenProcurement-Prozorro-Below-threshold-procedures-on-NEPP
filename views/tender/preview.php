<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = \app\models\Companies::findOne(['id'=>Yii::$app->user->identity->company_id])->legalName;
$fieldLabel = $tender->attributeLabels();
?>
<div class="tender-preview">

    <?php 
        $descr = '
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-tenderID">' . $fieldLabel['tenderID'] . '</label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-tenderID">' . $tender->tenderID . '</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id">' . $fieldLabel['id'] . '</label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id">' . $tender->id . '</span>
            </div>
        </div>';

        echo $this->render('/site/head', [
            'title' => $this->title, 
            'descr' => $descr
        ]); 
    ?>

    <div class="info-block">
        <h4><?=Yii::t('app','iнформацiя про органiзатора')?></h4>
        <?php 
            $Organization = $tender->procuringEntity;
            $Identifier = $Organization->identifier;
            $Address = $Organization->address;
        ?>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=Yii::t('app','TenderID')?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Organization->name ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$Identifier->attributeLabels()['scheme'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Identifier->scheme ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$Identifier->attributeLabels()['id'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Identifier->id ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$Identifier->attributeLabels()['legalName'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Identifier->legalName ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$Identifier->attributeLabels()['uri'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Identifier->uri ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$Organization->attributeLabels()['address'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$Address->streetAddress.', '.$Address->locality.', '.$Address->region.', '.$Address->postalCode.', '.$Address->countryName ?></span>
            </div>
        </div>
    </div>
    <div class="info-block">
        <h4><?=Yii::t('app','Загальна iнформацiя про закупiвлю')?></h4>
        <?php 
            $Value = $tender->value;
        ?>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$tender->attributeLabels()['title'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$tender->title ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$tender->attributeLabels()['description'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info" id="tender-id"><?=$tender->description ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label"><?=$tender->attributeLabels()['value'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$Value->amount ?></span>
                <span class="reg-info"><?=$Value->currency ?></span>
                <?php if ($Value->valueAddedTaxIncluded): ?>
                    <span class="reg-info"><?=Yii::t('app','з ПДВ')?></span>
                <?php else: ?>
                    <span class="reg-info"><?=Yii::t('app','без ПДВ')?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="info-block">
        <h4><?=Yii::t('app','Тендерна документацiя')?></h4>
        <table class="table table-responsive table-condensed">
            <?php 
                $Documents = $tender->documents;

                if (!empty($Document)):
                    foreach ($Documents as $doc):?>
                    <tr>
                        <td><?=$doc->datePublished ?></td>
                        <td><?=$doc->dateModified ?></td>
                        <td><?=$doc->description ?></td>
                        <td><a href="<?=$doc->url?>"><?=$doc->title ?></a></td>
                        <td><?=$doc->format ?></td>
                        <td><?=$doc->language ?></td>
                    </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td>not found</td>
                    </tr>
                <?php endif; ?>
        </table>
    </div>
    <div class="info-block">
        <h4><?=Yii::t('app','Специфiкацiя закупiвлi')?></h4>
        <?php 
            $Lots = $tender->lots;

            if (!empty($Lots)):
                $lotNumb = 0;
                foreach ($Lots as $lot):
                    $lotNumb++;?>
                    <div class="row">
                        <div class="col-md-3"><strong><?=Yii::t('app','Лот')?><span><?=$lotNumb ?></span></strong></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><label class="control-label" for="tender-id"><?=$lot->attributeLabels()['title'] ?></label></div>
                        <div class="col-md-6">
                            <span class="reg-info" id="tender-id"><?=$lot->title ?></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><label class="control-label" for="tender-id"><?=$lot->attributeLabels()['description'] ?></label></div>
                        <div class="col-md-6">
                            <span class="reg-info" id="tender-id"><?=$lot->description ?></span>
                        </div>
                    </div>
                    <?php
                        $Value = $lot->value;
                    ?>
                    <div class="row">
                        <div class="col-md-3"><label class="control-label" for="tender-id"><?=$lot->attributeLabels()['value'] ?></label></div>
                        <div class="col-md-6">
                            <span class="reg-info"><?=$Value->amount ?></span>
                            <span class="reg-info"><?=$Value->currency ?></span>
                            <?php if ($Value->valueAddedTaxIncluded): ?>
                                <span class="reg-info">з ПДВ</span>
                            <?php else: ?>
                                <span class="reg-info">без ПДВ</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                        $Value = $lot->minimalStep;
                    ?>
                    <div class="row">
                        <div class="col-md-3"><label class="control-label" for="tender-id"><?=$lot->attributeLabels()['minimalStep'] ?></label></div>
                        <div class="col-md-6">
                            <span class="reg-info"><?=$Value->amount ?></span>
                            <span class="reg-info"><?=$Value->currency ?></span>
                            <?php if ($Value->valueAddedTaxIncluded): ?>
                                <span class="reg-info"><?=Yii::t('app','з ПДВ')?></span>
                            <?php else: ?>
                                <span class="reg-info"><?=Yii::t('app','без ПДВ')?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                        if (!empty($tender->items)):
                            foreach ($tender->items as $item):
                                if ($item->relatedLot == $Lot->id):
                                    $unit = $item->unit;
                                    $classification = $item->classification;?>
                                    <div class="info-block lot-item">
                                        <div class="row"><div class="col-md-3"><strong><?=Yii::t('app','Склад лота')?></strong></div></div>
                                        <div class="row"><div class="col-md-3"><strong><?=$item->description ?></strong></div></div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$item->attributeLabels()['quantity'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$item->quantity ?></span>
                                                <span class="reg-info"><?=$unit->code ?></span>
                                                <span class="reg-info"><?=$unit->name ?></span>
                                            </div>
                                        </div>   
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$classification->attributeLabels()['scheme'] .', '.$classification->scheme ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$classification->description ?></span>
                                            </div>
                                        </div>
                                        <?php 
                                            if (!empty($item->additionalClassifications)):
                                                foreach ($item->additionalClassifications as $cl):?>            
                                                    <div class="row">
                                                        <div class="col-md-3"><label class="control-label" for="tender-id"><?=$cl->attributeLabels()['scheme'] .', '.$cl->scheme ?></label></div>
                                                        <div class="col-md-6">
                                                            <span class="reg-info"><?=$cl->description ?></span>
                                                        </div>
                                                    </div>
                                        <?php   endforeach;
                                            endif;
                                            $address = $item->deliveryAddress;?>
                                        <div class="row"><div class="col-md-3"><strong><?=$item->attributeLabels()['deliveryAddress'] ?></strong></div></div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$address->attributeLabels()['countryName'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$address->countryName ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$address->attributeLabels()['region'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$address->region ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$address->attributeLabels()['locality'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$address->locality ?></span>
                                            </div>
                                        </div> 
                                       <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$address->attributeLabels()['postalCode'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$address->postalCode ?></span>
                                            </div>
                                        </div> 
                                       <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$item->attributeLabels()['deliveryLocation'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$item->deliveryLocation ?></span>
                                            </div>
                                        </div>
                                        <?php $period = $item->deliveryDate; ?>    
                                        <div class="row"><div class="col-md-3"><strong><?=$item->attributeLabels()['deliveryDate'] ?></strong></div></div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$period->attributeLabels()['startDate'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$period->startDate ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$period->attributeLabels()['endDate'] ?></label></div>
                                            <div class="col-md-6">
                                                <span class="reg-info"><?=$period->endDate ?></span>
                                            </div>
                                        </div>
                                                                                                                                                                                                           
                                    </div> 

                    <?php       endif;
                            endforeach;
                        endif;?>           
                <?php endforeach; ?>
            <?php endif; ?>
    </div>
    <div class="info-block">
        <h4><?=Yii::t('app','Дати та термiни')?></h4>
        <?php 
            $enquiryPeriod = $tender->enquiryPeriod;
            $tenderPeriod = $tender->tenderPeriod;
            $auctionPeriod = $tender->auctionPeriod;
            $awardPeriod = $tender->awardPeriod;
        ?>
        <div class="row"><div class="col-md-3"><strong><?=$tender->attributeLabels()['enquiryPeriod'] ?></strong></div></div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$enquiryPeriod->attributeLabels()['startDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$enquiryPeriod->startDate ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$enquiryPeriod->attributeLabels()['endDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$enquiryPeriod->endDate ?></span>
            </div>
        </div>
        <div class="row"><div class="col-md-3"><strong><?=$tender->attributeLabels()['tenderPeriod'] ?></strong></div></div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$tenderPeriod->attributeLabels()['startDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$tenderPeriod->startDate ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$tenderPeriod->attributeLabels()['endDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$tenderPeriod->endDate ?></span>
            </div>
        </div>
        <div class="row"><div class="col-md-3"><strong><?=$tender->attributeLabels()['auctionPeriod'] ?></strong></div></div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$auctionPeriod->attributeLabels()['startDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$auctionPeriod->startDate ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$auctionPeriod->attributeLabels()['endDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$auctionPeriod->endDate ?></span>
            </div>
        </div>
        <div class="row"><div class="col-md-3"><strong><?=$tender->attributeLabels()['awardPeriod'] ?></strong></div></div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$awardPeriod->attributeLabels()['startDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$awardPeriod->startDate ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$awardPeriod->attributeLabels()['endDate'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$awardPeriod->endDate ?></span>
            </div>
        </div>
    </div>
    <div class="info-block">
        <h4><?=$tender->procuringEntity->attributeLabels()['contactPoint'] ?></h4>
        <?php $contactPoint =  $tender->procuringEntity->contactPoint; ?>
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$contactPoint->attributeLabels()['name'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$contactPoint->name ?></span>
            </div>
        </div>    
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$contactPoint->attributeLabels()['email'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$contactPoint->email ?></span>
            </div>
        </div>  
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$contactPoint->attributeLabels()['telephone'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$contactPoint->telephone ?></span>
            </div>
        </div>  
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$contactPoint->attributeLabels()['faxNumber'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$contactPoint->faxNumber ?></span>
            </div>
        </div> 
        <div class="row">
            <div class="col-md-3"><label class="control-label" for="tender-id"><?=$contactPoint->attributeLabels()['url'] ?></label></div>
            <div class="col-md-6">
                <span class="reg-info"><?=$contactPoint->url ?></span>
            </div>
        </div>      
    </div>
</div><!-- tender-preview -->