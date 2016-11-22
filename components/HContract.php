<?php
namespace app\components;

use Yii;
use app\models\contractModels\Contract;

class HContract
{

    public static $empty_fields = [
//        'lotValues' => '__EMPTY_LV__',
        'items'          => '__EMPTY_ITEM__',
        'documents' => '__EMPTY_DOC__',
//        'parameters' => '__EMPTY_PARAMETERS__',
//        'cancellations' => '__EMPTY_CANCEL__',
    ];

    public static $zero_fields = [];

    /**
     * @param  string $scenario
     * @return Contract
     */
    public static function get($scenario = 'default')
    {
        return new Contract($scenario);
    }

    /**
     * @param  string $scenario
     * @return Tender
     */
    public static function create()
    {
        $scenario = 'default';
        $contract = self::get($scenario);
        self::addEmpty($contract);
        self::addZero($contract);
        self::delIClass($contract);
        return $contract;
    }

    /**
     * @param  array $data
     * @param  string $scenario
     * @return Contract
     */
    public static function update($data = [], $scenario = 'default')
    {
        $contract = self::get($scenario);
        self::addEmpty($contract);
        if (!empty($data)) {
            $contract->load($data,'Contract');
        }

        self::delIClass($contract);
        return $contract;
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