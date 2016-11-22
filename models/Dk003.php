<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dkpp003".
 *
 * @property string $id
 * @property string $pid
 * @property string $name
 * @property string $name_ru
 * @property string $name_en
 * @property string $cpv_id
 * @property string $children
 * @property string $child_ids
 */
class Dk003 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dk003';
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
            [['id', 'pid'], 'string', 'max' => 20],
            [['cpv_id'], 'string', 'max' => 10]
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
            'cpv_id' => Yii::t('app', 'Cpv ID'),
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
        /**
         * Recurse function for CPV Tree
         * @param $out
         * @param $in
         * @param null $pid
         */
        function GetParentsCpv_REC(&$out, $in, $pid = null)
        {
            if (isset($in['children_array'])) {
                $in[':children'] = count($in['children_array']);
                if ($in[':children']) {
                    foreach ($in['children_array'] AS &$child) {
                        if (!isSet($child[':id'])) {
                            // Сташный костыль, в базе ЦПВ есть пробоины, исправляем висящих в воздухе детей детей!
                            $pid = $in[':id'];
                            foreach ($child['children_array'] AS $child_bed) {
//                                var_dump($child_bed);die;

                                if (!isset($child_bed[':id'])) {
                                    foreach ($child_bed['children_array'] AS $child_bed2) {
                                        $in[':child_ids'][] = $child_bed2[':id'];
                                        GetParentsCpv_REC($out, $child_bed2, $pid);
                                    }
                                } else {
                                    $in[':child_ids'][] = $child_bed[':id'];
                                    GetParentsCpv_REC($out, $child_bed, $pid);
                                }

                            }
                            $pid = 0;
                        } else {
                            $in[':child_ids'][] = $child[':id'];
                            GetParentsCpv_REC($out, $child);
                        }
                    }
                }
            } else {
                $in['children_array'] = [];
            }
            if ($pid) { // Часть костыля, висящие дети без родителя
                $in[':pid'] = $pid;
            }
            $in[':children'] = count($in['children_array']);
            $in[':child_ids'] = json_encode($in[':child_ids']);
            unset($in['children_array']);
            $out[$in[':id']] = $in;
        }

        $out = [];
        $tmp = Yii::$app->opAPI->getStandardsFromFile($url); // print_r($tmp); die();

        $res = [];
        foreach ($tmp['uk'] as $k => $v) {
            $id = $k;
            $_id = (string)preg_replace('/[^0-9]/', '', $k);

            $levels = strlen($_id);

            $link =& $res;
            $pid = '0';
            //$s=true;
            for ($i = 0; $i < $levels; $i++) {
                if ($i == $levels - 1) {
                    $link[$_id{$i}][':id'] = $id;
                    $link[$_id{$i}][':name'] = $v;
                    $link[$_id{$i}][':pid'] = $pid;
                    $link[$_id{$i}][':children'] = '';
                    $link[$_id{$i}][':child_ids'] = '';
                    break;
                } else {
                    $pid = @$link[$_id{$i}][':id'] ?: '0';
                    $link =& $link[$_id{$i}]['children_array'];
                }

            }
        }

        foreach ($res AS $val) {
            GetParentsCpv_REC($out, $val);
        }
        ksort($out);
        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = "REPLACE INTO `dk003` (`id`, `pid`, `name`, `children`, `child_ids`)
                            VALUES (:id,  :pid,  :name,  :children,  :child_ids);";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code => $param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }
}
