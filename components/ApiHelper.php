<?php
namespace app\components;

use app\models\tenderModels\Award;
use Yii;
use app\models\Companies;
use DateTime;
use DateInterval;
use DatePeriod;
use yii\helpers\ArrayHelper;

class ApiHelper
{
    /**
     * @var array
     */
    public static $_date_field_names = ['startDate', 'endDate', 'dateSigned'];

    public static $_award_types = ['unsuccessful', 'active', 'cancelled', 'tendererAction', 'add_award_bid_file','extend', 'winner_files']; // Дискваліфіковано, Переможець, Скасованно результат


    public static function parseResponce($response)
    {
        $out = [];

        $response_arr = explode("\r\n\r\n", $response);

        $n = count($response_arr);
        if ($n >=2) {
            $out['headers'] = explode("\r\n", $response_arr[$n - 2]);
            $out['body']    = json_decode($response_arr[$n - 1], 1);
            $out['raw']     = $response_arr[$n - 1];
        } else {
            throw new apiException('CBD response error, no body or headers, responce['. $response .']');
        }
        return $out;
    }

    /**
     * @param array $array
     * @param bool|false $forAPI
     */
    public static function FormatDate(array &$array, $forAPI = false)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::FormatDate($value, $forAPI);
            } else if (in_array($key, self::$_date_field_names)) {
                if (trim($value) != '') {
                    if ($forAPI) {
                        $value = date('c', strtotime(str_replace('/', '.', $value)));
                    } else {
                        $value = date('d/m/Y H:i', strtotime($value));
                    }
                } else {
                    unset($array[$key]);
                }
            }
        }
    }

