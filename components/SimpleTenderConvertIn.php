<?php

namespace app\components;

use app\models\Tenders;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\Json;
use Yii;
use ReflectionObject;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Countries;
use app\models\Regions;
use app\models\Persons;
use yii\base\ErrorException;


class SimpleTenderConvertIn
{


    /**
     * Функция для конвертации данных из ЦБД для формы редактирования тендера.
     *
     * @param int $id
     * @return array
     */
    public static function getSimpleTender($id)
    {
        $query = Tenders::find()->select(['response', 'json'])->where(['id' => $id])->asArray()->one();

        if (isset($query['response']) && $query['response'] != null) {//если не черновик
            $res = json_decode($query['response'], 1);
            self::FormatDate($res['data'], 'startDate');
            self::FormatDate($res['data'], 'endDate');
            self::FormatDate($res['data'], 'date');
            self::FormatFeatures($res['data']);

            /**
             * @TODO: Исправить костыль [relatedItem in documents]
             * Костыль от/для ЦБД, в ЦБД не очищают `relatedItem` после смены `documentOf` на 'tender'
             */
            if (isset($res['data']['documents'])) {
                foreach ($res['data']['documents'] AS &$doc) {
                    if ($doc['documentOf'] === 'tender') {
                        $doc['relatedItem'] = '';
                    }
                }
            }
            // === Костыль

            $post['Tender'] = $res['data'];
            return $post;
        } elseif (isset($query['json']) && $query['json'] != null) {
            $res = json_decode($query['json'], 1);
            self::FormatDate($res, 'startDate');
            self::FormatDate($res, 'endDate');
            self::FormatDate($res, 'date');
            self::FormatFeatures($res['data']);
            $post['Tender'] = $res['Tender'];
            unset($post['Tender']['items']['__EMPTY_ITEM__']);
            $post['Tender']['items'] = array_values($post['Tender']['items']);
            return $post;
        }
    }


    /**
     * Функция для конвертации данных из ЦБД для формы редактирования аварда в переговорной процедуре.
     *
     * @param $tenders Tenders
     * @return mixed
     */
    public static function getLimitedAward($tenders)
    {
        if (isset($tenders->response) && $tenders->response != null) {
            $data = Json::decode($tenders->response, true)['data'];
            if (isset($data['awards']) && count($data['awards']) > 0) {
                $active = 0;
                $awardsData = [];
                foreach ($data['awards'] as $award) {
                    if (in_array($award['status'], ['pending'])) {
                        $awardsData[] = $award;
                    }
                    if (in_array($award['status'], ['active'])) {
                        $active++;
                    }
                }
                if ($active && ($tenders->tender_type == 1 || $active == count($data['lots']))) {
                    return false;
                }
                return $awardsData;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $tenders \app\models\Tenders
     * @return mixed
     * @throws ErrorException
     */
    public static function getTenderInfo($tenders)
    {
        if ($tenders->response != null || $tenders->response != '') {//если не черновик
            $res = json_decode($tenders->response, 1);
            self::FormatDate($res['data'], 'startDate');
            self::FormatDate($res['data'], 'endDate');
            self::FormatDate($res['data'], 'date');
            self::FormatFeatures($res['data']);// echo '<pre>'; print_r($res['data']['awards']); die();

            //echo '<pre>'; print_r($res); die();
            return [ 'Tender' => $res['data'] ];
        } else {
            throw new ErrorException('Отсутсвуют данные от ЦБД'); }
    }

    public static function validateDate($date)
    {
        $date = date_parse($date);
        if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"])){
            return true;
//            echo "Valid date";die;
        } else{
            return false;
//            echo "Invalid date";die;
        }
    }


    public static function FormatDate(array &$array, $needle)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::FormatDate($value, $needle);
            } else if ($key === $needle) {
                if (trim($value) != '') {

                    if (self::validateDate(trim($value))) {
                        $value = str_replace('/', '.', $value);
                        $value = Yii::$app->formatter->asDatetime($value, "php:d/m/Y H:i:s");
                    }

                } else {
                    unset($array[$key]);
                }
            }
        }
    }

    public static function FormatFeatures(&$array)
    {
        if (!isset($array['features'])) return;

        if (count($array['features']) > 0) {
            foreach ($array['features'] as $k => &$feature) {
//                VarDumper::dump($feature, 10, true);die;
                foreach ($feature['enum'] as $key => &$item) {
                    $array['features'][$k]['enum'][$key]['value'] = $item['value'] * 100;
                }
            }
        }
    }

    public static function PrepareToDraft($post)
    {
        unset($post['Tender']['items']['__EMPTY_ITEM__']);
        unset($post['Tender']['lots']['__EMPTY_LOT__']);

        if (isset($post['Tender']['documents']['__EMPTY_DOC__'])) {
            unset($post['Tender']['documents']['__EMPTY_DOC__']);
        }
        if (isset($post['Tender']['features']['__EMPTY_FEATURE__'])) {
            unset($post['Tender']['features']['__EMPTY_FEATURE__']);
        }

        $post['Tender']['items'] = array_values($post['Tender']['items']);
        $post['Tender']['lots'] = array_values($post['Tender']['lots']);
        $post['Tender']['documents'] = array_values($post['Tender']['documents']);
        $post['Tender']['features'] = array_values($post['Tender']['features']);

        return $post;
    }


}
