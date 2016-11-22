<?php
/**
 * Created by PhpStorm.
 * User: fix
 * Date: 09.11.2016
 * Time: 11:35
 */

namespace app\modules\pages\models;

use creocoder\nestedsets\NestedSetsBehavior;
use yii;

class PagesTree extends yii\db\ActiveRecord
{
    const FOLDER = 'folder';
    const FILE = 'file';

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                // 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new PagesTreeQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasOne(Page::className(), ['pt_id' => 'id']);
    }

    /** Finds children of the node (without use db)
     * Находит детей узла (без использования бд)
     *
     * @param $tree array PagesTree
     * @param bool $info
     * @return array
     */
    public function findChildren($tree, $info = false)
    {
        $keyLeaves = [];
        foreach ($tree as $key => $node) {
            if ($node->lft > $this->lft && $node->rgt < $this->rgt) {
                $keyLeaves[] = $info ? $node : $node->id;
            }
        }
        return $keyLeaves;
    }

    /** Finds parent node of the node (without use db)
     * Находит родительский узел (без использования бд)
     *
     * @param $tree array PagesTree
     * @return array|bool|mixed
     */
    public function findRoot($tree)
    {
        if ($this->isRoot()) {
            return $this->id;
        }
        foreach ($tree as $key => $node) {
            if ($node->lft < $this->lft && $node->rgt > $this->rgt && $this->depth - $node->depth == 1) {
                return ['id' => $node->id, 'key' => $key, 'name' => $node->name];
            }
        }
        return false;
    }

    /** Creates path for alias
     *  Создает путь для alias
     *
     * @param $tree array PagesTree
     * @param string $path
     * @return string
     */
    public function createPath($tree, $path = '')
    {
        if ($this->isRoot()) {
            return '';
        }
        $path = $this->name . ($path != '' ? ('/' . $path) : '');
        if ($this->depth == 1) {
            return $path;
        } else {
            $root = self::findById($tree, $this->findRoot($tree)['id']);
            return $root->createPath($tree, $path);
        }
    }

    /** Finds node by $id (without use db)
     * Находит узел по $id (без использования бд)
     *
     * @param $tree
     * @param $id
     * @return bool
     */
    public static function findById($tree, $id)
    {
        foreach ($tree as $key => $node) {
            if ($node->id == $id) {
                return $node;
            }
        }
        return false;
    }

    /** Checks if exists root
     * Проверяет, существует ли корень
     *
     * @param $tree array PagesTree
     * @return bool||PagesTree
     */
    public static function checkExistRoot($tree)
    {
        foreach ($tree as $node) {
            if ($node->lft == 1) {
                return $node;
            }
        }
        return false;
    }
}