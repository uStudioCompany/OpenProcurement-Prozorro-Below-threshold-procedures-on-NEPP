<?php

namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\Menu;
use app\modules\backend\models\MenuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class TranslateController extends BackendController
{
    /**
     * @inheritdoc
     */
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
                            return \app\models\User::checkAdmin();
                        }
                    ],
                ],

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionUpdate($id)
    {

        $file = \yii\helpers\FileHelper::findFiles('../messages/' . $id . '/', [
            'only' => ['app.php'],
            'except' => ['*.DS_Store']
        ]);


        if (Yii::$app->request->post('text')) {
            file_put_contents($file[0],Yii::$app->request->post('text'));
            return $this->redirect('index');
        }

        $data = file_get_contents($file[0]);
        return $this->render('update', [
            'data' => $data,
        ]);
    }
}