//    public static function FormatDateWithoutChangeTime(array &$array) {
//        foreach ($array as $key => &$value) {
//            if (is_array($value)) {
//                self::FormatDateWithoutChangeTime($value);
//            } else if ( in_array($key,self::$_date_field_names) ) {
//                if (trim($value) != '') {
//                        $value = date('d.m.Y H:i', strtotime( str_replace('/','.',$value .' 16:00') ));
//                } else {
//                    unset($array[$key]); }
//            }
//        }
//    }

    public static function cdbAvailableControl(){
        if(!file_get_contents('../cdbAvailable.txt')){
            Yii::$app->session->setFlash('cdbAvailable', Yii::t('app', 'The central database is temporarily unavailable. Try to visit the site later.'));
        }
    }

    public static function convertDate($date, $forAPI = false)
    {
        if ($forAPI) {
            $date = date('c', strtotime(str_replace('/', '.', $date)));
        } else {
            $date = date('d.m.Y', strtotime($date));
        }
        return $date;
    }


    /**
     * @param array $array
     */
    public static function CalcPdv(array &$array)
    {
        if ($array['valueAddedTaxIncluded']) {
            //$array['amountNet'] = $array['amount'] - ($array['amountNet'] / 6);
        } else {
            $array['amount'] = $array['amount'] * 1.2;
            //$array['amountNet'] = $array['amount'];
        }
        unset ($array['valueAddedTaxIncluded']);
    }

    /**
     * @param array $array
     * @throws \Exception
     */
    public static function fillCompany(array &$array)
    {
        /**
         * @var $company Companies
         */
        $company = Companies::findOne(['id' => Yii::$app->user->identity->company_id]);
        if (!$company) {
            throw new \Exception('Company info not found');
        }

        $array = [
            'name' => $company->legalName,
            'identifier' => [
                'id' => $company->identifier,
                'scheme' => $company->countryNameSheme->name,
                'legalName' => $company->legalName,
            ]];
    }

    /**
     * @param $tender \app\models\tenderModels\Tender
     * @param $lot \app\models\tenderModels\Lot
     * @return array
     */
    public static function sortBids($tender, $lot = null, $tenders)
    {
        $tmp_arr = [];
        foreach ($tender->bids AS $key => $bid) {
            if ($key === 'iClass') continue;
            //если бид не собрался корректно
            if ($tenders->tender_type == 2 && in_array($tenders->tender_method, ['limited_negotiation', 'limited_negotiation.quick'])) {
                $bid = Award::createBidFromAward($bid, $tender);
            }

            $amount = 0;
            if ($lot) {
                foreach ($bid->lotValues as $key_lot => $lotValue) {
                    if ($lot->id === $lotValue->relatedLot) {
                        $amount = self::calcFeatures($tender, $bid, $lotValue->relatedLot, $lotValue->value->amount);
                    }
                }
            } else {
                $amount = self::calcFeatures($tender, $bid, null, $bid->value->amount);
            }
            if ($amount) {
                $bid->_counted_amount = $amount;
                $bid->_counted_num = $key;
//                $tmp_arr[$amount . '_' . $i++] = $bid;
                $tmp_arr[] = $bid;
            }
        }
        // для переговорок мы не сортируем биды по цене, а выводим по порядку
        if(!in_array($tenders->tender_method, ['limited_reporting', 'limited_negotiation','limited_negotiation.quick'])){
            ArrayHelper::multisort($tmp_arr, [ '_counted_amount','_counted_num',], [SORT_ASC, SORT_ASC]);
//            Yii::$app->VarDumper->dump($tmp_arr, 10, true);die;
//            ksort($tmp_arr, SORT_NUMERIC);
        }



        return array_values($tmp_arr);
    }

    /**
     * @param $tender \app\models\tenderModels\Tender
     * @param $bid \app\models\tenderModels\Bid
     * @param $lot_id string
     * @param $amount int
     * @return int
     */
    public static function calcFeatures($tender, $bid, $lot_id, $amount)
    {
        $out_amount = $amount;
        if (isset($bid->parameters) && count($bid->parameters)) {

            foreach ($bid->parameters AS $key => $parameter) {

                if ($feature = self::checkFeatureCode($tender, $lot_id, $parameter->code)) {

                    $percent = 0;
                    foreach ($bid->parameters AS $key2 => $parameter2) {

                        if ($feature2 = self::checkFeatureCode($tender, $lot_id, $parameter2->code)) {
                            $percent = $percent +  $parameter2->value*100;
//                            Yii::$app->VarDumper->dump($parameter->value, 10, true);
                        }
                    }
//                    $out_amount = $amount - ($amount * $parameter->value);
                    //100/(1+13/87) = 87
                    //$amount = 100 грн.
                    //$parameter->value = 0,2   0,11
                    $tmp = 100-$percent;
                    $out_amount = $amount / (1 + $percent/$tmp);
                    $enum_title = '';
                    foreach ($feature->enum as $key_enum => $enum) {
                        if ($enum->value === ($parameter->value * 100)) {
                            $enum_title = $enum->title;
                        }
                    }
                    $bid->_counted_history[] = ['name' => $feature->title . ($enum_title ? " ($enum_title)" : ''), 'value' => $parameter->value];
                }
            }

        }
        return $out_amount;
    }

    /**
     * @param $tender \app\models\tenderModels\Tender
     * @param $lot_id string
     * @param $code int
     * @return bool|\app\models\tenderModels\Feature
     */
    public static function checkFeatureCode($tender, $lot_id, $code)
    {
        foreach ($tender->features as $key => $feature) {
            if ($key === 'iClass') continue;
            if ($feature->code !== $code) {
                continue;
            }

            if ($feature->featureOf === 'tenderer') {
                return $feature; /* true;*/
            }

            if ($feature->featureOf === 'lot' && $feature->relatedItem === $lot_id) {
                return $feature; /* true;*/
            }

            if ($feature->featureOf === 'item') {
                if ($lot_id === null) {
                    return $feature; /* true;*/
                }

                foreach ($tender->items as $key_item => $item) {
                    if ($item->id === $feature->relatedItem && $item->relatedLot === $lot_id) {
                        return $feature; /* true;*/
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $awardId string
     * @param $data array|string
     * @return bool|array
     * //\app\models\tenderModels\Award
     */
    public static function checkAwardId($awardId, $data)
    {

        if (!is_array($data)) {
            $data = json_decode($data, 1);
        }

        if (isset($data['data']['awards'])) {
            foreach ($data['data']['awards'] AS $award) {
                if ($awardId === $award['id']) {
                    return $award;
                }
            }
        }

        return false;
    }

    public static function findLotByAwardId($tender, $id)
    {
        if (isset($tender->awards)) {
            foreach ($tender->awards AS $award) {
                if ($award->status === 'cancelled') continue;
                if ($award->id === $id) {
                    return $award->lotID;
                }
            }
        }
        return null;
    }

    public static function checkTenderMethod(&$post)
    {
        if (!isset($post['tender_method'])) {
            $post['tender_method'] = Yii::$app->params['tender.method'][0] . '_' . Yii::$app->params['tender.method.type'][0];
            $post['procurementMethod'] = ['method' => Yii::$app->params['tender.method'][0], 'method_type' => Yii::$app->params['tender.method.type'][0]];
            return true;
        }

        $tmp = explode('_', $post['tender_method']);

        if (!in_array($tmp[0], Yii::$app->params['tender.method'])) {
            return false;
        }

        if (!in_array($tmp[1], Yii::$app->params['tender.method.type'])) {
            return false;
        }

        $post['procurementMethod'] = ['method' => $tmp[0], 'method_type' => $tmp[1]];
        return true;
    }

    public static function numberOfWorkingDays($from, $to) {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = \Yii::$app->params['Holidays'];

        $from = new DateTime($from);
        $to = new DateTime($to);
        $to->modify('+1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        return $days;
    }

    public static function getBidDocumentConvert($url){
        //$tender_id . '/documents'
        return strpos($url, '_') ? $url : $url . '/documents';
    }

}