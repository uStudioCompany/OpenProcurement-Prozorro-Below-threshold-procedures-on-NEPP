<?php

namespace app\modules\buyer\controllers;

use app\models\tenderModels\Tender;
use Yii;
use app\models\Tenders;
use app\models\TendersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * TendersController implements the CRUD actions for Tenders model.
 */
class TendersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('Forbidden', Yii::t('app', 'no.access'));
                    $this->goBack();
                },

                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if(Yii::$app->request->get('id')){
                                $res = Tenders::findOne(Yii::$app->request->get('id'))->company_id;
                                return Yii::$app->user->identity->company_id == $res;
                            }else{
                                return true;
                            }

                        },
                    ],
                ],

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tenders models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TendersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tenders model.
     * @param integer $id
     * @return mixed
     */
//    public function actionView($id)
//    {
//        $tender = new Tender([], [], 'create');
//
//        $res = Yii::$app->simpleTenderConvertIn->getSimpleTender($id);
////            VarDumper::dump($res, 10, true);die;
////        $post['Tender'] = $res;
//        $tender->load($res, 'Tender');
//
//
//        return $this->render('view', [
//            'tender' => $tender,
//
//        ]);
//    }

    /**
     * Creates a new Tenders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Tenders();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing Tenders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing Tenders model.
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

    /**
     * Finds the Tenders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tenders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tenders::findOne(['id'=>$id,'company_id'=>Yii::$app->user->identity->company_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
