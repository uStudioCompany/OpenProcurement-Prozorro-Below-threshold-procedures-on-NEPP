<?php

namespace app\modules\backend\controllers;

use app\models\User;
use Yii;
use app\models\Companies;
use app\modules\backend\models\CompaniesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * CompaniesController implements the CRUD actions for Companies model.
 */
class CompaniesController extends BackendController
{
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
     * Lists all Companies models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompaniesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);//->with('CompanyType');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Companies model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model'     => $this->findModel($id),
            'user_list' => (new \app\models\Persons())->findPersonsByCompanyId($id),
        ]);
    }

    /**
     * Creates a new Companies model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Companies();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Companies model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = 'updateCompanyAdmin';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Companies model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        $model->setStatus(Companies::STATUS_ACCEPTED);

        return $this->redirect(['view', 'id' => $model->id],307);
    }

    public function actionNotaccepted($id)
    {
        $model = $this->findModel($id);
        $model->setStatus(Companies::STATUS_NEW);

        return $this->redirect(['view', 'id' => $model->id],307);
    }

    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        $model->setStatus(Companies::STATUS_BLOCKED);

//        $users = User::findByCompanyId($id);
//        $uid = [];
//        foreach ($users as $k=>$user) {
//            if($user->id){
//                $uid[]=$user->id;
//            }
//        }
//        $uid = implode(',',$uid);
//        User::updateAll(['status'=>Companies::STATUS_BLOCKED],['in','id',$uid]);
//        Yii::$app->VarDumper->dump($uid, 10, true);die;

        return $this->redirect(['view', 'id' => $model->id],307);
    }

    /**
     * Finds the Companies model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Companies the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Companies::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
