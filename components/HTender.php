<?php
namespace app\components;

use Yii;
use app\models\tenderModels\Tender;

class HTender
{

    public static $empty_fields = [
        'items'         => '__EMPTY_ITEM__',
        'lots'          => '__EMPTY_LOT__',
        'documents'     => '__EMPTY_DOC__',
        'features'      => '__EMPTY_FEATURE__',
        'cancellations' => '__EMPTY_CANCEL__',
    ];

    public static $zero_fields = ['items','lots','cancellations'];

    /**
     * @param  string $scenario
     * @return Tender
     */
    public static function get($scenario='default') {
        return new Tender($scenario);
    }

    /**
     * @param  string $scenario
     * @return Tender
     */
    public static function create($scenario='default') {
        $tender = self::get($scenario);
        self::addEmpty($tender);
        self::addZero($tender);
        self::delIClass($tender);
        return $tender;
    }

    /**
     * @param  array  $data
     * @param  string $scenario
     * @return Tender
     */
    public static function update($data=[], $scenario='default') {
        $tender = self::get($scenario);
        self::addEmpty($tender);
        if (!empty($data)) {
            $tender->load($data);
        }
        // Для отображения тендера без лотов, нужен один пустой лот...
        // :( так исторически сложилось...
        if (count($tender->lots) <= 2) {
            $tender->fill($tender->lots);
        }
        self::delIClass($tender);
        return $tender;
    }

    /**
     * @param  array  $data
     * @param  string $scenario
     * @return Tender
     */
    public static function load($data=[], $scenario='default') {
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
    public static function addEmpty($obj) {
        foreach ( self::$empty_fields AS $field=>$value) {
        //foreach ( $obj->emptyFields() AS $field=>$value) {
            $obj->fill($obj->$field,$value);
        }

        return $obj;
    }

    /**
     * @param  Tender $obj
     * @return Tender
     */
    public static function addZero($obj) {
        foreach ( self::$zero_fields AS $field) {
            $obj->fill($obj->$field);
        }

        return $obj;
    }

    /**
     * @param Tender $obj
     */
    public static function delIClass(&$obj) {
        if(!is_object($obj)) {
            //echo '<pre>'; print_r($obj); die();
            return;
        }
        foreach ($obj->attributes AS $key=>$val) {
            if (is_array($val) && isset($val['iClass'])) {
                $abr = &$obj->$key;
                unset( $abr['iClass']);
                foreach($val AS $i=>&$sub_val) {
                    if ($i==='iClass') continue;
                    self::delIClass($sub_val);
                }
            } else if (is_object($val)) {
                self::delIClass($obj->$key);
            }
        }
    }

    public static function checkMode()
    {
        $cookies = Yii::$app->request->cookies;
        // если куки нет, то ставим test mode
        if (!isset($_COOKIE['auction-mode'])) {
            $cookie = new \yii\web\Cookie([
                'name' => 'auction-mode',
                'value' => 'test',
                'expire' => time() + 86400 * 365,
                'httpOnly' => false
            ]);
            \Yii::$app->getResponse()->getCookies()->add($cookie);
            return true;
        } else {
            if ($cookies['auction-mode']->value == 'test' || $_COOKIE['auction-mode'] == 'test') {
                return true;
            } else {
                return false;
            }

        }
    }

    public static function simpleCheckMode()
    {
        $cookies = Yii::$app->request->cookies;
        // если куки нет, то ставим test mode
        if (isset($_COOKIE['auction-mode'])) {
            return $cookies['auction-mode']->value == 'test' ? true : false;
        }
    }
}