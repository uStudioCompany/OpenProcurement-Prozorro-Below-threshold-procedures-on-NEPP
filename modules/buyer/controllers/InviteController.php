<?php

namespace app\modules\buyer\controllers;

use Yii;
//use yii\base\Model;
use app\models\Invite;
use app\models\InviteSearch;
use app\models\User;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * InviteController implements the CRUD actions for Invite model.
 */
class InviteController extends Controller
{

    const INVITE_STATUS_ACTIVE = 1;

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
                            if (Yii::$app->request->get('id')) {
                                $res = Invite::findOne(Yii::$app->request->get('id'))->company_id;
                                return Yii::$app->user->identity->company_id == $res;
                            } else {
                                return true;
                            }

                        },
                    ],
                    [
                        'actions' => ['register'],
                        'allow' => true,
                        'roles' => ['?'],
                    ]
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
        $searchModel = new InviteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invite model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        //echo '<pre>'; print_r($this->findModel($id)); die();
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Invite model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invite();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Invite model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Invite model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $token
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRegister($token)
    {
        if (($invite_model = Invite::findOne(['token' => $token, 'status' => 0])) !== null) {
            $user_model = new User();
            if ($user_model->load(Yii::$app->request->post()) /*&& Model::validate($user_model)*/) {

                $user_model->company_id = $invite_model->company_id;
                $user_model->is_owner = 0;

                if ($user_model->save()) {
                    $invite_model->setStatus(self::INVITE_STATUS_ACTIVE);
                    $user_model->setStatus(User::STATUS_ACTIVE);
                    Yii::$app->session->setFlash('message', Yii::t('app', 'invite.Register success, sign in'));
                    return $this->redirect(['/']);
                } else {
                    throw new ErrorException('Save error');
                }
            } else {
                return $this->render('register', [
                    'model' => $invite_model,
                    'user_model' => $user_model,
                ]);
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }

    /**
     * Finds the Invite model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invite the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invite::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
