<?php

namespace app\modules\buyer\controllers;

use app\components\ApiHelper;
use app\models\Companies;
use app\models\DocumentUploadTask;
use app\modules\seller\helpers\HContract;
use app\modules\seller\models\Tenders;
use Yii;
use app\models\Contracting;
use app\models\ContractingSearch;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\contractModels\Changes;

/**
 * ContractingController implements the CRUD actions for Contracting model.
 */
class ContractingController extends Controller
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
                    $this->goBack(Yii::$app->request->referrer);
                },
                'class' => AccessControl::className(),
                'except'=>['view', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->request->get('id')) {
                                return \app\models\Companies::checkCompanyIsContractOwner(Yii::$app->request->get('id'));
                            }

                        },
                    ],
                ],

            ],
        ];
    }

    /**
     * Lists all Contracting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContractingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contracting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $contracts = Contracting::getModel($id);
        $data['Contract'] = Json::decode($contracts->response)['data'];

        return $this->render('view', [
            'contract' => \app\components\HContract::update($data),
            'contracts' => $contracts
        ]);
    }


    /**
     * Updates an existing Contracting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $contractModel = Contracting::getModel($id);
        $data['Contract'] = Json::decode($contractModel->response)['data'];
        $tenders = Tenders::findOne($contractModel->tid);
        $changesModel = new Changes();
        $post = Yii::$app->request->post();
        if ($post) {
            $changesModel->load($post);
           if ($changesModel->validate()) {
               // сохраняем данные в БД
               if ($res = Contracting::saveToDB($post, $contractModel)) {

                   // разбиваем по точкам отправки
                   if ($res = Contracting::SplitData($res)) {

                       try {

                           // отправляем ченжди
                           $url = $contractModel->contract_id . '/changes';
                           $response = Yii::$app->opAPI->contractsPOST(
                               Json::encode($res['change']),
                               $url,
                               $contractModel->token
                           );
                           //отправляем на contracts
                           if (isset($res['contracts'])) {
                               $response2 = Yii::$app->opAPI->contracts(
                                   Json::encode($res['contracts']),
                                   $contractModel->contract_id,
                                   $contractModel->token
                               );
                           }

                           // если допороговая и не нажата галочка "Хочу подписать"
                           if ($tenders->tender_method == 'open_belowThreshold' && !isset($post['needSign'])) {
                               $contractModel->ecp = 1;
                               $contractModel->save(false);
                           } else {
                               // сбрасываем ецп
                               $contractModel->ecp = 0;
                               $contractModel->save(false);
                           }


                           //если есть документы, то ставим их на загрузку
                           if (isset($post['Contract']['documents']['__EMPTY_DOC__'])) {
                               unset($post['Contract']['documents']['__EMPTY_DOC__']);
                           }

                           if (isset($post['Contract']['documents']) && count($post['Contract']['documents']) > 0) {
                               $new_json = Yii::$app->opAPI->contracts(
                                   null,
                                   $contractModel->contract_id
                               );
                               DocumentUploadTask::updateTableAfterSaveContracting($id, $contractModel->contract_id, $contractModel->token, $post, $new_json['raw'], $response['body']['data']['id']);
                               self::update($id);
                               Yii::$app->session->setFlash('message', Yii::t('app', 'Змiни успiшно внесенi. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                               return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));
                           }

                           // активируем, если нет документов
                           if ($response && isset($response['body']['data']['id'])) {
                               $url2 = $contractModel->contract_id . '/changes/' . $response['body']['data']['id'];
                               $res2 = '{"data": {"status": "active", "dateSigned": "' . $res['change']['data']['dateSigned'] . '"}}';

                               $activate = Yii::$app->opAPI->contracts(
                                   $res2,
                                   $url2,
                                   $contractModel->token
                               );
                           }

                           self::update($id);
                           Yii::$app->session->setFlash('message', Yii::t('app', 'Змiни успiшно внесенi. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                           return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));


                       } catch (apiDataException $e) {
                           throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                       } catch (apiException $e) {
                           throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                       }
                   }
               }
           }
        }

        return $this->render('update', [
            'tenders' => $tenders,
            'contracts' => $contractModel,
            'contract' => \app\components\HContract::update($data),
            'changesModel' => $changesModel,
        ]);


    }

    public function actionTerminate($id)
    {
        $contractModel = Contracting::getModel($id);
        $data['Contract'] = Json::decode($contractModel->response)['data'];
        $tenders = Tenders::findOne($contractModel->tid);

        $post = Yii::$app->request->post();
        if ($post) {

            if ($data = Contracting::saveToDB($post, $contractModel)) {
//Yii::$app->VarDumper->dump($data, 10, true);die;
                unset($data['data']['status']);
                if (isset($post['Contract']['documents']['__EMPTY_DOC__'])) {
                    unset($post['Contract']['documents']['__EMPTY_DOC__']);
                }
//                Yii::$app->VarDumper->dump($data, 10, true);die;
                try {

                    // отправляем все, кроме статуса
                    $response = Yii::$app->opAPI->contracts(
                        Json::encode($data),
                        $contractModel->contract_id,
                        $contractModel->token
                    );



                    // если допороговая и не нажата галочка "Хочу подписать"
                    if ($tenders->tender_method == 'open_belowThreshold' && !isset($post['needSign'])) {
                        $contractModel->ecp = 1;
                        $contractModel->save(false);

                        //если есть документы, то ставим их на загрузку
                        if (isset($post['Contract']['documents']) && count($post['Contract']['documents']) > 0) {
                            $new_json = Yii::$app->opAPI->contracts(
                                null,
                                $contractModel->contract_id
                            );
                            DocumentUploadTask::updateTableAfterSaveContracting($id, $contractModel->contract_id, $contractModel->token, $post, $new_json['raw'], null, false, true);
                            self::update($id);
                            Yii::$app->session->setFlash('message', Yii::t('app', 'Контракт буде припинено пiсля завантаження доданих файлiв. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                            return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));
                        }

                        // если документов нет сразу отправляем статус
                        try {
                            $action = [
                                'data' => [
                                    'status' => 'terminated'
                                ]
                            ];

                            $terminate = Yii::$app->opAPI->contracts(
                                Json::encode($action),
                                $contractModel->contract_id,
                                $contractModel->token
                            );
                            self::update($id);
                            Yii::$app->session->setFlash('message', Yii::t('app', 'Контракт припинено.'));
                            return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));

                        } catch (apiDataException $e) {
                            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                        } catch (apiException $e) {
                            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                        }




                    } else {
                        // сбрасываем ецп
                        $contractModel->ecp = 0;
                        $contractModel->save(false);

                        //если есть документы, то ставим их на загрузку
                        if (isset($post['Contract']['documents']) && count($post['Contract']['documents']) > 0) {
                            $new_json = Yii::$app->opAPI->contracts(
                                null,
                                $contractModel->contract_id
                            );
                            DocumentUploadTask::updateTableAfterSaveContracting($id, $contractModel->contract_id, $contractModel->token, $post, $new_json['raw'], null, false, false);
                            self::update($id);
                            Yii::$app->session->setFlash('message', Yii::t('app', 'Контракт буде припинено пiсля накладання ЕЦП. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                            return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));
                        }

                        self::update($id);
                        Yii::$app->session->setFlash('message', Yii::t('app', 'Контракт буде припинено пiсля накладання ЕЦП. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                        return $this->redirect(Url::toRoute('/buyer/contracting/view/' . $id, true));

                    }







                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }


//                $data = Contracting::ConvertOut($post);
            } else {
                echo 'Нет связи с БД.';
            }

        }

        return $this->render('terminate', [
            'tenders' => $tenders,
            'contracts' => $contractModel,
            'contract' => \app\components\HContract::update($data),
        ]);
    }

    public static function update($id)
    {
        $model = new \app\models\ContractingUpdateTask();
        $model->cid = $id;
        $model->updateApi();
    }

    /**
     * Finds the Contracting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contracting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contracting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
