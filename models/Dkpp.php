<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dkpp".
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
class Dkpp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dkpp';
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

        function GetParentsDkpp_REC(&$out, $in) {
            if (isset($in['children_array'])) {
                $in[':children'] = count($in['children_array']);
                if ($in[':children']) {
                    foreach ($in['children_array'] AS &$child) {
                        $in[':child_ids'][] = $child[':id'];
                        GetParentsDkpp_REC($out, $child);
                    }
                }
            } else {
                $in['children_array'] = [];
            }
            $in[':children']  = count($in['children_array']);
            $in[':child_ids'] = json_encode($in[':child_ids']);
            unset($in['children_array']);
            $out[$in[':id']]  = $in;
        }

        $out = [];
        // ДКПП - усть только на Украинском... ![uk]! (((
        $tmp = Yii::$app->opAPI->getStandards($url,['uk']);
        if ( isset($tmp['uk']) ) {

            $res    = [];
            $levels = 6;
            $pid    = '0';

            foreach ($tmp['uk'] as $k=>$v) {
                if (strlen($k) === 1) {
                    $k = '0'.$k; }
                if(preg_match('/-/',$k)) {
                    $_mk=explode('-',$k);
                    $_mk[1]=str_replace('.','-',$_mk[1]);
                    $_mk=implode('.',$_mk);
                }
                else $_mk=$k;
                $_matches=explode('.',$_mk);
                $matches=array($_matches[0]);
                $levels=sizeof($_matches);
                for($i=1;$i<$levels;$i++){
                    if(!preg_match('/-/',$_matches[$i]) && $_matches[$i]!='00' && strlen($_matches[$i])>1 && (int)$_matches[$i]{0})
                        $matches[]=$_matches[$i]{0};
                    $matches[]=$_matches[$i];
                }

                $levels=sizeof($matches);

                $link=&$res;
                for ($i=0;$i<$levels;$i++) {
                    if($i==$levels-1/* || (int)$matches[$i+1]==0*/) {
                        $link[$matches[$i]][':id']=$k;
                        $link[$matches[$i]][':pid']=$pid;
                        $link[$matches[$i]][':name']=$v;
                        $link[$matches[$i]][':name_ru']=$v;
                        $link[$matches[$i]][':name_en']='';
                        $link[$matches[$i]][':cpv_id']='';
                        $link[$matches[$i]][':children']=0;
                        $link[$matches[$i]][':child_ids']=[];
                        $pid = '0';
                        break;
                    }
                    else {
                        if(empty($link[$matches[$i]])) {
                            $link[$matches[$i]][':id']=$k;
                            $link[$matches[$i]][':pid']=$pid;
                            $link[$matches[$i]][':name']=$v;
                            $link[$matches[$i]][':name_ru']=$v;
                            $link[$matches[$i]][':name_en']='';
                            $link[$matches[$i]][':cpv_id']='';
                            $link[$matches[$i]][':children']=0;
                            $link[$matches[$i]][':child_ids']=[];
                        }
                        $pid=@$link[$matches[$i]][':id']?:'0';
                        $link=&$link[$matches[$i]]['children_array'];
                    }
                }
            }
            // Из дерева превращаем в плоский масив, данные для ДБ
            //
            //print_r($res);

            foreach ($res AS $val) {
                GetParentsDkpp_REC($out, $val);
            }
        }
        ksort($out);
        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = "REPLACE INTO `dkpp_015` (`id`, `pid`, `name`, `name_ru`, `name_en`, `cpv_id`, `children`, `child_ids`)
                            VALUES (:id,  :pid,  :name,  :name_ru,  :name_en,  :cpv_id,  :children,  :child_ids);";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code=>$param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }
}
