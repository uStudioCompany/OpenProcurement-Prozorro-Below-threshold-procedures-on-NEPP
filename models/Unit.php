<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unit".
 *
 * @property string $id
 * @property string $name
 * @property string $symbol
 * @property string $name_ru
 * @property string $symbol_ru
 * @property string $name_en
 * @property string $symbol_en
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'symbol', 'name_ru', 'symbol_ru', 'name_en', 'symbol_en'], 'required'],
            [['id', 'symbol', 'symbol_ru', 'symbol_en'], 'string', 'max' => 10],
            [['name', 'name_ru', 'name_en'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'symbol' => Yii::t('app', 'Symbol'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'symbol_ru' => Yii::t('app', 'Symbol Ru'),
            'name_en' => Yii::t('app', 'Name En'),
            'symbol_en' => Yii::t('app', 'Symbol En'),
        ];
    }
    
    public function helperLoadCodes($url)
    {
        $out = [];
        $tmp = Yii::$app->opAPI->getStandards($url);
        if (isset($tmp['uk']) && isset($tmp['ru']) && isset($tmp['en']) ) {
            foreach ($tmp['uk'] AS $code => $val) {
                $out[$code] = [
                    ':id'        => $code,
                    ':name'      => $val['name_uk'],
                    ':symbol'    => $val['symbol_uk'],
                    ':name_ru'   => $tmp['ru'][$code]['name_ru'],
                    ':symbol_ru' => $tmp['ru'][$code]['symbol_ru'],
                    ':name_en'   => $tmp['en'][$code]['name_en'],
                    ':symbol_en' => $tmp['en'][$code]['symbol_en']];
            }
        }
        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = "REPLACE INTO `unit` (`id`, `name`, `symbol`, `name_ru`, `symbol_ru`, `name_en`, `symbol_en`)
                             VALUES (:id,  :name,  :symbol,  :name_ru,  :symbol_ru,  :name_en,  :symbol_en);";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code=>$param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }
}
