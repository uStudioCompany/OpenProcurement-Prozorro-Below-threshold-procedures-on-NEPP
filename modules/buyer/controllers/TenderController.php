<?php

namespace app\modules\buyer\controllers;

use app\components\HTender;
use app\components\HAward;
use app\models\AccessRule;
use app\models\CabinetEventSeller;
use app\models\Complaints;
use app\models\Contracting;
use app\models\Notifications;
use app\models\Plans;
use app\models\Questions;
use app\models\tenderModels\Complaint;
use app\models\tenderModels\Question;
use Yii;
use app\components\ApiHelper;
use app\components\HtmlHelper;
use app\components\SimpleTenderConvertOut;
use app\components\SimpleTenderConvertIn;
use app\models\DocumentUploadTask;
use app\models\FileUpload;
use app\models\tenderModels\Award;
use app\models\Tenders;
use app\models\Companies;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use app\components\apiDataException;
use app\components\apiException;
use yii\filters\AccessControl;
use app\models\tenderModels\Cancellation;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/**
 * Class TenderController
 * @package app\controllers
 */
class TenderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return AccessRule::checkAccess($action);
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('Forbidden', Yii::t('app', 'no.access'));
                    $this->redirect('/');
                },
            ],
        ];
    }


    public function actionCreate()
    {
        $post = Yii::$app->request->post();

        if (isset($post['simple_submit']) && !isset($post['drafts']) && !isset($post['autosave'])) {

            if ($tender = $this->publishTender($post)) {

                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tender->id, true));

            } else {
                // Если форма не прошла серверную валидацию
                $tender = HTender::update($post);
                $tender->validate();
            }

        } elseif (isset($post['drafts'])) { //если нажата кнопка "сохранить в черновик"

            $tenders = $this->saveTender($post);
            Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Данные успешно сохранены в черновик.'));
            return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));

        } elseif (isset($post['autosave'])) {

            if (!Yii::$app->request->isAjax) {
                throw new ErrorException('Что то не так.');
            }

            $tenders = $this->saveTender($post);

            $res = [
                'key' => Yii::$app->request->csrfToken,
                'tenderid' => $tenders->id];
            return Json::encode($res);

        } else {
            $tender = HTender::create();
        }

        return $this->render('create', [
            'tender' => $tender,
            'tenders' => null,
            'tenderId' => '',
            'published' => false
            //'files' => $files,
        ]);

    }

    public function actionUpdate($id)
    {
        //проверяем можно ли редактировать тендер
        if (!Tenders::CheckAllowedStatus($id, 'update')) {
            Yii::$app->session->setFlash('Forbidden', Yii::t('app', 'Статус тендера не дозволяє його редагувати.'));
            $this->goBack();
        }
        //проверяем владельца тендера
        // if (!Companies::checkCompanyIsTenderOwner($id)) {
        //      Yii::$app->session->setFlash('Forbidden', Yii::t('app', 'Редагувати тендер може тільки його власник'));
        //      $this->goBack();
        //  }
        $post = Yii::$app->request->post();

        if (isset($post['simple_submit']) && !isset($post['drafts'])) {
//            Yii::$app->VarDumper->dump($post, 10, true);die;
            if ($tender = $this->publishTender($post)) {

                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tender->id, true));

            } else {
                // Если форма не прошла серверную валидацию
                $tender = HTender::update($post);
                $tender->validate();
            }

        } elseif (isset($post['drafts'])) { //если нажата кнопка "сохранить в черновик"

            $tenders = $this->saveTender($post);
            Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Iнформацiя успiшно збережена до чернетки'));
            return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));

        } elseif (isset($post['autosave'])) {

            if (!Yii::$app->request->isAjax) {
                throw new ErrorException('Что то не так(тип запроса).');
            }

            $tenders = $this->saveTender($post);

            $res = [
                'key' => Yii::$app->request->csrfToken,
                'tenderid' => $tenders->id];
            return Json::encode($res);


        } else {
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        }

        $tenders = Tenders::getModelById($id);

        return $this->render('create', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);
    }

    public function actionView($id)
    {
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($tenders->id));

        $post = Yii::$app->request->post();
        if (isset($post['stage2_waiting'])) {

            $json = [
                'data' => [
                    'status' => 'active.stage2.waiting',
                ]
            ];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $post['tid'],
                    $tenders->token
                );
                self::update($tenders->id);
                Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Тендер буде переведено на другий етап'));
                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }


            Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Iнформацiя успiшно збережена до чернетки'));
            return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));
        } elseif (isset($post['stage2_active_tendering'])) {

            $json = [
                'data' => [
                    'status' => 'active.tendering',
                ]
            ];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $post['tid'],
                    $tenders->token
                );
                self::update($tenders->id);
                Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Тендер буде переведено на другий етап'));
                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }


            Yii::$app->session->setFlash('draft_success', Yii::t('app', 'Iнформацiя успiшно збережена до чернетки'));
            return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));
        }

        return $this->render('success', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);

    }

    public function actionCancel($id)
    {
        $tenders = Tenders::getModelById($id);
        $oldTenderData = $tenders->response;

        if ($post = Yii::$app->request->post()) {
            if ($tender = $this->publishCancel($tenders, $post)) {
                CabinetEventSeller::AddEventInCabinetIfTenderClose(Json::decode($oldTenderData), $post, $tenders);
                Notifications::SendMailCancelTenderOrLotToRequester(Json::decode($oldTenderData), $post, $tenders);
                Yii::$app->session->setFlash('message', Yii::t('app', 'Тендер скасовано. Статус оновиться на протязi 5 хвилин'));
                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $id, true));
            }
        }

        if (!$tenders->tender_id) {
            throw new ErrorException(Yii::t('app', 'Неможна скасувати неопублiкований тендер!'));
        }

        $tender = HTender::update(SimpleTenderConvertIn::getTenderInfo($tenders));

        $company = Companies::findOne(['id' => $tenders->company_id]);

        return $this->render('cancel', [
            'tender' => $tender,
            'tenders' => $tenders,
            'company' => $company,
            'tendersId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);
    }

    public function actionQuestions($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['answer_question_submit'])) {

            $this->SendQuestionAnswer($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
            Yii::$app->session->setFlash('message', Yii::t('app', 'Вiдповiдь успiшно вiдправлена.'));

        } elseif (isset($post['question_submit'])) {

            $this->SendQuestion($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
            Yii::$app->session->setFlash('message', Yii::t('app', 'Питання успiшно вiдправлено.'));
        } else {
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        }

        return $this->render('_question', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'question' => new Question()
        ]);
    }

    public function actionComplaints($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['answer_complaint_submit'])) {

            $this->SendComplaintsAnswer($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
            Yii::$app->session->setFlash('message', Yii::t('app', 'Вiдповiдь успiшно вiдправлена.'));
            //если complaint в преквалификации европейской процедуры. тут проверка с или потому что из разных форма разное приходит :(
            if ($post['complaint_type'] == 'prequalification_complaint_submit' || $post['type'] == 'prequalification') {
                return $this->redirect(Url::toRoute('/buyer/tender/prequalification-complaints?id=' . $tenders->id . '&prequalification=' . $post['target_id'], true));
            } else {
                return $this->redirect(Url::toRoute('/buyer/tender/complaints/' . $tenders->id, true));
            }


        } elseif (isset($post['award_complaint_submit'])) {
            if ($this->SendComplaintsAnswer($post, $tenders, 'award')) {
                return 'ok';
            }

        } elseif(isset($post['add_documents_to_complaints'])) {
            return $this->addDocsToComplaints($post,$tenders);

        } else {
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        }

        return $this->render('_complaints', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'complaint' => new Complaint()
        ]);
    }

    public function addDocsToComplaints($post,$tenders)
    {
        if (true) {
            $url = $tenders->tender_id . '/complaints/'. $post['cid'];
            switch ($post['type']) {
                case 'prequalification':
                    $url = $tenders->tender_id .'/qualifications/'. $post['target_id'] . '/complaints/'. $post['cid'];
                    break;
                case 'award':
                    $url = $tenders->tender_id .'/awards/'. $post['target_id'] . '/complaints/'. $post['cid'];
                    break;
            }
            DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url, $tenders->token, $post, 'complaint_an');
            Yii::$app->session->setFlash('message', Yii::t('app', 'Доданi файли будуть завантажуються на протязi 5 хвилин.'));
        }
        Yii::$app->session->setFlash('message', Yii::t('app', 'Файли завантажуються. Iнформацiя буде оновлена протягом 5 хвилин.'));
        if ($post['complaint_type'] == 'prequalification_complaint_submit' || $post['type'] == 'prequalification') {
            return $this->redirect(Url::toRoute('/buyer/tender/prequalification-complaints?id=' . $tenders->id . '&prequalification=' . $post['target_id'], true));
        } elseif($post['type'] == 'award') {
            return $this->redirect(Url::toRoute('/buyer/tender/qualification-complaints?id=' . $tenders->id . '&qualification=' . $post['target_id'], true));
        } else {
            return $this->redirect(Url::toRoute('/buyer/tender/complaints/' . $tenders->id, true));
        }
    }

    public function SendQuestion($post, $tenders)
    {
        $question['data']['author'] = SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
        $question['data']['title'] = $post['Question']['title'];
        $question['data']['description'] = $post['Question']['description'];

        if ($post['Question']['questionOf'] == 'tender') {
            $question['data']['questionOf'] = 'tender';
        } else {
            $question['data']['questionOf'] = explode('_', $post['Question']['questionOf'])[0];
            $question['data']['relatedItem'] = explode('_', $post['Question']['questionOf'])[1];
        }

        SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($question),
                $tenders->tender_id . '/questions'
            );

            // записывем, кто подал вопрос
            $questionModel = new Questions();
            $questionModel->question_id = $response['body']['data']['id'];
            $questionModel->company_id = Yii::$app->user->identity->company_id;
            $questionModel->user_id = Yii::$app->user->identity->id;
            $questionModel->tid = $tenders->id;
            $questionModel->create_at = time();
            $questionModel->save(false);

            self::update($tenders->id);

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function SendQuestionAnswer($post, $tenders)
    {
        $answer = [
            'data' => [
                'answer' => $post['Tender'][0]['answer']
            ]
        ];

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($answer),
                $tenders->tender_id . '/questions/' . $post['Tender'][0]['id'],
                $tenders->token);

            self::update($tenders->id);
            CabinetEventSeller::createEventForSellerFromTender($tenders->id, 'question', $post['Tender'][0]['id']);

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function actionContract()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $tenders = Tenders::findOne($post['t_id']);
            if (!Companies::checkCompanyIsTenderOwner($post['t_id'], $tenders)){
                return false;
            }
            if (isset($post['activate']) && $post['activate'] == 'activate') {

                ApiHelper::FormatDate($post, true);

                if ($tenders->tender_method == 'open_belowThreshold') { // если допороговая процедура
                    $data = [
                        'data' => [
                            'status' => 'active',
                            'contractNumber' => array_values($post['Contract'])[0]['contractNumber'],
                            'period' => [
                                'startDate' => array_values($post['ContractPeriod'])[0]['startDate'],
                                'endDate' => array_values($post['ContractPeriod'])[0]['endDate']
                            ],
                            'dateSigned' => array_values($post['Contract'])[0]['dateSigned']
                        ]
                    ];
                }


//                Yii::$app->VarDumper->dump($data, 10, true);die;
//                $data = '{"data":{"status":"active"}}';
                try {
                    $response = Yii::$app->opAPI->tenders(Json::encode($data), $tenders->tender_id . '/contracts/' . $post['contract_id'], $tenders->token);
                    self::update($tenders->id);

                    //записываем, что нажал пользователь
                    $res = Tenders::findOne(['tender_id' => $tenders->tender_id]);
                    if ($res->user_action) {
                        $userAction = Json::decode($res->user_action);
                    } else {
                        $userAction = [];
                    }

                    $userAction['Contracts'][$post['contract_id']] = 'activate';

                    $res->user_action = Json::encode($userAction);
                    $res->save(false);
                    //-----------------------------------

                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные -' . $e->getErrors(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }
            } elseif (isset($post['t_id']) && $post['t_id']) {
                DocumentUploadTask::updateTableAfterSaveContract($tenders->id, $tenders->tender_id . '/contracts/' . $post['contract_id'], $tenders->token, $post, $tenders->response, $post['contract_id']);
            }
        }
    }

    public static function update($id)
    {
        $model = new \app\models\TenderUpdateTask();
        $model->tid = $id;
        $model->updateTenderApi();
    }

    public function actionPrequalificationComplaints($id, $prequalification)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id),'eu_prequalification');

        return $this->render('_prequalification_complaints', [
            'complaint' => new Complaint(),
            'tender' => $tender,
            'tenders' => $tenders,
            'qualifications_id'=>$prequalification,
            'tenderId' => $id,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id)
        ]);
    }

    public function actionQualificationComplaints($id, $qualification)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);
        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        $currentAwardModel = Award::getAwardById($tender, $qualification);

        foreach ($tender->awards as $a => $item) {
            if ($item->id == $qualification) {
                $awardComplaints = $item->complaints;
                break;
            }
        }

        if ($post) {
            if (isset($post['documents'])) {
                if (isset($post['tendererAction'])) {
                    $json = [
                        'data' => [
                            'resolutionType' => $post['Tender'][0]['resolutionType'],
                            'resolution' => $post['tendererAction'],
                            'tendererAction' => $post['tendererAction']
                        ]
                    ];
                }
                $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints/' . $post['complaintId'];
                try {
                    if (isset($post['tendererAction'])) {
                        $response = Yii::$app->opAPI->tenders(
                            Json::encode($json),
                            $url,
                            $tenders->token
                        );
                        self::update($tenders->id);
                    }
                    DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url, $tenders->token, $post, 'q_complaint');
                    Yii::$app->session->setFlash('message', Yii::t('app', 'Вiдповiдь успiшно надана. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                    return $this->redirect(Url::current());
                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }
            } else {
//                Yii::$app->VarDumper->dump($post, 10, true, true);
                if (isset($post['tendererAction'])) {

                    if (!in_array($tenders->tender_method, ['open_belowThreshold']) && $post['complaintGurrentStatus'] == 'satisfied' ) {
                        $json = [
                            'data' => [
                                'status' => 'resolved',
                                'tendererAction' => $post['tendererAction']
                            ]
                        ];

                        // отменяем победителя в аварде
                        try {
                            Yii::$app->opAPI->awards(
                                json_encode(['data' => ["status" => "cancelled"]]),
                                $tenders->tender_id,
                                $tenders->token,
                                $post['awardId']);

                        } catch (apiException $e) {
                            return 'API Error <pre>' . $e->getMessage() . '</pre>';
                        }


                    }else {
                        $json = [
                            'data' => [
                                'status' => 'answered',
                                'resolutionType' => $post['Tender'][0]['resolutionType'],
                                'resolution' => $post['tendererAction'],
                                'tendererAction' => $post['tendererAction']
                            ]
                        ];
                    }
                    $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints/' . $post['complaintId'];

                    try {
                        $response = Yii::$app->opAPI->tenders(
                            Json::encode($json),
                            $url,
                            $tenders->token
                        );

                        self::update($tenders->id);
                        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
                        Yii::$app->session->setFlash('message', Yii::t('app', 'Вiдповiдь успiшно надана.'));
                        return $this->redirect(Url::current());

                    } catch (apiDataException $e) {
                        throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    } catch (apiException $e) {
                        throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    }


                }
            }


            return $this->render('_qualification_complaints', [
                'complaint' => new Complaint(),
                'tender' => $tender,
                'tenders' => $tenders,
                'tenderId' => $id,
                'awardComplaints' => $awardComplaints,
                'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id),
                'currentAwardModel' => $currentAwardModel
            ]);


        }


        return $this->render('_qualification_complaints', [
            'complaint' => new Complaint(),
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'awardComplaints' => $awardComplaints,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id),
            'currentAwardModel' => $currentAwardModel
        ]);
    }

    public function actionAward($id)
    {
        $tenders = Tenders::getModelById($id);

        if (!$tenders->tender_id) {
            throw new ErrorException(Yii::t('app', 'Неможна скасувати неопублiкований тендер!'));
        }

        if ($post = Yii::$app->request->post()) {

            if (!in_array($type = $post['type'], ApiHelper::$_award_types)) {
                die(HtmlHelper::printErr(Yii::t('app', 'Error! Unknown type')));
            }

            if (!$award = ApiHelper::checkAwardId($post['awardId'], $tenders->response)) {
                die(HtmlHelper::printErr(Yii::t('app', 'Error! Unknown award id')));
            }

            $file_name = '';
            $mime = '';
            $title = '';

            if (in_array($type, ['active','unsuccessful','extend'])) {

                $data = array_values($post['Award'])[0];

                //если не нажата галочка в допороговом тендере "Я хочу подписать"

                $json = [];
                if (!isset($post['need_award_esign']) && $tenders->tender_method == 'open_belowThreshold') {
                    $json = [
                        'data' => [
                            'status' => $type,
                        ]
                    ];
                } elseif (isset($post['need_award_esign']) && $tenders->tender_method == 'open_belowThreshold') {
                    $json = [
                        'data' => [
                            'status' => 'pending',
                        ]
                    ];
                }

//Yii::$app->VarDumper->dump($post, 10, true);die;
//Yii::$app->VarDumper->dump($tenders->tender_method, 10, true);die;
                if ($type == 'active') {




                } elseif ($type == 'unsuccessful') {

                    $title = implode(', ', $post['Award']['cause']);


                }elseif ($type == 'extend'){
                    $post['documents'][0]['title'] = Yii::t('app','Повідомлення про продовження строку розгляду тендерної пропозиції');
                }

                $url = $tenders->tender_id . '/awards/' . $post['awardId'];

                if (isset($post['documents'])) {
                    DocumentUploadTask::updateTableAfterSaveAward($tenders->id, $url, $tenders->token, $post, $tenders->response, $post['awardId']);

                    if (in_array($type,['extend'])) {
                        Yii::$app->session->setFlash('message', Yii::t('app', Yii::t('app','Файли завантажуються. Iнформацiя буде оновлена протягом 5 хвилин.')));
                        return $this->redirect(Url::toRoute('/buyer/tender/award/' . $id, true));
                    }else{
                        DocumentUploadTask::forceDocument($post['documents'][0]['realName']);
                    }
                }

                if (count($json)) {
//                    Yii::$app->VarDumper->dump($json, 10, true);die;
                    try {
                        $response = Yii::$app->opAPI->tenders(
                            Json::encode($json),
                            $url,
                            $tenders->token
                        );

                        //записываем, что нажал пользователь
                        $res = Tenders::findOne(['tender_id' => $tenders->tender_id]);
                        if ($res->user_action) {
                            $userAction = Json::decode($res->user_action);
                        } else {
                            $userAction = [];
                        }

                        $userAction['Awards'][$post['awardId']] = $type;

                        $res->user_action = Json::encode($userAction);
                        $res->save(false);
                        //-----------------------------------

                        self::update($tenders->id);
                        return $this->redirect(Url::toRoute('/buyer/tender/award/' . $id, true));


                    } catch (apiDataException $e) {
                        throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    } catch (apiException $e) {
                        throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                    }
                } else {
                    self::update($tenders->id);
                    return $this->redirect(Url::toRoute('/buyer/tender/award/' . $id, true));
                }
            } else if ($type === 'cancelled') {

                try {
                    $response = Yii::$app->opAPI->awards(
                        json_encode(['data' => ["status" => "cancelled"]]),
                        $tenders->tender_id,
                        $tenders->token,
                        $award['id']);
                    $this->actionJson($id);
                } catch (apiException $e) {
                    return 'API Error <pre>' . $e->getMessage() . '</pre>';
                }

            } else if ($type === 'tendererAction') {

                $json = [
                    'data' => [
                        'status' => 'answered',
                        'resolutionType' => 'resolved',
                        'resolution' => $post['tendererAction'],
                        'tendererAction' => $post['tendererAction']
                    ]
                ];

                $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints/' . $post['complaintId'];

                try {
                    $response = Yii::$app->opAPI->tenders(
                        Json::encode($json),
                        $url,
                        $tenders->token
                    );
//Yii::$app->VarDumper->dump($response, 10, true);
                    self::update($tenders->id);
                    return true;

                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }

            }

            return $this->renderPartial('_award_msg', [
                'tenders' => $tenders,
                'type' => $type,
                'award' => $award,
                'tendersId' => $id,]);
        }

        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders));
