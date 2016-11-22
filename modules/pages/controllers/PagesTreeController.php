<?php

namespace app\modules\pages\controllers;

use app\modules\backend\controllers\BackendController;
use app\modules\pages\models\Page;
use app\modules\pages\models\PagesTree;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii;

class PagesTreeController extends BackendController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all PagesTree models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tree = PagesTree::find()->with('pages')->orderBy('lft')->all();
        return $this->render('index', [
            'models' => $tree,
        ]);
    }

    /**
     * Creates a new PagesTree model.(folder)
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreateFolder($pt_id = null)
    {
        Yii::$app->session->setFlash('success', Yii::t('app', 'SAVE_SUCCESS'));
        return $this->actionUpdateFolder(null, $pt_id);
    }

    /**
     * Updates an existing PagesTree model.(folder)
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer|null $id
     * @return mixed
     */
    public function actionUpdateFolder($id = null, $pt_id = null)
    {
        $post = Yii::$app->request->post();
        $tree = PagesTree::find()->where(['icon' => 'folder'])->orderBy('lft')->all();
        if ($id === null) {
            $model = new PagesTree();
        } else {
            $model = $this->findModel($id);
            $pt_id = $model->findRoot($tree);
        }

        if (isset($post['PagesTree'])) {
            if ($id === null) {
                $model = new PagesTree(['name' => $post['PagesTree']['name']]);
                $model->icon = 'folder';

                $folder = PagesTree::findOne($post['pt_id']);
                if (!$model->appendTo($folder)) {
                    return false;
                }
            } else {
                $model->name = $post['PagesTree']['name'];
                $model->save();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'SAVE_SUCCESS'));
            return $this->redirect(['index']);
        }

        return $this->render($id === null ? 'create-folder' : 'update-folder', [
            'model' => $model,
            'folders' => $tree,
            'pt_id' => $pt_id,
            'action' => is_null($id) ? 'create' : 'update'
        ]);
    }

    /** Delete PagesTree model(with all children) where model->id = $id
     * Удаляет папку со всеми детьми(папками и страницами)
     *
     * @param integer $id
     * @return yii\web\Response
     */
    public function actionDeleteFolder($id)
    {
        $model = $this->findModel($id);
        $tree = PagesTree::findAll(['icon' => PagesTree::FILE]);
        $pageToDelete = $model->findChildren($tree);
        if ($model->deleteWithChildren()) {
            if (!empty($pageToDelete)) {
                Page::deleteAll(['in', 'pt_id', $pageToDelete]);
            }
        }
        return $this->redirect(['index']);
    }

    /** Add PagesTree model as a root
     * Добавляет новый корень (удаляет все папки и страницы)
     *
     * @return yii\web\Response
     */
    public function actionAddRoot()
    {
        $tree = PagesTree::find()->all();
        $root = PagesTree::checkExistRoot($tree);
        if (!$root) {
            PagesTree::deleteAll();
            Page::deleteAll();
            $root = new PagesTree(['name' => '']);
            $root->icon = PagesTree::FOLDER;
            $root->makeRoot();
            Yii::$app->session->setFlash('success', Yii::t('app', 'SAVE_SUCCESS'));
        } else {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'Root is exist'));
        }
        return $this->redirect(['index']);
    }

    /** Move node via ajax request
     * Перемещает узел, через ajax запрос
     *
     * @return bool
     */
    public function actionMove()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('index');
        }
        $post = Yii::$app->request->post();
        if ($post['parentID'] == -1) {
            return false;
        }
        $tree = PagesTree::find()->with('pages')->orderBy('lft')->all();
        if (!empty($post)) {
            $movableNode = PagesTree::findById($tree, $post['itemID']);
            if ($movableNode->findRoot($tree)['id'] == $post['parentID']) {
                return false;
            }
            $newNode = $movableNode;
            $children = $movableNode->findChildren($tree, true);
            $infoChildren = [];
            foreach ($children as $child) {
                $infoChildren[] = ['parentName' => $child->findRoot($tree)['name'], 'name' => $child->name, 'icon' => $child->icon, 'id' => $child->id];
            }
            if ($movableNode->deleteWithChildren()) {
                $parent = $this->findModel($post['parentID']);
                $model = new PagesTree(['name' => $newNode->name]);
                $model->icon = $newNode->icon;
                if (!$model->appendTo($parent)) {
                    return false;
                } else {
                    if ($model->icon == PagesTree::FILE) {
                        $tree = PagesTree::find()->with('pages')->orderBy('lft')->all();
                        $page = Page::findOne(['pt_id' => $newNode->id]);
                        $page->pt_id = $model->id;
                        $page->alias = $model->createPath($tree);
                        $page->save(false);
                    }
                    foreach ($infoChildren as $infoChild) {
                        $model = new PagesTree(['name' => $infoChild['name']]);
                        $model->icon = $infoChild['icon'];
                        $model->appendTo(PagesTree::findOne(['name' => $infoChild['parentName']]));
                        if ($model->icon == PagesTree::FILE) {
                            $tree = PagesTree::find()->with('pages')->orderBy('lft')->all();
                            $page = Page::findOne(['pt_id' => $infoChild['id']]);
                            $page->pt_id = $model->id;
                            $page->alias = $model->createPath($tree);
                            $page->save(false);
                        }
                    }
                }
            }
        }
        return false;
    }


    /**
     * Finds the PagesTree model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return PagesTree model
     * @throws yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PagesTree::findOne($id)) !== null) {
            return $model;
        }
        throw new yii\web\NotFoundHttpException();
    }
}