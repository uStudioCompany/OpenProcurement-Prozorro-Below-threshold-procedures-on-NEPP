<?php

namespace app\modules\seller\controllers;

use app\models\Plans;
use app\models\PlansSearch;
use app\models\User;
use app\models\Companies;
use yii\base\ErrorException;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use app\components\ApiHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use app\models\planModels\Plan;

/**
 * Class TenderController
 * @package app\controllers
 */
class PlanController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('Forbidden', Yii::t('app', 'no.access'));
                    $this->redirect(Url::toRoute('/pages/company-auth'));
                },

                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return true;
//                            if (Yii::$app->request->get('id')) {
//                                $res = Plans::findOne(Yii::$app->request->get('id'))->company_id;
//                                return Yii::$app->user->identity->company_id == $res && Companies::checkAllowedCompanyStatusToWork(Yii::$app->user->identity->company_id);
//                            } else {
//                                return Companies::checkAllowedCompanyStatusToWork(Yii::$app->user->identity->company_id);
//                            }

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
     * Lists all Invite models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


//    public function actionCreate()
//    {
//        $post = Yii::$app->request->post();
//
//        if (isset($post['drafts']) or isset($post['autoSave']) or isset($post['publish'])) { //если нажата кнопка "Опубликовать"б "сохранить в черновик" или (автосейв)
//
//            if (isset($post['id']) && $post['id']) {
//                $plans = Plans::getModel($post['id']);
//            } else {
//                $plans = new Plans();
//            }
//
//            $plan = new Plan([], [], 'update');
//
//            if ($rez = $this->edit($plans, $post, $plan)) {
//                return $rez;
//            }
//
//        } else {
//            $plan = new Plan([], [], 'create');
//        }
//
//        return $this->render('create', [
//            'id' => 0,
//            'published' => 0,
//            'plan' => $plan,
//        ]);
//
//    }


//    public function actionUpdate($id)
//    {
//        $plan = new Plan([], [], 'update');
//        $post = Yii::$app->request->post();
//
//        if (isset($post['drafts']) or isset($post['autoSave']) or isset($post['publish'])) { //если нажата кнопка "сохранить в черновик" или автосейв
//
//            $plans = Plans::getModel($id);
//
//            if ($rez = $this->edit($plans, $post, $plan)) {
//                return $rez;
//            }
//
//        } else {
//            $plans = Plans::getPlans($id);
//            $plan->load($plans->json, 'Plan');
//        }
//
//        return $this->render('create', [
//            'id' => $id,
//            'published' => !empty($plans->plan_id),
//            'plan' => $plan,
//        ]);
//    }

//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    public function actionView($id)
    {
        $plan = new Plan([], [], 'update');
        $plans = Plans::getPlans($id);
        $plan->load($plans->json, 'Plan');

//        Yii::$app->VarDumper->dump($plan, 10, true);die;
        return $this->render('view', [
            'plan' => $plan,
            'plans' => $plans,
        ]);
    }


    /**
     * @param $plans Plans
     * @param $post array
     * @param $plan Plan
     * @return string|\yii\web\Response
     * @throws ErrorException
     */
    public function edit($plans, $post, $plan)
    {
//        Yii::$app->VarDumper->dump($post, 10, true);die;
        if (Plans::submitToDB($plans, $plan, $post, isset($post['publish']))) {

            if (isset($post['publish'])) {
                if (!Plans::submitToApi($plans)) {
                    Yii::$app->session->setFlash('message_error', Yii::t('app', 'Ошибка публикации'));
                    return false;
                }

                $plans->save();
            }

            if (Yii::$app->request->isAjax) {
                $res = [
                    'key' => Yii::$app->request->csrfToken,
                    'id' => $plans->id];
                return json_encode($res);
            } else {
                if (isset($post['publish'])) {
                    Yii::$app->session->setFlash('message', Yii::t('app', 'План успешно создан.'));
                } else {
                    Yii::$app->session->setFlash('message', Yii::t('app', 'Данные успешно сохранены в черновик.'));
                }

                return $this->redirect(['index']);
            }
        }
        return false;
    }

    protected function findModel($id)
    {
        if (($model = Plans::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}