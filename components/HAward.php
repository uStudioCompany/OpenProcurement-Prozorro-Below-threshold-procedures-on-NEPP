<?php
namespace app\components;

use Yii;
use app\models\tenderModels\Award;

class HAward
{

    public static $empty_fields =[];

    public static $zero_fields = ['suppliers'];

    /**
     * @param  string $scenario
     * @return Award
     */
    public static function get($scenario='default') {
        return new Award($scenario);
    }

    /**
     * @param  string $scenario
     * @return Award
     */
    public static function create($scenario='default') {
        $obj = self::get($scenario);
        self::addEmpty($obj);
        self::addZero($obj);
        self::delIClass($obj);
        return $obj;
    }

    /**
     * @param  array  $data
     * @param  string $scenario
     * @return Award
     */
    public static function update($data=[], $scenario='default') {
        $obj = self::get($scenario);
        self::addEmpty($obj);
        if (!empty($data)) {
            $obj->load($data);
        }
        self::delIClass($obj);
        return $obj;
    }

    /**
     * @param  array  $data
     * @param  string $scenario
     * @return Award
     */
    public static function load($data=[], $scenario='default') {
        $obj = self::get($scenario);
        if (!empty($data)) {
            $obj->load($data);
        }
        self::delIClass($obj);
        return $obj;
    }

    /**
     * @param  Award $obj
     * @return Award
     */
    public static function addEmpty($obj) {
        foreach ( self::$empty_fields AS $field=>$value) {
        //foreach ( $obj->emptyFields() AS $field=>$value) {
            $obj->fill($obj->$field,$value);
        }

        return $obj;
    }

    /**
     * @param  Award $obj
     * @return Award
     */
    public static function addZero($obj) {
        foreach ( self::$zero_fields AS $field) {
            $obj->fill($obj->$field);
        }

        return $obj;
    }

    /**
     * @param Award $obj
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
}