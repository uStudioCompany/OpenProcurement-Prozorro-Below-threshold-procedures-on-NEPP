<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use \yii\db\ActiveRecord;
/**
 * This is the model class for table "cpv".
 *
 * @property string $id
 * @property string $pid
 * @property string $name
 * @property string $name_ru
 * @property string $name_en
 * @property string $dkpp_id
 * @property string $children
 * @property string $child_ids
 */
class Cpv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cpv';
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
            [['id', 'pid'], 'string', 'max' => 10],
            [['dkpp_id'], 'string', 'max' => 15]
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
            'dkpp_id' => Yii::t('app', 'Dkpp ID'),
            'children' => Yii::t('app', 'Children'),
            'child_ids' => Yii::t('app', 'Child Ids'),
        ];
    }

    public function search($params)
    {

        $query = $this->find();

        if (isset($params['pid'])) {
            if($params['pid'] == '99999999-9'){
                return $query->where(['id'=>'99999999-9'])->all();
            }else{
                $query->andWhere('pid = :pid', [':pid' => $params['pid']]);
            }

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
        function GetParentsCpv_REC(&$out, $in, $pid=null) {
            if (isset($in['children_array'])) {
                $in[':children'] = count($in['children_array']);
                if ($in[':children']) {
                    foreach ($in['children_array'] AS &$child) {
                        if (!isSet($child[':id'])) {
                            // Сташный костыль, в базе ЦПВ есть пробоины, исправляем висящих в воздухе детей детей!
                            $pid = $in[':id'];
                            foreach ($child['children_array'] AS $child_bed) {
                                $in[':child_ids'][] = $child_bed[':id'];
                                GetParentsCpv_REC($out, $child_bed, $pid);
                            }
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
                $in[':pid']   = $pid; }
            $in[':children']  = count($in['children_array']);
            $in[':child_ids'] = json_encode($in[':child_ids']);
            unset($in['children_array']);
            $out[$in[':id']]  = $in;
        }

        $out = [];
        $tmp = Yii::$app->opAPI->getStandards($url); // print_r($tmp); die();
        if (isset($tmp['uk']) && isset($tmp['ru']) && isset($tmp['en']) ) {

            $res    = array();
            $levels = 6;

            //  Строим дерево, для подсчета детей
            foreach($tmp['uk'] AS $key=>$val) {

                $link=&$res;
                $pid='0';

                // Маска для кода ЦПВ
                preg_match('/^([0-9]{2})([0-9])([0-9])([0-9])([0-9]{3})\-[0-9]$/',$key,$matches);

                for($i=1;$i<$levels;$i++) {
                    if($i==$levels-1 || (int)$matches[$i+1]==0) {
                        $link[$matches[$i]][':id']=$matches[0];
                        $link[$matches[$i]][':name']=$val;
                        $link[$matches[$i]][':name_ru']=$tmp['ru'][$key];
                        $link[$matches[$i]][':name_en']=$tmp['en'][$key];
                        $link[$matches[$i]][':pid']=$pid;
                        $link[$matches[$i]][':dkpp_id']='';
                        $link[$matches[$i]][':children']='';
                        $link[$matches[$i]][':child_ids']='';
                        break;
                    } else {
                        $pid=@$link[$matches[$i]][':id']?:'0';
                        $link=&$link[$matches[$i]]['children_array'];
                    }
                }
            }

            // Из дерева превращаем в плоский масив, данные для ДБ
            //
            foreach ($res AS $val) {
                GetParentsCpv_REC($out, $val);
            }
        }
        ksort($out);
        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = "REPLACE INTO `cpv` (`id`, `pid`, `name`, `name_ru`, `name_en`, `dkpp_id`, `children`, `child_ids`)
                           VALUES (:id,  :pid,  :name,  :name_ru,  :name_en,  :dkpp_id,  :children,  :child_ids);";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code=>$param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }

    public function helperLoadCodesMapping($url)
    {
        $out = [];
        $tmp = Yii::$app->opAPI->getStandards($url,['cpv2dkpp']); //,'dkpp2cpv'// print_r($tmp); die();
        if (isset($tmp['cpv2dkpp']) ) {
            foreach ($tmp['cpv2dkpp'] AS $cpv=>$dkpp_arr) {
                $out[$cpv] = [':id'=>$cpv,':dkpp_id'=>$dkpp_arr[0]];
            }
        }
        return $out;
    }

    public function helperSaveCodesMapping($params)
    {
        $sql = "UPDATE  `cpv` SET  `dkpp_id` =  :dkpp_id WHERE  `id` =  :id;";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code=>$param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }
}