//        Yii::$app->VarDumper->dump($tender, 10, true);die;

        $company = Companies::findOne(['id' => $tenders->company_id]);


        return $this->render('award', [
            'tender' => $tender,
            'tenders' => $tenders,
            'company' => $company,
            'tendersId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);
    }

    public function actionAward_form($id) //_unsuccessful
    {
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders));
        if (!in_array($type = $_GET['type'], ApiHelper::$_award_types)) {
            die(Yii::t('app', 'Error! Unknown type'));
        }

        if (!$award = ApiHelper::checkAwardId($_GET['award_id'], $tenders->response)) {
            die(Yii::t('app', 'Error! Unknown award id'));
        }

        return $this->renderPartial('_award_form', [
            'model' => $tenders,
            'tender' => $tender,
            'type' => $type,
            'award' => $award,
            'tendersId' => $id,
        ]);
    }

    public function SendComplaintsAnswer($post, $tenders, $type = 'simple')
    {

        if (isset($post['complaint_type'])) {
            if ($post['complaint_type'] == 'award_complaint_submit') {
                $type = 'award';
            }
            if ($post['complaint_type'] == 'prequalification_complaint_submit') {
                $type = 'prequalification';
            }
        }

        switch ($type) {
            case ('simple'):
                $url = $tenders->tender_id . '/complaints/' . $post['Tender'][0]['id'];
                break;
            case ('award'):
                $url = $tenders->tender_id . '/awards/' . $post['Tender'][0]['award_id'] . '/complaints/' . $post['Tender'][0]['complaint_id'];
                break;
            case ('prequalification'):
                $url = $tenders->tender_id . '/qualifications/' . $post['target_id'] . '/complaints/' . $post['Tender'][0]['complaint_id'];
                break;
        }

        if (isset($post['documents'])) {

            $answer = [
                'data' => [
                    'resolutionType' => $post['Tender'][0]['resolutionType'],
                    'resolution' => $post['Tender'][0]['resolution'],
                ]
            ];
//Yii::$app->VarDumper->dump($answer, 10, true);die;
            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($answer),
                    $url,
                    $tenders->token
                );

                self::update($tenders->id);
                if ($type == 'simple') {
                    CabinetEventSeller::createEventForSellerFromTender($tenders->id, 'complaint', $post['Tender'][0]['id']);
                } elseif ($type == 'award') {
                    CabinetEventSeller::createEventForSellerFromTender($tenders->id, 'award', $post['Tender'][0]['complaint_id']);
                } elseif ($type == 'prequalification') {
                    //???
                }
                DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url, $tenders->token, $post, 'complaint_an');
                return true;

            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }


        } else {

            $answer = [
                'data' => [
                    'status' => 'answered',
                    'resolutionType' => $post['Tender'][0]['resolutionType'],
                    'resolution' => $post['Tender'][0]['resolution'],
                ]
            ];

//        Yii::$app->VarDumper->dump($answer, 10, true);die;
            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($answer),
                    $url,
                    $tenders->token
                );
                self::update($tenders->id);
                if ($type == 'simple') {
                    CabinetEventSeller::createEventForSellerFromTender($tenders->id, 'complaint', $post['Tender'][0]['id']);
                } elseif ($type == 'award') {
                    CabinetEventSeller::createEventForSellerFromTender($tenders->id, 'award', $post['Tender'][0]['complaint_id']);
                } elseif ($type == 'prequalification') {
                    /// ????
                }
                return true;

            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }


    }

    public function saveTender($post)
    {
        unset($post['_csrf']);
        $tenders = Tenders::getModel($post);
//        $tenders->tender_type = $post['tender_type'];
//        $tenders->tender_method = $post['tender_method'];
        $post = SimpleTenderConvertIn::PrepareToDraft($post);
        $tenders->attributes = [
            'title' => isset($post['Tender']['title']) ? $post['Tender']['title'] : $tenders->title,
            'description' => isset($post['Tender']['description']) ? $post['Tender']['description'] : $tenders->description,
            'tender_type' => isset($post['tender_type']) ? $post['tender_type'] : $tenders->tender_type,
            'tender_method' => isset($post['tender_method']) ? $post['tender_method'] : $tenders->tender_method,
            'json' => Json::encode($post)
        ];
        if ($tenders->status == null) {
            $tenders->status = 'draft';
        }

        $tenders->save(false);
        return $tenders;
    }

    public function publishCancel($tenders, $post)
    {

        $cancel = new Cancellation();

        if (SimpleTenderConvertOut::prepareToValidateCancel($post, $cancel)) {
            if ($cancel_id = SimpleTenderConvertOut::sendCancellations($tenders->id, $tenders->tender_id, $tenders->token, $post, $tenders->response)) {
                DocumentUploadTask::updateTableAfterSaveCancel($tenders->id, $tenders->tender_id . '/cancellations/' . $cancel_id, $tenders->token, $post['Tender']['cancellations'], $tenders->response);
                return true;
            }
        }
        return false;
    }

    public function publishTender($post)
    {

        $tender = HTender::load();

        if (!ApiHelper::checkTenderMethod($post)) {
            Yii::$app->session->setFlash('message_error', Yii::t('app', 'Unknown tender method'));
            return false;
        }
//Yii::$app->VarDumper->dump($post, 10, true, true);
        if (SimpleTenderConvertOut::prepareToValidate($post, $tender)) {
            //формируем массив json
            $data = SimpleTenderConvertOut::prepareToAPI($post); //die('<pre>'. print_r($data,1));

            //проверяем, было ли автосохранение и достаем модель
            $tenders = Tenders::getModel($post);

            //сохраняем тендер перед отправкой, только если это не переход на второй этап
            if (!in_array($post['tender_method'], Yii::$app->params['2stage.tender'])) {
                $tenders->title = $post['Tender']['title'];
                $tenders->description = $post['Tender']['description'];
                $tenders->tender_type = $post['tender_type'];
                $tenders->tender_method = $post['tender_method'];
                $tenders->json = Json::encode(['Tender' => $post['Tender']]);
                if (!$tenders->save(false)) {
                    Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, save data'));
                    return false;
                }
            }
            try {

                $old_lots = [];
                if ($tenders->response && $post['tender_type'] == 2) {

                    $lot_ids = [];
                    $new_lots = $data['data']['lots'];
                    $old_lots = json_decode($tenders->response, 1)['data']['lots'];
                    unset($data['data']['lots']);

                    foreach ($old_lots as $key => $old_lot) {
                        $lot_ids[] = $old_lot['id'];
                        $old_lots[$old_lot['id']] = $old_lot;
                        unset($old_lots[$key]);
                    }

                    foreach ($new_lots AS $key => $new_lot) {
                        if (in_array($new_lot['id'], $lot_ids)) {
                            // update LOT
                            $lot_response = Yii::$app->opAPI->lots(Json::encode(['data' => $new_lot]), $tenders->tender_id, $tenders->token, $new_lot['id']);
                        } else {
                            // create LOT
                            $lot_response = Yii::$app->opAPI->lots(Json::encode(['data' => $new_lot]), $tenders->tender_id, $tenders->token, null);
                        }
                        unset($old_lots[$new_lot['id']]);
                    }

                    /**
                     * При удалении лота
                     * Привязываем документы лотов к тендеру
                     */
                    if (count($old_lots)) {
                        $old_documents = json_decode($tenders->response, 1)['data']['documents'];
                        $old_items = json_decode($tenders->response, 1)['data']['items'];
                        foreach ($old_lots as $key => $old_lot) {
                            foreach ($old_documents AS $doc) {
                                if ($doc['documentOf'] === 'tender') continue;
                                if ($doc['documentOf'] === 'lot' && $doc['relatedItem'] === $key) {
                                    // Документы привызанные к лоту
                                    $doc_response = Yii::$app->opAPI->tendersDocuments(null, json_encode(['data' => ['documentOf' => 'tender', 'relatedItem' => null]]), $tenders->tender_id, $tenders->token, $doc['id']);
                                } else if ($doc['documentOf'] === 'item') {
                                    foreach ($old_items as $old_item) {
                                        if ($old_item['id'] === $doc['relatedItem'] && $old_item['relatedLot'] === $key) {
                                            // Документы привязанные к ителу из этого лота
                                            $doc_response = Yii::$app->opAPI->tendersDocuments(null, json_encode(['data' => ['documentOf' => 'tender', 'relatedItem' => null]]), $tenders->tender_id, $tenders->token, $doc['id']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                /**
                 * отправляем данные(TENDERS) в ЦБД
                 */

                if ($tenders->tender_method == 'open_belowThreshold') {
                    $data['data']['submissionMethodDetails'] = 'quick';
                    $data['data']['procurementMethodDetails'] = 'quick, accelerator=1440';
                }
                $cookies = Yii::$app->request->cookies;
                if ($cookies['auction-mode']->value == 'test' || $_COOKIE['auction-mode'] == 'test') {
                    $data['data']['mode'] = 'test';
                }
//                $data['data']['procurementMethodDetails'] = 'quick, accelerator=1440';
//                $data['data']['tenderPeriod']['startDate'] = date('c', strtotime('now'));
//                $data['data']['tenderPeriod']['endDate'] = '2016-08-05T16:00:00+03:00';

//                print_r(Json::encode($data));die;
//                print_r(json_encode($data));die;
//                Yii::$app->VarDumper->dump($data, 10, true, true);
//                Yii::$app->VarDumper->dump(Json::encode($data), 10, true);die;
//                Yii::$app->VarDumper->dump($post, 10, true);die;
//                print_r(json_encode($data));die;
//                $response = Yii::$app->opAPI->tenders($data, $tenders->tender_id, $tenders->token); //echo '<pre>'; print_r($response); die();

                if ($tenders->token) {
                    $response = Yii::$app->opAPI->tenders(json_encode($data), $tenders->tender_id, $tenders->token);
                } else {

                    /**
                     * Новый тендер отправляем черновиком
                     * А потом активируем, согласно Yii::$app->params['active_statuses']
                     */
                    $draft_token = null;
                    $data['data']['status'] = 'draft';
                    for ($i=0;$i<=Yii::$app->params['two_phase_commit_count'];$i++) {
                        try {
                            if (!$draft_token) {
                                $response = Yii::$app->opAPI->tenders(json_encode($data), $tenders->tender_id, $tenders->token);
                                if (isset($response['body']['access']) && $response['body']['access']) {
                                    $draft_token = $response['body']['access']['token'];
                                    $tenders->tender_id = $response['body']['data']['id'];
                                    // токен миграции
                                    if(isset($response['body']['access']['transfer'])){
                                        $tenders->transfer_token = $response['body']['access']['transfer'];
                                    }
                                }
                            }
                            if ($draft_token) {
                                $response = Yii::$app->opAPI->tenders(json_encode(['data' => ['status' => Yii::$app->params['active_statuses'][$tenders->tender_method]]]), $tenders->tender_id, $draft_token);
                                break;
                            }
                        } catch (\Exception $e) {
                            /** Игнорим ошибку 5 раз (Yii::$app->params['two_phase_commit_count']) */
                            if ( substr($e->getCode(),0,1) != '5') {
                                throw $e;
                            }
                            if ($i == Yii::$app->params['two_phase_commit_count']) {
                                Yii::$app->session->setFlash('message_error', Yii::t('app', 'error create tender on CBD'));
                                return $tenders;
                            }
                        }
                    }
                    /* ---------------------------------- */
                }


                $tenders->ecp = 0;
                //лист-створення тендеру власнику
                if($tenders->save(false)) {
                    Notifications::createTender($tenders);
                }
                /**
                 * Удаляем лоты
                 */
                if (count($old_lots)) {
                    foreach ($old_lots as $key => $old_lot) {
                        $lot_response = Yii::$app->opAPI->lots(null, $tenders->tender_id, $tenders->token, $old_lot['id']);
                    }

                    // Обновляем тендер, после удаления лотов...
                    $response = Yii::$app->opAPI->tenders(null, $tenders->tender_id, $tenders->token);
                }

                if (isset($draft_token) && $draft_token) { //(isset($response['body']['access'])) {
                    $tenders->token = $draft_token; // $response['body']['access']['token'];
                    $tenders->tender_id = $response['body']['data']['id'];
                }
                $tenders->status = $response['body']['data']['status'];
                $tenders->date_modified = $response['body']['data']['dateModified'];
                $tenders->response = $response['raw'];
                $tenders->tender_cbd_id = Json::decode($response['raw'])['data']['tenderID'];

                $cookies = Yii::$app->request->cookies;
                if($cookies['auction-mode']->value == 'test' || $_COOKIE['auction-mode'] == 'test'){
                    $tenders->test_mode = 1;
                }else{
                    $tenders->test_mode = 0;
                }

                $tenders->save(false);

                SimpleTenderConvertOut::sendCancellations($tenders->id, $tenders->tender_id, $tenders->token, $post, $tenders->response);

                DocumentUploadTask::updateTableAfterSave($tenders->id, $tenders->tender_id, $tenders->token, $post, $tenders->response);

                if (!$tenders->save(false)) {
                    //throw new ErrorException('Не удалось сохранить данные');
                    Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, save data'));
                    return false;
                }

            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getErrors(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
            Yii::$app->session->setFlash('tender_added', Yii::t('app', 'tender_sended'));
            return $tenders;

        } else {
            return false;
        }
    }

    public function actionFileupload()
    {
        $model = new FileUpload();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $tenderId = Yii::$app->request->post('tenderId');
                $hash = Yii::$app->security->generateRandomString(12);
                $path = Yii::$app->params['upload_dir'] . $model->file->baseName . '' . $hash . '.' . $model->file->extension;
                $newName = $model->file->baseName . '' . $hash . '.' . $model->file->extension;
                $model->file->saveAs($path);
                //сохраняем файл в таблицу с файлами
                DocumentUploadTask::addFile($newName, $tenderId);

                $fileArr = [
                    'path' => $path,
                    'newName' => $newName,
                    'model' => $model->file
                ];
            }
        }
        return Json::encode($fileArr);
    }

    public function actionFiledelete($file)
    {
        if (Yii::$app->request->isGet) {
            $files = \yii\helpers\FileHelper::findFiles('uploads/', [
                'only' => [$file],
                'except' => ['*.DS_Store']
            ]);

            $res = @unlink($files[0]);
            DocumentUploadTask::removeFile($file);
        }
        return Json::encode($res);
    }

    public function actionTest()
    {

        $query = Tenders::find()
            ->select()
            ->join('LEFT JOIN', 'bids', 'tenders.id = bids.tid')
//            ->join('LEFT JOIN', 'user')
            ->where([
                'tenders.status' => 'active.auction',
            ])
            ->all();

        ini_set('memory_limit', '-1');


        Yii::$app->VarDumper->dump($query, 10, true);
        die;
    }

    public function beforeAction($action)
    {
        if ($action->id == 'fileupload' || $action->id == 'filedelete') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionJson($id)
    {

        self::update($id);
    }

    public function actionLimitedavards($id)
    {

        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders));
        $tenderType = $tenders->tender_type;
        if (isset($post['cancel'])){
            return $this->redirect(Url::toRoute('/buyer/tender/view/' . $id, true));
        }
        if (isset($post['add_limited_avards'])) {
            $award = HAward::load($post, 'limitedavards');
            if ($award && $award->validate()) {
                $data = SimpleTenderConvertOut::prepearLimitedAvards($id, $post);

                if ($this->SendLimitedAvards($data, $tenders, $award->id)) {
                    //получаем обновленные данные
                    $tenders = Tenders::getModelById($id);
                    $response = Json::decode($tenders->response);

                    if ($response['data']['awards'][count($response['data']['awards']) - 1]['status'] != 'canceled') {
                        $awardId = $response['data']['awards'][count($response['data']['awards']) - 1]['id'];
                        unset($post['documents']['__CONTRACT_DOC__']);
                        if (isset($post['documents'])) {
                            $docPost['documents'] = $post['documents']; // ????????

                            DocumentUploadTask::updateTableAfterSaveAward(
                                $tenders->id,
                                $tenders->tender_id . '/awards/' . $awardId,
                                $tenders->token,
                                $docPost,
                                $tenders->response,
                                $awardId
                            );
                        }
                    }

                    Yii::$app->session->setFlash('tender_added', Yii::t('app', 'Победитель успешно назначен.'));
                    return $this->redirect(Url::toRoute('/buyer/tender/view/' . $id, true));
                }

            } else {
                $award = HAward::update($post, 'limitedavards');
                $award->validate();
            }

        } elseif (is_array($res = SimpleTenderConvertIn::getLimitedAward($tenders))) { //update
            if ($tenderType ==2) {
                foreach ($res as $aw) {
                    $awards[$aw['lotID']] = HAward::update(['Award' => $aw], 'limitedavards');
                }
                $activeLots = Award::checkLotAwardsForDropdown($tender);
                foreach ($activeLots as $lotId => $lotTitle) {
                    if (!in_array($lotId, array_keys($awards))) {
                        $awards[$lotId] = HAward::create('limitedavards');
                    }
                }
            } else {
                if (empty($res)) {
                    $awards[] = HAward::create('limitedavards');
                } else {
                    $awards[] = HAward::update(['Award' => $res[0]], 'limitedavards');
                }
            }
        } else { // create
            if ($tenderType == 2) {
                $activeLots = Award::checkLotAwardsForDropdown($tender);
                foreach ($activeLots as $lotId => $lotTitle) {
                    $awards[$lotId] = HAward::create('limitedavards');
                }
            } else {
                $awards[] = HAward::create('limitedavards');
            }
        }
        return $this->render('limited/_limited_avards', [
            'awards' => $awards,
            'tenders' => $tenders,
            'tender' => $tender,
            'tenderAmount' => Json::decode($tenders->response)['data']['value']['amount']
        ]);

    }

    public function SendLimitedAvards($data, $tenders, $awardId)
    {
        unset($data['data']['id']);
        try {
            $response = Yii::$app->opAPI->awards(
                Json::encode($data),
                $tenders->tender_id,
                $tenders->token,
                $awardId
            );
            self::update($tenders->id);
            return true;

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }

    }



    public function actionSetqualificationstatus()
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($post['tenderId']);
//        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders), 'eu_prequalification');


        if ($post['action'] == 'active') {
            $json = ['data' => ['status' => 'active',]];
        } elseif ($post['action'] == 'unsuccessful') {
            $json = ['data' => ['status' => 'unsuccessful',]];
        }
        $url = $post['tender_id'] . '/qualifications/' . $post['qualId'];
//        Yii::$app->VarDumper->dump($url, 10, true);die;

        if (self::SendEcpFile($post, 'qualifications')) {
            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );
                self::update($tenders->id);
                //добавляем новость в кабинет о результате квалификации
                CabinetEventSeller::AddPrequalificationEvent($tenders, $post['qualId'], $post['action']);
            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }
    }

    public function actionEuprequalification($id)
    {
        $tenders = Tenders::getModelById($id);
//        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders), 'eu_prequalification');

        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id), 'eu_prequalification');  //если не передать сценарий, то отпадают валидаторы

//        Yii::$app->VarDumper->dump($tender->bids, 10, true);die;
        $post = Yii::$app->request->post();

        if (isset($post['send_prequalification'])) {

            $data = array_values($post['Qualifications'])[0];

            if ($data['action'] == 'active') {

                $json = [
                    'data' => [
                        'eligible' => true,
                        'qualified' => true
                    ]
                ];
            } elseif ($data['action'] == 'unsuccessful') {

                $title = implode(', ', $post['Qualifications']['cause']);
                $json = [
                    'data' => [
                        'title' => $title,
                        'description' => $data['description']
                    ]
                ];
            }
            $url = $tenders->tender_id . '/qualifications/' . $data['id'];

            if (isset($post['documents'])) {
                DocumentUploadTask::updateTableAfterSavePrequalification($tenders->id, $url, $tenders->token, $post, $tenders->response, $data['id']);
//                $upload = new CronTaskController();
//                $upload->actionForceDocument($post['documents'][0]['realName']);
                DocumentUploadTask::forceDocument($post['documents'][0]['realName']);
//                system(".." . DIRECTORY_SEPARATOR . "yii cron-task/force-document " . $post['documents'][0]['realName']);
            }

            //записываем, что нажал пользователь
            $res = Tenders::findOne(['tender_id' => $tenders->tender_id]);
            if ($res->user_action) {
                $userAction = Json::decode($res->user_action);
            } else {
                $userAction = [];
            }

            $userAction['Qualifications'][$data['id']] = $data['action'];

            $res->user_action = Json::encode($userAction);
            $res->save(false);
            

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );
                self::update($tenders->id);
                return $this->redirect(Url::toRoute('/buyer/tender/euprequalification/' . $id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }

        } elseif (isset($post['prequalification_next_status'])) {
            $json = [
                'data' => [
                    'status' => 'active.pre-qualification.stand-still',
                ]
            ];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $post['tid'],
                    $tenders->token
                );
                self::update($tenders->id);
                return $this->redirect(Url::toRoute('/buyer/tender/view/' . $tenders->id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }

        } elseif (isset($post['send_precvalification_complain_resolved_answer'])) {


            // посылаем сообщение

            $data = array_values($post['Complaint'])[0];

            $json = [
                'data' => [
                    'status' => 'resolved',
                    'tendererAction' => $data['tendererAction'],
                ]
            ];

            $url = $tenders->tender_id . '/qualifications/' . $data['qualification_id'] . '/complaints/' . $data['complaint_id'];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );

                self::update($tenders->id);
                return $this->redirect(Url::toRoute('/buyer/tender/euprequalification/' . $id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }

            //затем отменяем квалификацию(этот функционал не тестирован т.к. не готова ЦБД)

            $json = [
                'data' => [
                    'status' => 'cancelled',
                ]
            ];

            $url = $tenders->tender_id . '/qualifications/' . $data['qualification_id'];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );

//                self::update($tenders->id);
//                return $this->redirect(Url::toRoute('/buyer/tender/euprequalification/' . $id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }




        } elseif (isset($post['cancel_prequalification'])) {

            // если есть удовлетворенные жалобы, то резолвим
            Complaints::resolvedSatisfiedComplaint($tender, $tenders, $post);

            $data = array_values($post['Qualifications'])[0];
            $json = [
                'data' => [
                    'status' => 'cancelled',
                ]
            ];

            $url = $tenders->tender_id . '/qualifications/' . $data['id'];

            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );

                self::update($tenders->id);
                return $this->redirect(Url::toRoute('/buyer/tender/euprequalification/' . $id, true));


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }


        }

        return $this->render('euprocedure/_eu_prequalification', [
            'tenders' => $tenders,
            'tender' => $tender
        ]);
    }

    public function actionSetcontractsign()
    {
        $post = Yii::$app->request->post();
        if(self::SendEcpFile($post, 'contracts')){
            //добавляем новость всем участникам, о том что был выбран победитель
            CabinetEventSeller::AddEventIfContractActivate($post);
        }
    }

    public function actionSetawardstatus()
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($post['tenderId']);
//        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders), 'eu_prequalification');

        if ($post['action'] == 'active') {
            $json = ['data' => ['status' => 'active',]];
        } elseif ($post['action'] == 'unsuccessful') {
            $json = ['data' => ['status' => 'unsuccessful',]];
        }
        $url = $post['tender_id'] . '/awards/' . $post['awardId'];

        
        if (self::SendEcpFile($post, 'awards')) {
            try {
                $response = Yii::$app->opAPI->tenders(
                    Json::encode($json),
                    $url,
                    $tenders->token
                );
                self::update($tenders->id);
                //добавляем новость в кабинет, о том что статус ставки пользователя изменен
                CabinetEventSeller::AddEventIfAwardChange($tenders, $post['awardId']);
            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }
    }

    public function actionSetContractingEcp()
    {
        $post = Yii::$app->request->post();
        $contractModel = Contracting::getModel($post['cid']);
//        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders), 'eu_prequalification');

//        if(isset($post['status'])) {
//            if ($post['status'] == 'active') {
//                $json = ['data' => ['status' => 'active',]];
//            } elseif ($post['status'] == 'unsuccessful') {
//                $json = ['data' => ['status' => 'unsuccessful',]];
//            }
//        }
        try {
            self::SendEcpFile($post, 'contracting');

//                $response = Yii::$app->opAPI->tenders(
//                    Json::encode($json),
//                    $contractModel->contract_id,
//                    $contractModel->token
//                );
            ContractingController::update($contractModel->id);
            $contractModel->ecp = 1;
            $contractModel->save(false);
        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function actionTenderecp()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ErrorException('Что то не так.');
        }

        $post = Yii::$app->request->post();

        $tenders = Tenders::find()->where(['tender_id'=>$post['tender_id']])->one();
        $tenders->ecp = 1;
        $tenders->save(false);


        self::SendEcpFile($post, 'tender');
    }

    public function actionPlanecp()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ErrorException('Что то не так.');
        }

        $post = Yii::$app->request->post();
        self::SendEcpFile($post, 'plan');

    }

    public static function SendEcpFile($post, $type)
    {
        $random = Yii::$app->security->generateRandomString();
        $dir = Yii::$app->params['upload_dir'] . DIRECTORY_SEPARATOR . 'ecp' . DIRECTORY_SEPARATOR . $random;
        if (mkdir($dir, 0777, true)) {
            file_put_contents($dir . DIRECTORY_SEPARATOR . 'sign.p7s', $post['data']);
        } else {
            die('Failed to create folders...');
        }

        switch ($type) {
            case ('tender'):
                $tenders = Tenders::find()->where(['tender_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['tender_id'];
                break;
            case ('qualifications'):
                $tenders = Tenders::find()->where(['tender_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['tender_id'] . '/qualifications/' . $post['qualId'];
                break;
            case ('awards'):
                $tenders = Tenders::find()->where(['tender_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['tender_id'] . '/awards/' . $post['awardId'];
                break;
            case ('contracting'):
                $tenders = Contracting::find()->where(['id' => $post['cid']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['contract_id'];

                $res = Json::decode($tenders[0]['json']);
                if(isset($res['data']['status']) && $res['data']['status'] == 'terminated'){
                    $action = [
                        'data' => [
                            'status' => 'terminated'
                        ]
                    ];
                }
                break;
            case ('contracts'):
                $tenders = Tenders::find()->where(['tender_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['tender_id'] . '/contracts/' . $post['contractId'];
                $action = [
                    'data' => [
                        'status' => 'active'
                    ]
                ];
                break;
            case ('plan'):
                $tenders = Plans::find()->where(['plan_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['plan_id'];
                break;
        }
//Yii::$app->VarDumper->dump($tenders, 10, true);die;
        $model = new DocumentUploadTask();

        $model->tid = $tenders[0]['id'];
        $model->file = 'ecp' . DIRECTORY_SEPARATOR . $random . DIRECTORY_SEPARATOR . 'sign.p7s';
        $model->title = 'sign';

        // зашиваем сюда активацию контракта после подписи
        if (isset($action)) {
            $model->exec_json = Json::encode($action);
        }


        $model->mime = 'application/pkcs7-signature';
        $model->tender_id = $uploadUrl;
        $model->tender_token = $tenders[0]['token'];


        if ($type == 'plan') {


            try {
                $response = Yii::$app->opAPI->tendersDocuments(
                    [
                        'name' => Yii::$app->params['upload_dir'] . $model->file,
                        'title' => $model->title,
                        'mime' => $model->mime],
                    null,
                    $model->tender_id,
                    $model->tender_token,
                    $model->document_id,
                    'plans'
                );
                $model->status = 2;

            } catch (apiDataException $e) {
                $model->status = 8;
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                $model->status = 8;
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }

            $model->api_answer = print_r($response, 1);
            $model->save(false);
            $return = false;

            //обновляем json плана  и записываем, что подписал пользователь
            $json = Yii::$app->opAPI->plans(null, $post['tender_id'], null, null);

            // прийдется еще раз брать модель
            $plan = Plans::findOne(['plan_id' => $post['tender_id']]);
            $plan->response = $json['raw'];
            $plan->signed_data = $json['raw'];
            $plan->save();
            Yii::$app->session->setFlash('message', Yii::t('app', 'Пдпис накладено'));
//            exec(".." . DIRECTORY_SEPARATOR . "yii cron-task/force-plan-document " . $model->file, $output, $return);
        } else {

            // если contracting, то меняем точку входа
            if($type=='contracting'){
                $model->type = 'contracting';
            }

            $model->save(false);

            //загрузка файла
            DocumentUploadTask::forceDocument($model->file);
            // обновление тендера / плана / контракта
            if($type=='contracting'){
                return true;
            }else{
                self::update($tenders[0]['id']);
            }

            return true;
        }

        if (!$return) {
            return true;
        } else {
            return false;
        }
    }


}