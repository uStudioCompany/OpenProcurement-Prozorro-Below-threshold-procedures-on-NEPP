<?php

namespace app\modules\backend\controllers;

use app\models\CompanyType;
use Yii;
use app\models\CountrySheme;
use app\models\CountryShemeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CountryShemeController implements the CRUD actions for CountrySheme model.
 */
class CountryShemeController extends BackendController
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CountrySheme models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CountryShemeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CountrySheme model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CountrySheme model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CountrySheme();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CountrySheme model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->validate()) {

            if ($post['CountrySheme']['company_type_ids'] == '') {
                $model->company_type_ids = '';
            }else{
                $model->company_type_ids = implode(',', $post['CountrySheme']['company_type_ids']);
            }
            $model->save(false);
            return $this->redirect(['index']);
        } else {
            $model->company_type_ids = explode(',',$model->company_type_ids);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CountrySheme model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGetCompanyTypesByCountryId()
    {

        if (Yii::$app->request->isAjax) {

            $post = Yii::$app->request->post();

            $res = CompanyType::GetCompanyTypesByCountryId($post['id']);
            if ($res) {
                $html = '';
                foreach ($res as $k => $v) {
                    $html .= '<label><input type="checkbox" value="' . $v['id'] . '" name="CountrySheme[company_type_ids][]">' . $v['name'] . '</label>';
                }
                echo $html;
            }
        }
    }

    /**
     * Finds the CountrySheme model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CountrySheme the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CountrySheme::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
