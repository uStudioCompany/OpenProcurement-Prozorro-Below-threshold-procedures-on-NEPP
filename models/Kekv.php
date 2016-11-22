<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kekv".
 *
 * @property string $id
 * @property string $pid
 * @property string $name
 * @property string $name_ru
 * @property string $name_en
 * @property string $children
 * @property string $child_ids
 */
class Kekv extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kekv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['name', 'name_ru', 'name_en', 'child_ids'], 'string'],
            [['children'], 'integer'],
            [['id', 'pid'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Pid'),
            'name' => Yii::t('app', 'Name'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_en' => Yii::t('app', 'Name En'),
            'children' => Yii::t('app', 'Children'),
            'child_ids' => Yii::t('app', 'Child Ids'),
        ];
    }

    public function search($params)
    {
        $query = $this->find();

        if (isset($params['pid'])) {
            $query->andWhere('pid = :pid', [':pid' => $params['pid']]);
        }

        if (isset($params['name'])) {
            $query->andWhere(['like', 'name', ':name', [':name' => $params['name']]]);
        }

        if (isset($params['id'])) {
            $query->andWhere(['like', 'id', ':id', [':id' => $params['id']]]);
        }

        if (isset($params['name_ru'])) {
            $query->andWhere(['like', 'name_ru', ':name', [':name' => $params['name']]]);
        }

        if (isset($params['name_en'])) {
            $query->andWhere(['like', 'name_en', ':name', [':name' => $params['name']]]);
        }

        $query->orderBy('id');

        return $query->all();
    }

    public function helperLoadCodes($url)
    {
        $out = [];

        $tmp = Yii::$app->opAPI->getStandards($url, ['uk']);

        $children = [];
        $child_ids = [];

        foreach ($tmp['uk'] AS $k => $row) {

            if (mb_strpos($k, '000') !== false) {
                $pid = '0';
            } else if (mb_strpos($k, '00') !== false) {
                $pid = substr($k, 0, 1) . '000';
            } else {
                $pid = substr($k, 0, 1) . substr($k, 1, 1) . '00';
            }
            $out[$k] = [':id' => $k, ':pid' => $pid, ':name' => $row, ':name_ru' => $row, ':name_en' => $row, ':children' => 0, ':child_ids' => ''];
            @$children[$pid]++;
            $child_ids[$pid][] = $k;
        }

        foreach ($children AS $key => $val) {
            if (!$key) continue;
            $out[$key]['children'] = $val;
            $out[$key]['child_ids'] = json_encode($children[$key]);
        }

        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = 'REPLACE' . ' INTO `kekv` (`id`, `pid`, `name`, `name_ru`, `name_en`, `children`, `child_ids`)
                                VALUES (:id,  :pid,  :name,  :name_ru,  :name_en,  :children,  :child_ids);';

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code => $param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }
}
