<?php
namespace app\modules\seller\helpers;

use Yii;
use app\modules\seller\models\BidModels\Bid;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class HBid
{

    public static $empty_fields = [
        'lotValues' => '__EMPTY_LV__',
//        'lots'          => '__EMPTY_LOT__',
        'documents' => '__EMPTY_DOC__',
        'parameters' => '__EMPTY_PARAMETERS__',
//        'cancellations' => '__EMPTY_CANCEL__',
    ];

    public static $zero_fields = ['lotValues'];

    /**
     * @param  string $scenario
     * @return Tender
     */
    public static function get($scenario = 'default')
    {
        return new Bid($scenario);
    }

    /**
     * @param  string $scenario
     * @return Tender
     */
    public static function create()
    {
        $scenario = 'default';
        $bid = self::get($scenario);
        self::addEmpty($bid);
        self::addZero($bid);
        self::delIClass($bid);
        return $bid;
    }

    /**
     * @param  array $data
     * @param  string $scenario
     * @return Bid
     */
    public static function update($data = [], $scenario = 'default')
    {
//        Yii::$app->VarDumper->dump($data, 10, true);die;
        $bid = self::get($scenario);
        self::addEmpty($bid);
        if (!empty($data)) {
            $bid->load($data);
        }
        // Для отображения тендера без лотов, нужен один пустой лот...
//        // :( так исторически сложилось...
//        if (count($tender->lots) <= 2) {
//            $tender->fill($tender->lots);
//        }
        self::delIClass($bid);
//Yii::$app->VarDumper->dump($bid, 10, true);die;
        return $bid;
    }

    /**
     * @param  array $data
     * @param  string $scenario
     * @return Tender
     */
    public static function load($data = [], $scenario = 'default')
    {
        $tender = self::get($scenario);
        if (!empty($data)) {
            $tender->load($data);
        }
        self::delIClass($tender);
        return $tender;
    }

    /**
     * @param  Tender $obj
     * @return Tender
     */
    public static function addEmpty($obj)
    {
        foreach (self::$empty_fields AS $field => $value) {
            //foreach ( $obj->emptyFields() AS $field=>$value) {
            $obj->fill($obj->$field, $value);
        }

        return $obj;
    }

    /**
     * @param  Tender $obj
     * @return Tender
     */
    public static function addZero($obj)
    {
        foreach (self::$zero_fields AS $field) {
            $obj->fill($obj->$field);
        }

        return $obj;
    }

    /**
     * @param Tender $obj
     */
    public static function delIClass(&$obj)
    {
        if (!is_object($obj)) {
            //echo '<pre>'; print_r($obj); die();
            return;
        }
        foreach ($obj->attributes AS $key => $val) {
            if (is_array($val) && isset($val['iClass'])) {
                $abr = &$obj->$key;
                unset($abr['iClass']);
                foreach ($val AS $i => &$sub_val) {
                    if ($i === 'iClass') continue;
                    self::delIClass($sub_val);
                }
            } else if (is_object($val)) {
                self::delIClass($obj->$key);
            }
        }
    }

    public static function getOneBidFields($lot, $bid, $tenders, $form, $count)
    {


        foreach ($bid->lotValues as $b => $oneBid) {
            if ($b === '__EMPTY_LV__') continue;

            if ($lot->id == $oneBid->relatedLot) {

                //echo '<pre>'; print_r($oneBid->value); DIE();

                echo $form->field($oneBid->value, '[' . $lot->id . ']amount')
                    ->textInput([
                        'name' => 'Bid[lotValues][' . $lot->id . '][value][amount]',
                        'class' => 'form-control bid_value',
                        'data-lid'=>$lot->id,
                        'data-tid'=>$tenders->id,
                        'data-max'=>$lot->value->amount,
                        'data-prev'=>$oneBid->value->amount,
                        'data-error'=>0,
                        'data-need_check'=>0,
                    ])
                    ->label($count . Yii::t('app', '. Цінова пропозиція до лоту ') . $lot->title);


                echo '<hr/>';

                return;

            }
        }


        echo $form->field($bid->lotValues['__EMPTY_LV__']->value, '[' . $lot->id . ']amount')
            ->textInput([
                'name' => 'Bid[lotValues][' . $lot->id . '][value][amount]',
                'class' => 'form-control bid_value',
                'data-lid'=>$lot->id,
                'data-tid'=>$tenders->id,
                'data-max'=>$lot->value->amount,
                'data-prev'=>$bid->lotValues['__EMPTY_LV__']->value->amount.'',
                'data-error'=>0,
                'data-need_check'=>1,
            ])
            ->label($count . Yii::t('app', '. Цінова пропозиція до лоту ') . $lot->title);

        echo '<hr/>';


    }

    public static function getOneBidFieldsConfirm($lot, $bid, $tenders, $form, $count)
    {

        foreach ($bid->lotValues as $b => $oneBid) {
            if ($b === '__EMPTY_LV__') continue;

            if ($lot->id == $oneBid->relatedLot) {



                    echo $form->field($oneBid->value, '[' . $lot->id . ']amount')
                        ->textInput([
                            'name' => 'Bid[lotValues][' . $lot->id . '][value][amount]',
                            'class' => 'form-control bid_value',
                            'data-lid' => $lot->id,
                            'data-tid' => $tenders->id,
                            'data-max' => $lot->value->amount,
                            'data-prev' => $oneBid->value->amount,
                            'data-error' => 0,
                            'data-need_check' => 0,
//                        'disabled' => true
                        ])
                        ->label($count . Yii::t('app', '. Цінова пропозиція до лоту ') . $lot->title);



                return;

            }
        }
    }

    public static function getOneCompetentiveBidFields($lot, $bid)
    {
        foreach ($bid->lotValues as $b => $bidLot) {
            if ($b === '__EMPTY_LV__') continue;

            if ($lot->id == $bidLot->relatedLot) {
                return $bidLot->status = 'pending' ? true : false;
            }
        }


    }

    public static function getTenderLots($tender, $documents)
    {
        $relatedItem = $documents->relatedItem;
        echo '<option value="">' . Yii::t('app', 'Select document level') . '</option>';
        echo '<option value="tender"'. (($relatedItem == null && $documents->documentOf == 'tender') ? 'selected' : '') .'>' . Yii::t('app', 'tender') . '</option>';
        foreach ($tender->lots as $k => $lot) {
            if  ($lot->status == 'active') {
                $selected = $lot->id == $relatedItem ? 'selected' : '';
                echo '<option value="' . $lot->id . '" ' . $selected . '>' . $lot->title . '</option>';
            }
        }
    }

    public static function getLotItem($tender, $lotId)
    {
        $itemsArr = [];
        foreach ($tender->items as $l => $item) {
            if ($item->relatedLot == $lotId) {
                $itemsArr[] = $item->id;
            }

        }
        return $itemsArr;
    }

    public static function getDocumentURL($tenderId, $document)
    {
//        Yii::$app->VarDumper->dump($document, 10, true);die;
        if (isset($document['documentType']) && $document['documentType']) {
            $convert = self::getDocumentConvert($document['documentType']);
            return $tenderId . '/' . $convert;
        } else {
            return $tenderId;
        }
    }

    public static function getDocumentConvert($documentType)
    {
        return Yii::$app->params[$documentType];
    }


}