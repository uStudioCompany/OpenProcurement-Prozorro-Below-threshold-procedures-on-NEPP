<?php

namespace app\modules\pages\controllers;

use app\modules\backend\controllers\BackendController;
use app\modules\pages\models\Page;
use app\modules\pages\models\PageSearch;
use app\modules\pages\models\PagesTree;
use app\modules\pages\Module;
use vova07\imperavi\actions\GetAction as ImperaviGetAction;
use vova07\imperavi\actions\UploadAction as ImperaviUploadAction;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * ManagerController implements the CRUD actions for Page model.
 * @author Belosludcev Vasilij <bupy765@gmail.com>
 * @since 1.0.0
 */
class ManagerController extends BackendController
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
     * @inheritdoc
     */
    public function actions()
    {
        $module = Yii::$app->getModule('pages');
        
        $actions = [];
        
        // add images that have already been uploaded
        if ($module->addImage) {
            $actions['images-get'] = [
                'class' => ImperaviGetAction::className(),
                'url' => Yii::getAlias($module->urlToImages),
                'path' => Yii::getAlias($module->pathToImages),
                'type' => ImperaviGetAction::TYPE_IMAGES,
            ];
        }
        // upload image
        if ($module->uploadImage) {
            $actions['image-upload'] = [
                'class' => ImperaviUploadAction::className(),
                'url' => Yii::getAlias($module->urlToImages),
                'path' => Yii::getAlias($module->pathToImages),
            ];
        }
        // add files that have already been uploaded
        if ($module->addFile) {
            $actions['files-get'] = [
                'class' => ImperaviGetAction::className(),
                'url' => Yii::getAlias($module->urlToFiles),
                'path' => Yii::getAlias($module->pathToFiles),
                'type' => ImperaviGetAction::TYPE_FILES,
            ];
        }
        // upload file
        if ($module->uploadFile) {
            $actions['file-upload'] = [
                'class' => ImperaviUploadAction::className(),
                'url' => Yii::getAlias($module->urlToFiles),
                'path' => Yii::getAlias($module->pathToFiles),
                'uploadOnlyImage' => false,
            ];
        }
        
        return $actions;
    }

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($pt_id = null)
    {
        Yii::$app->session->setFlash('success', Module::t('SAVE_SUCCESS'));
        return $this->actionUpdate(null, $pt_id);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer|null $id
     * @return mixed
     */
    public function actionUpdate($id = null, $pt_id = null)
    {
        $post = Yii::$app->request->post();
        $tree = PagesTree::find()->where(['icon' => 'folder'])->orderBy('lft')->all();
        if ($id === null) {
            $model = new Page;
            $model->pt_id = $pt_id;
        } else {
            $model = $this->findModel($id);
            $currentNode = PagesTree::findOne($model->pt_id);
            $folderID = $currentNode->findRoot($tree)['id'];
            $model->pt_id = $folderID;
            $model->alias = (strpos($model->alias, '/') !== false) ? substr(strrchr($model->alias, '/'), 1) : $model->alias;
        }
        if ($model->load($post)) {
            $node = new PagesTree(['name' => $post['Page']['title']]);
            $node->icon = 'file';
            $folder = PagesTree::findOne($post['Page']['pt_id']);
            if (!$node->appendTo($folder)) {
                return false;
            }
            $model->pt_id = $node->id;
            $model->alias = $folder->createPath($tree, $model->alias);

            if ($model->save()) {
                if (isset($currentNode)) {
                    PagesTree::findOne($currentNode->id)->deleteWithChildren();
                }
                Yii::$app->session->setFlash('success', Module::t('SAVE_SUCCESS'));
                return $this->redirect(['pages-tree/index']);
            }
        }

        $module = Yii::$app->getModule('pages');

        return $this->render($id === null ? 'create' : 'update', [
            'model' => $model,
            'module' => $module,
            'folders' => $tree
        ]);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (PagesTree::findOne($model->pt_id)->deleteWithChildren() && $model->delete()) {
            Yii::$app->session->setFlash('success', Module::t('DELETE_SUCCESS'));
        }
        return $this->redirect(['pages-tree/index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Module::t('PAGE_NOT_FOUND'));
    }
    
}
