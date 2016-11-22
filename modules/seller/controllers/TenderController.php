<?php

namespace app\modules\seller\controllers;

use app\components\HTender;
use app\models\AccessRule;
use app\models\Bids;
use app\models\CashFlow;
use app\models\Complaints;
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
use app\modules\seller\models\Tenders;
use app\models\User;
use app\models\Companies;
use yii\base\ErrorException;
use app\components\apiTimeoutException;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use app\components\apiDataException;
use app\components\apiException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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
                    $this->goBack((!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null));
                },

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionView($id)
    {

        $tenders = Tenders::getModelById($id);
//        Yii::$app->VarDumper->dump(Json::decode($tenders->response), 10, true);die;
        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($tenders->id));
        $post = Yii::$app->request->post();

        $bidModel = Bids::getModel($id);
        if (isset($bidModel->answer) && $bidModel->answer) {
            $action = 'update';
            $data['Bid'] = Json::decode($bidModel->answer)['data'];
            $url = $tenders->tender_id . '/bids/' . $bidModel->bid_id;
        } else {
            $action = 'create';
            $data = '';
            $url = $tenders->tender_id . '/bids';
        }

        if ($post) {

//Yii::$app->VarDumper->dump($post, 10, true, true);
            if (isset($post['delete_bids'])) { //удаляем все ставки

                try {
                    $response = Yii::$app->opAPI->bids(
                        null,
                        $tenders->tender_id,
                        $bidModel->token,
                        $bidModel->bid_id,
                        true
                    );

                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные - ' . $e->getMessage() .', '. $e->getErrors()/* .', '. $e->getResponse()*/, $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }

                /** @TODO: Отменить списание денег */
                // Получаем все лоты со ставками и отменяем...
                $lots = null;
                if (isset($data['Bid']['lotValues'])) {
                    foreach($data['Bid']['lotValues'] AS $lot) {
                        $lots[] = $lot['relatedLot'];
                        //Yii::$app->finance->refund( $tender, $lot['relatedLot'] );
                    }
                } /* else if (isset($data['Bid']['value'])) {
                    Yii::$app->finance->refund( $tender, null );
                }*/
                Yii::$app->finance->refundMus( $tender, $lots );



                $bidModel->bid_id = '';
                $bidModel->token = '';
                $bidModel->json = '';
                $bidModel->answer = '';
                $bidModel->save(false);



                return $this->redirect(Url::toRoute('/seller/tender/view/' . $id, true));
            }



            //echo '<pre>'; print_r($post); DIE();

            $res = \app\modules\seller\helpers\BidConvertOut::prepareToAPI($post, $tender, $tenders);



            // насильно переводим в массив неценовые показатели (цбд глючит)
            $res['data']['parameters'] = array_values( (array)$res['data']['parameters'] );


            $lots = [];

            /** Собираем ставки на лоты для проверки */
            if (isset($res['data']['lotValues'])) {
                foreach ($res['data']['lotValues'] AS $new_bid) {
                    if ($new_bid['value']['amount'] > 0) {
                        $lots[$new_bid['relatedLot']] = $new_bid['relatedLot'];
                    }
                }
            }

            /** Если ставка уже опубликованна (редактирование) */
            if ($bidModel->token) {
                if (isset($data['Bid']['lotValues'])) {
                    foreach ($data['Bid']['lotValues'] AS $old_bid) {
                        unset($lots[$old_bid['relatedLot']]);
                    }
                }
            }

            if (count($lots)) {
                $lots = array_values($lots);
            }



            if (
                /** Простая, создание  */
                ($tenders->tender_type == 1 && !isset($data['Bid'])) ||
                /** Мультилот, создание/редактирование, есть новые ставки на лоты */
                ($tenders->tender_type == 2 && count($lots))
            ) {
                /** @var array $result Проверка наличия денег для оплаты ставки */
                $result = Yii::$app->finance->isMembershipAvailable( $tender, $lots );
            }

            if (isset($result['error']) && $result['error']) {
                /** НЕТ ДЕНЕГ --- */

                Yii::$app->session->setFlash('message_bid_error', Yii::t('app','Error! FinancialViability! '. $result['code']));

            } else {
                /** ЕСТЬ ДЕНЬГИ ---- */

                //записываем, что отправил пользователь
//                Yii::$app->VarDumper->dump($res, 10, true, true);
                $bidModel->json = Json::encode($res);
                $bidModel->save(false);
//Yii::$app->VarDumper->dump(json_encode($res), 10, true, true);
//                print_r(json_encode($res));die;
                try {
                    $response = Yii::$app->opAPI->tenders(
                        json_encode($res),
                        $url,
                        $bidModel->token
                    );

                } catch (apiDataException $e) {
                    $e->getErrors();
                    throw new ErrorException('Отправлены не корректные данные - '. /*$e->getMessage() .' '.*/ $e->getErrors()/* .', '. $e->getResponse()*/, $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    //echo '<pre>'; print_r($e); DIE();
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }

                if ($response['body'] != null) {

                    if ($action == 'create') {
                        $bidModel->bid_id = $response['body']['data']['id'];
                        $bidModel->token = $response['body']['access']['token'];
                        // токен миграции
                        if(isset($response['body']['access']['transfer'])){
                            $tenders->transfer_token = $response['body']['access']['transfer'];
                        }
                    }

                    /** Списываем деньги */
                    if ($bidModel->token) {
                        if (
                            /** Простая, создание  */
                            ($tenders->tender_type == 1 && !isset($data['Bid'])) ||
                            /** Мультилот, создание/редактирование, есть новые ставки на лоты */
                            ($tenders->tender_type == 2 && count($lots))
                        ) {
                            Yii::$app->finance->spendSumm($tender, $lots);
                        }
                    }

//                  $bidModel->json = Json::encode($res);
                    $bidModel->answer = $response['raw'];
                    $bidModel->tid = $id;
                    $bidModel->company_id = Yii::$app->user->identity->company_id;
                    $bidModel->user_id = Yii::$app->user->identity->id;
                    $bidModel->save(false);
                }

                //ставим на загрузку документы
                if (isset($post['documents'])) {
                    unset($post['documents']['__EMPTY_DOC__']);
                    if (count($post['documents']) > 0) {


                        //перед постановкой на загрузку, получаем свежие данные по ставке

                        try {
                            $response = Yii::$app->opAPI->getBids(
                                null,
                                $tenders->tender_id,
                                $bidModel->token,
                                $bidModel->bid_id,
                                $bidModel->token

                            );
//                      Yii::$app->VarDumper->dump($response, 10, true);die;
                        } catch (apiDataException $e) {
                            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                        } catch (apiException $e) {
                            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                        }
                        $bidModel->answer = $response['raw'];
//                      Yii::$app->VarDumper->dump($post, 10, true);die;


                        DocumentUploadTask::updateTableAfterSaveBid($id, $tenders->tender_id . '/bids/' . $bidModel->bid_id, $bidModel->token, $post, $response['raw'], $bidModel->bid_id, $tenders, $bidModel);
                    }
                }

                $mailAction = $action;
                //обновляем данные
                $action = 'update';
//              self::update($tenders->id);
                $data['Bid'] = Json::decode($bidModel->answer)['data'];
                $randomMessage = Yii::$app->security->generateRandomString(5);
                Yii::$app->session->setFlash('bid_send' . $randomMessage, Yii::t('app', 'Ставка успiшно вiдправлена. Iнформацiя буде оновлена протягом 5 хвилин.'));

                //отправляем письмо о ставке
                //Notifications::SendBid($tenders, $data['Bid'], $mailAction);

                return $this->redirect(Url::toRoute('/seller/tender/view/' . $id . '?messageid=' . $randomMessage, true));
            }

        }


        $companyComplaintsIds = Complaints::getCompanyQualificationComplains($tenders->id);

        return $this->render('success', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'published' => !empty($tenders->tender_id),
            'bid' => \app\modules\seller\helpers\HBid::$action($data),
            'userBid' => Bids::findOne(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tenders->id]) ? Bids::findOne(['company_id' => Yii::$app->user->identity->company_id, 'tid' => $tenders->id])->json : null,
            'action' => $action,
            'companyComplaintsIds' => $companyComplaintsIds
        ]);

    }


    public function actionQuestions($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);


        if (isset($post['question_submit'])) {
            $this->SendQuestion($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
            $curTender = Tenders::findOne(['id' => $id]);


            $tenderEmail = NULL;
            if($curTender->user_id){
                $tenderEmail = User::findOne(['id' => $curTender->user_id])->username;
            }

            Notifications::SentEmailTenderQuestion($post, $tender, $tenderEmail);

            Yii::$app->session->setFlash('message', Yii::t('app', 'Питання успiшно вiдправлено.'));

        } else {
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        }


        return $this->render('_question', [
            'tender' => $tender,
            'tenders' => $tenders,
            'question' => new Question(),
            'tenderId' => $id,
        ]);
    }

    public function actionPrequalificationComplaints($id, $prequalification)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['prequalification_complaint_cancelled'])) {
            $this->CancelComplaint($post, $tenders);
        } elseif (isset($post['prequalification_satisfied_true']) || isset($post['prequalification_satisfied_false'])) {
            $this->SatisfiedComplaints($post, $tenders, 'prequalification');
        }

            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id), 'eu_prequalification');

        return $this->render('_prequalification_complaints', [
            'complaint' => new Complaint(),
            'tender' => $tender,
            'tenders' => $tenders,
            'qualifications_id' => $prequalification,
            'tenderId' => $id,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id)
        ]);
    }

    public function actionQualificationComplaints($id, $qualification, $type = null)
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


        if (isset($post['documents'])) {


            $complaint['data']['author'] = SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
            $complaint['data']['title'] = $post['Complaint']['title'];
            $complaint['data']['description'] = $post['Complaint']['description'];

            unset($complaint['data']['author']['contactPoint']['availableLanguage']);

            if ($type == 'award') {
//                    Yii::$app->VarDumper->dump($post, 10, true);die;

                $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
                $url = $tenders->tender_id . '/awards/' . $post['qid'] . '/complaints';

                unset($complaint['data']['author']['contactPoint']['availableLanguage']);

                try {
                    $response = Yii::$app->opAPI->tenders(
                        Json::encode($complaint),
                        $url . '?acc_token=' . $bidToken->token
                    );

                    $complaintModel = new Complaints();
                    $complaintModel->token = $response['body']['access']['token'];
                    $complaintModel->complaint_id = $response['body']['data']['id'];
                    $complaintModel->company_id = Yii::$app->user->identity->company_id;
                    $complaintModel->tid = $post['tid'];
                    $complaintModel->type = 'award';
                    $complaintModel->create_at = time();
                    $complaintModel->save();

                    DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url . '/' . $complaintModel->complaint_id, $complaintModel->token, $post, 'q_compl_aw');
                    self::update($tenders->id);
                    Yii::$app->session->setFlash('qualification_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                    return $this->redirect(Url::toRoute('/seller/tender/view/' . $tenders->id, true));

                } catch (apiDataException $e) {
                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiException $e) {
                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                } catch (apiTimeoutException $e) {
                    throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
                }
            }


        } else {

            if (isset($post['complaint_cancelled'])) {
                $this->CancelAwardComplaint($post, $tenders);
                self::update($tenders->id);
                Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга успiшно скасована.'));
                return $this->redirect(Url::current());
            } elseif (isset($post['award_claim_resolved'])
                || isset($post['award_claim_satisfied_true'])
                || isset($post['award_claim_satisfied_false'])
            ) {
                $this->SatisfiedComplaints($post, $tenders, 'award');
            } elseif (isset($post['award_claim_convert_to_pending'])) {
                $this->ComplaintConvertToClaim($post, $tenders, 'award');
            }

            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));

            foreach ($tender->awards as $a => $item) {
                if ($item->id == $qualification) {
                    $awardComplaints = $item->complaints;
                    break;
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

    public function actionCancelPrequalificationComplaints($id, $cid, $prequalification)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);


        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));

        return $this->render('_cancel_prequalification_complaints', [
            'complaint' => new Complaint(),
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id)
        ]);
    }

    public function actionComplaints($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['complaint_type'])) {
            //echo '<pre>'; print_r($post); DIE();
            switch ($post['complaint_type']) {
                case 'tender_complaint_submit':
                    $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
                    $this->SendComplaints($post, $tenders, 'simple');
                    $curTender = Tenders::findOne(['id' => $id]);
                    $tenderEmail = NULL;
                    if($curTender->user_id){
                        $tenderEmail = User::findOne(['id' => $curTender->user_id])->username;
                    }
                    Notifications::SentEmailTenderCompare($post, $tender, $tenderEmail);
                    return $this->redirect(Url::toRoute('/seller/tender/complaints/' . $tenders->id, true));

                case 'award_complaint_submit':
                    return $this->SendComplaints($post, $tenders, 'award');

                case 'prequalification_complaint_submit':
                    return $this->SendComplaints($post, $tenders, 'prequalification');
            }
        }

        if (isset($post['complaint_resolved'])) {
            $this->SatisfiedComplaints($post, $tenders);
        } elseif (isset($post['complaint_convert_to_claim'])) {
            $this->ComplaintConvertToClaim($post, $tenders);
        } elseif (isset($post['complaint_cancelled'])) {
            $this->CancelComplaint($post, $tenders);
        } elseif (isset($post['cancel_prequalification_complaint_submit'])) {
            $this->CancelQualificationComplaint($post, $tenders);
        } elseif(isset($post['add_documents_to_complaints'])) {
            return $this->addDocsToComplaints($post,$tenders);
        }

        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));

        return $this->render('_complaints', [
            //'complaint' => new Complaint(),
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($id)
        ]);
    }

    public function actionComplaintsCreate($tid,$type,$status,$target_id)
    {
        $tenders = Tenders::getModelById($tid);
        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($tid));
        switch($type) {
            case 'tender':
                break;
            case 'award':
                break;
            case 'prequalification':
                break;
        }

        return $this->render('_complaints_form', [
            //'complaint' => new Complaint(),
            'type' => $type,
            'status' => $status,
            'tender' => $tender,
            'tenders' => $tenders,
            'tid' => $tid,
            'target_id' => $target_id,
            'companyComplaints' => \app\models\Companies::getSellerCompanyComplaints($tid)
        ]);
    }

    public function addDocsToComplaints($post,$tenders)
    {
        $complaintModel = Complaints::find()->where(['complaint_id'=>$post['cid'],'tid'=>$post['tid'],'company_id'=>Yii::$app->user->identity->company_id])->one();
        if ($complaintModel) {
            //echo '<pre>'; print_r($post); DIE();
            $url = $tenders->tender_id . '/complaints/'. $complaintModel->complaint_id;
            $docType = 'complaint';
            switch ($post['type']) {
                case 'prequalification':
                    $url = $tenders->tender_id .'/qualifications/'. $post['target_id'] . '/complaints/'. $complaintModel->complaint_id;
                    $docType = 'q_complaint';
                    break;
                case 'award':
                    $url = $tenders->tender_id .'/awards/'. $post['target_id'] . '/complaints/'. $complaintModel->complaint_id;
                    $docType = 'aw_complaint';
                    break;
            }
            DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url, $complaintModel->token, $post, $docType);
            Yii::$app->session->setFlash('message', Yii::t('app', 'Доданi файли будуть завантажуються на протязi 5 хвилин.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Помилка Скарга/Вимога не знайденна.'));
        }
        if ($post['complaint_type'] == 'prequalification_complaint_submit' || $post['type'] == 'prequalification') {
            return $this->redirect(Url::toRoute('/seller/tender/prequalification-complaints?id=' . $tenders->id . '&prequalification=' . $post['target_id'], true));
        } elseif($post['type'] == 'award') {
            return $this->redirect(Url::toRoute('/seller/tender/qualification-complaints?id=' . $tenders->id . '&qualification=' . $post['target_id'], true));
        }
        return $this->redirect(Url::toRoute('/seller/tender/complaints/' . $tenders->id, true));
    }

//    public function SendComplaintsOLD($post, $tenders, $type = 'simple')
//    {
//
//        if (isset($post['documents'])) {
//
//
//            $complaint['data']['author'] = SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
//            $complaint['data']['title'] = $post['Complaint']['title'];
//            $complaint['data']['description'] = $post['Complaint']['description'];
//
//            unset($complaint['data']['author']['contactPoint']['availableLanguage']);
//
//            if ($type == 'prequalification') {
//
//                $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
//                $url = $tenders->tender_id . '/qualifications/' . $post['qid'] . '/complaints';
//
//                unset($complaint['data']['author']['contactPoint']['availableLanguage']);
//
//                try {
//                    $response = Yii::$app->opAPI->tenders(
//                        Json::encode($complaint),
//                        $url . '?acc_token=' . $bidToken->token
//                    );
//
//                    $complaintModel = new Complaints();
//                    $complaintModel->token = $response['body']['access']['token'];
//                    $complaintModel->complaint_id = $response['body']['data']['id'];
//                    $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                    $complaintModel->user_id = Yii::$app->user->identity->id;
//                    $complaintModel->tid = $post['tid'];
//                    $complaintModel->type = 'qualification';
//                    $complaintModel->create_at = time();
//                    $complaintModel->save();
//
//                    DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url . '/' . $complaintModel->complaint_id, $complaintModel->token, $post, 'q_complaint');
//                    self::update($tenders->id);
//                    Yii::$app->session->setFlash('qualification_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
//                    return $this->redirect(Url::toRoute('/seller/tender/view/' . $tenders->id, true));
//
//                } catch (apiDataException $e) {
//                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiException $e) {
//                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiTimeoutException $e) {
//                    throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                }
//            } elseif ($type == 'award') {
//
////                Yii::$app->VarDumper->dump($post, 10, true);
//
//                $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
//                $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints';
////Yii::$app->VarDumper->dump($url, 10, true);die;
//                unset($complaint['data']['author']['contactPoint']['availableLanguage']);
//
//                try {
//                    $response = Yii::$app->opAPI->tenders(
//                        Json::encode($complaint),
//                        $url . '?acc_token=' . $bidToken->token
//                    );
//
//                    $complaintModel = new Complaints();
//                    $complaintModel->token = $response['body']['access']['token'];
//                    $complaintModel->complaint_id = $response['body']['data']['id'];
//                    $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                    $complaintModel->user_id = Yii::$app->user->identity->id;
//                    $complaintModel->tid = $post['tid'];
//                    $complaintModel->type = 'award';
//                    $complaintModel->create_at = time();
//                    $complaintModel->save();
//
//                    DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url . '/' . $complaintModel->complaint_id, $complaintModel->token, $post, 'complaint_aw');
//                    self::update($tenders->id);
//                    Yii::$app->session->setFlash('award_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
//                    return $this->redirect(Url::toRoute('/seller/tender/award/' . $tenders->id, true));
//
//                } catch (apiDataException $e) {
//                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiException $e) {
//                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiTimeoutException $e) {
//                    throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                }
//
//            }
//
//            //если есть привязка к лоту
//            if($tenders->tender_type == 2){
//                if(isset($post['Complaint']['relatedLot']) && $post['Complaint']['relatedLot'] != 'tender'){
//                    $complaint['data']['relatedLot'] = explode('_',$post['Complaint']['relatedLot'])[1];
//                }
//            }
////            Yii::$app->VarDumper->dump($complaint, 10, true);die;
//            try {
//                $response = Yii::$app->opAPI->tenders(
//                    Json::encode($complaint),
//                    $tenders->tender_id . '/complaints'
//                );
//
//                $complaintModel = new Complaints();
//                $complaintModel->token = $response['body']['access']['token'];
//                $complaintModel->complaint_id = $response['body']['data']['id'];
//                $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                $complaintModel->user_id = Yii::$app->user->identity->id;
//                $complaintModel->tid = $post['tid'];
//                $complaintModel->type = 'complaint';
//                $complaintModel->create_at = time();
//                $complaintModel->save();
//                DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $tenders->tender_id . '/complaints/' . $complaintModel->complaint_id, $complaintModel->token, $post, 'complaint');
//                self::update($tenders->id);
//                Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
////                return $this->redirect(Url::toRoute('/seller/tender/view/' . $tenders->id, true));
//
//            } catch (apiDataException $e) {
//                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//            } catch (apiException $e) {
//                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//            }
//
//        } else {
//
//
////Yii::$app->VarDumper->dump($post, 10, true);die;
//            $complaint['data']['author'] = SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
//            $complaint['data']['title'] = $post['Complaint']['title'];
//            $complaint['data']['description'] = $post['Complaint']['description'];
//
////            Yii::$app->VarDumper->dump($post, 10, true);die;
//            //для сверх порогов можно создать сразу жалобу
//            //if($tenders->tender_method == 'open_aboveThresholdUA' || $tenders->tender_method == 'open_aboveThresholdEU' || $tenders->tender_method == 'open_aboveThresholdUA.defense'){
//            if (isset($post['Complaint']['status']) && $post['Complaint']['status']) {
//                $complaint['data']['status'] = $post['Complaint']['status'];
//            }else{
//                $complaint['data']['status'] = 'claim';
//            }
//
//
//            unset($complaint['data']['author']['contactPoint']['availableLanguage']);
////            Yii::$app->VarDumper->dump($post, 10, true);die;
//            if ($type == 'simple') {
//
//                $url = $tenders->tender_id . '/complaints';
//
//                //если есть привязка к лоту
//                if($tenders->tender_type == 2){
//                    if(isset($post['Complaint']['relatedLot']) && $post['Complaint']['relatedLot'] != 'tender'){
//                        $complaint['data']['relatedLot'] = explode('_',$post['Complaint']['relatedLot'])[1];
//                    }
//                }
//
//            } elseif ($type == 'award') {
//
//                $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints';
//                $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
//
//
//                //чистим данные в жалобе
////                unset($complaint['data']['author']['contactPoint']['availableLanguage']);
//                //$complaint['data']['status'] = $post['Complaint']['status'];
////Yii::$app->VarDumper->dump($complaint, 10, true);die;
//                try {
//                    $response = Yii::$app->opAPI->tenders(
//                        Json::encode($complaint),
//                        $url . '?acc_token=' . $bidToken->token
//                    );
//
//                    $complaintModel = new Complaints();
//                    $complaintModel->token = $response['body']['access']['token'];
//                    $complaintModel->complaint_id = $response['body']['data']['id'];
//                    $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                    $complaintModel->user_id = Yii::$app->user->identity->id;
//                    $complaintModel->tid = $post['tid'];
//                    $complaintModel->type = 'award';
//                    $complaintModel->create_at = time();
//                    $complaintModel->save();
//
//                    self::update($tenders->id);
//                    Yii::$app->session->setFlash('award_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
//                    return $this->redirect(Url::toRoute('/seller/tender/award/' . $tenders->id, true));
//
//                } catch (apiDataException $e) {
//                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiException $e) {
//                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiTimeoutException $e) {
//                    throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                }
//
//
//            } elseif ($type == 'prequalification') {
//
//                $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
//                $url = $tenders->tender_id . '/qualifications/' . $post['qid'] . '/complaints';
//
//                //чистим данные в жалобе
//                unset($complaint['data']['author']['contactPoint']['availableLanguage']);
//                //$complaint['data']['status'] = 'pending';
//
//                try {
//                    $response = Yii::$app->opAPI->tenders(
//                        Json::encode($complaint),
//                        $url . '?acc_token=' . $bidToken->token
//                    );
//
//                    $complaintModel = new Complaints();
//                    $complaintModel->token = $response['body']['access']['token'];
//                    $complaintModel->complaint_id = $response['body']['data']['id'];
//                    $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                    $complaintModel->user_id = Yii::$app->user->identity->id;
//                    $complaintModel->tid = $post['tid'];
//                    $complaintModel->type = 'qualification';
//                    $complaintModel->create_at = time();
//                    $complaintModel->save();
//
//                    self::update($tenders->id);
//                    Yii::$app->session->setFlash('qualification_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
//                    return $this->redirect(Url::toRoute('/seller/tender/euprequalification/' . $tenders->id, true));
//
//                } catch (apiDataException $e) {
//                    throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiException $e) {
//                    throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                } catch (apiTimeoutException $e) {
//                    throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                }
//            }
////Yii::$app->VarDumper->dump($complaint, 10, true);die;
//
//            try {
//                $response = Yii::$app->opAPI->tenders(
//                    Json::encode($complaint),
//                    $url
//                );
//
//                $complaintModel = new Complaints();
//                $complaintModel->token = $response['body']['access']['token'];
//                $complaintModel->complaint_id = $response['body']['data']['id'];
//                $complaintModel->company_id = Yii::$app->user->identity->company_id;
//                $complaintModel->user_id = Yii::$app->user->identity->id;
//                $complaintModel->tid = $post['tid'];
//                $complaintModel->type = 'complaint';
//                $complaintModel->create_at = time();
//                $complaintModel->save();
////                Yii::$app->VarDumper->dump($post, 10, true);die;
//                Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
//                self::update($tenders->id);
//                return true;
//
//            } catch (apiDataException $e) {
//                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//            } catch (apiException $e) {
//                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//            } catch (apiTimeoutException $e) {
//                throw new ErrorException('Timeout' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//            }
//        }
//    }

    public function SendComplaints($post, $tenders, $type = 'simple')
    {
        $complaint['data']['author'] = SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
        $complaint['data']['title'] = $post['Complaint']['title'];
        $complaint['data']['description'] = $post['Complaint']['description'];
        $complaint['data']['status'] = (isset($post['Complaint']['status']) && $post['Complaint']['status']) ? $post['Complaint']['status'] : null;

        if (isset($post['documents'])) {
            unset ($complaint['data']['status']); }

        unset($complaint['data']['author']['contactPoint']['availableLanguage']);

        $url = $tenders->tender_id . '/complaints';
        $token = '';
        $doc_type = 'complaint';

        switch ($type) {
            case 'simple':
                if($tenders->tender_type == 2){
                    if(isset($post['Complaint']['relatedLot']) && $post['Complaint']['relatedLot'] != 'tender'){
                        $complaint['data']['relatedLot'] = explode('_',$post['Complaint']['relatedLot'])[1]; } }
                break;
            case 'award':

                //echo '<pre>'; print_r($bidToken); DIE();
                $url = $tenders->tender_id . '/awards/' . $post['target_id'] . '/complaints';
                if ( ! in_array($tenders->tender_method,['limited_negotiation','limited_negotiation.quick']) ) {
                    $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
                    $token = '?acc_token=' . $bidToken->token;
                }
                $doc_type = 'complaint_aw';
                break;
            case 'prequalification':
                $url = $tenders->tender_id . '/qualifications/' . $post['target_id'] . '/complaints';
                if ( ! in_array($tenders->tender_method,['limited_negotiation','limited_negotiation.quick']) ) {
                    $bidToken = Bids::findOne(['tid' => $tenders->id, 'company_id' => Yii::$app->user->identity->company_id]);
                    $token = '?acc_token=' . $bidToken->token;
                }
                $doc_type = 'q_complaint';
                break;
        }

        //echo '<pre>'; print_r($complaint); DIE();

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($complaint),
                $url . $token );
        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }


        $complaintModel = new Complaints();
        $complaintModel->token = $response['body']['access']['token'];
        $complaintModel->complaint_id = $response['body']['data']['id'];
        $complaintModel->company_id = Yii::$app->user->identity->company_id;
        $complaintModel->user_id = Yii::$app->user->identity->id;
        $complaintModel->tid = $post['tid'];
        $complaintModel->type = 'complaint';
        $complaintModel->create_at = time();
        $complaintModel->save();


        if (isset($post['documents'])) {
            DocumentUploadTask::updateTableAfterSaveComplaint($tenders->id, $url . '/' . $complaintModel->complaint_id, $complaintModel->token, $post, $doc_type); }

        //---
        self::update($tenders->id);

        switch ($type) {
            case 'simple':
                Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                break;
            case 'award':
                Yii::$app->session->setFlash('award_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                return $this->redirect(Url::toRoute('/seller/tender/award/' . $tenders->id, true));
                break;
            case 'prequalification':
                Yii::$app->session->setFlash('qualification_complaint_send', Yii::t('app', 'Скарга успiшно вiдправлена. Доданi файли будуть завантажуються на протязi 5 хвилин.'));
                return $this->redirect(Url::toRoute('/seller/tender/view/' . $tenders->id, true));
                break;
        }
    }

    public function CancelComplaint($post, $tenders)
    {
        $url = $tenders->tender_id . '/complaints/' . $post['Complaint']['id'];

        if (explode('_', $tenders->tender_method)[1] == 'aboveThresholdUA' ||
            explode('_', $tenders->tender_method)[1] == 'aboveThresholdEU' ||
            explode('_', $tenders->tender_method)[1] == 'negotiation' ||
            explode('_', $tenders->tender_method)[1] == 'negotiation.quick'
        ) {

            if (isset($post['Complaint']['status']) &&
                ($post['Complaint']['status']) == 'draft' ||
                ($post['Complaint']['status']) == 'claim' ||
                ($post['Complaint']['status']) == 'answered'
            ) {
                $status = 'cancelled';
            } else {
                $status = 'stopping';
            }
        } else {
            $status = 'cancelled';
        }

//        Yii::$app->VarDumper->dump($status, 10, true);die;

        $cancel['data'] = [
            'status' => $status,
            'cancellationReason' => $post['Complaint']['cancellationReason'],
        ];
        if (isset($post['prequalification_complaint_cancelled'])) {
            $url = $tenders->tender_id . '/qualifications/' . $post['qualification_id'] . '/complaints/' . $post['Complaint']['id'];
        }
        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($cancel),
                $url,
                $post['token']
            );

            self::update($tenders->id);
            Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга успiшно скасована.'));

            // отсылаем письмо о отмене скарги
            Notifications::SendComplaintCancel(Json::decode($tenders->response), $post['Complaint']['id'], 'complaints', $post['Complaint']['cancellationReason']);

            return true;

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function CancelAwardComplaint($post, $tenders)
    {

        $url = $tenders->tender_id . '/awards/' . $post['AwardId'] . '/complaints/' . $post['Complaint']['id'];

        if (isset($post['Complaint']['status']) &&
            ($post['Complaint']['status']) == 'draft' ||
            ($post['Complaint']['status']) == 'claim' ||
            ($post['Complaint']['status']) == 'answered'
        ) {
            $status = 'cancelled';
        } else {
            $status = 'stopping';
        }
        $cancel['data'] = [
            'status' => $status,
            'cancellationReason' => $post['Complaint']['cancellationReason'],
        ];

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($cancel),
                $url,
                $post['token']
            );

            self::update($tenders->id);
            Yii::$app->session->setFlash('message', 'Скарга успiшно скасована.');

            // отсылаем письмо о отмене скарги
            Notifications::SendComplaintCancel(Json::decode($tenders->response), $post['Complaint']['id'], 'qualification', $post['Complaint']['cancellationReason']);

            return true;

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function CancelQualificationComplaint($post, $tenders)
    {

        if (explode('_', $tenders->tender_method)[1] == 'aboveThresholdUA' ||
            explode('_', $tenders->tender_method)[1] == 'aboveThresholdEU' ||
            explode('_', $tenders->tender_method)[1] == 'negotiation' ||
            explode('_', $tenders->tender_method)[1] == 'negotiation.quick'
        ) {

            if (isset($post['status']) &&
                $post['status'] == 'draft' ||
                $post['status'] == 'claim' ||
                $post['status'] == 'answered'
            ) {
                $status = 'cancelled';
            } else {
                $status = 'stopping';
            }
        } else {
            $status = 'cancelled';
        }

        $cancel['data'] = [
            'status' => $status,
            'cancellationReason' => $post['Complaint']['cancellationReason'],
        ];

        $token = Complaints::findOne(['complaint_id' => $post['cid']])->token;

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($cancel),
                $tenders->tender_id . '/qualifications/' . $post['qid'] . '/complaints/' . $post['cid'],
                $token
            );

            self::update($tenders->id);
            Yii::$app->session->setFlash('cancel_qualification_complaint_send', Yii::t('app', 'Скарга успiшно вiмiнена.'));

            // отсылаем письмо о отмене скарги
            Notifications::SendComplaintCancel(Json::decode($tenders->response), $post['Complaint']['id'], 'prequalification', $post['cid']);

            return $this->redirect(Url::toRoute('/seller/tender/view/' . $tenders->id, true));

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function ComplaintConvertToClaim($post, $tenders, $type = null)
    {

//        Yii::$app->VarDumper->dump($tenders, 10, true);die;

        $satisfied['data'] = [
            'status' => 'pending',
            'satisfied' => false
        ];

        if ($type == 'award') {
            $url = $tenders->tender_id . '/awards/' . $post['AwardId'] . '/complaints/' . $post['Complaint']['id'];
        } else {
            $url = $tenders->tender_id . '/complaints/' . $post['Complaint']['id'];
        }

        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($satisfied),
                $url,
                $post['token']
            );
            self::update($tenders->id);
            Yii::$app->session->setFlash('message', Yii::t('app', 'Вимога перетворена у скаргу.'));
            return true;

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function SatisfiedComplaints($post, $tenders, $type = null)
    {
        if ($type == 'award') {
            $url = $tenders->tender_id . '/awards/' . $post['AwardId'] . '/complaints/' . $post['Complaint']['id'];
            //это убрать если есть другая возможность кроме закрыть в европейской, украинской, оборонной процедурах
            if (isset($post['award_claim_satisfied_true'])) {
                $satisfied['data']['satisfied'] = true;
            } elseif (isset($post['award_claim_satisfied_false'])) {
                $satisfied['data']['satisfied'] = false;
            }
        } elseif ($type == 'prequalification') {
            $url = $tenders->tender_id . '/qualifications/' . $post['qualification_id'] . '/complaints/' . $post['Complaint']['id'];
            if (isset($post['prequalification_satisfied_true'])) {
                $satisfied['data']['satisfied'] = true;
            } elseif (isset($post['prequalification_satisfied_false'])) {
                $satisfied['data']['satisfied'] = false;
            }
        } else {
            $url = $tenders->tender_id . '/complaints/' . $post['Complaint']['id'];
        }
        if (!isset($satisfied)) {
            $satisfied['data'] = [
                'status' => 'resolved',
                'satisfied' => true
            ];
        }


        try {
            $response = Yii::$app->opAPI->tenders(
                Json::encode($satisfied),
                $url,
                $post['token']
            );
            self::update($tenders->id);
            Yii::$app->session->setFlash('message', Yii::t('app', 'Скарга задоволена.'));
            return true;

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
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

//        SimpleTenderConvertOut::getSellerProcuringEntity(explode('_', $tenders['tender_method'])[1]);
        unset($question['data']['author']['contactPoint']['availableLanguage']);
//Yii::$app->VarDumper->dump($question, 10, true);die;
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

    public static function update($id)
    {
        $model = new \app\models\TenderUpdateTask();
        $model->tid = $id;
        $model->updateTenderApi(null, false);
    }

    public function actionAward($id)
    {
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders));
        $company = Companies::findOne(['id' => $tenders->company_id]);

        if ($post = Yii::$app->request->post()) {

            $bidModel = Bids::find()->where(['bid_id'=>$post['bidId']])->asArray()->all();
//            Yii::$app->VarDumper->dump($bidModel, 10, true, true);
            if (!in_array($type = $post['type'], ApiHelper::$_award_types)) {
                die(HtmlHelper::printErr(Yii::t('app', 'Error! Unknown type')));
            }
            $url = $tenders->tender_id . '/bids/' . $post['bidId'];

            if (in_array($type, ['winner_files'])) {
                DocumentUploadTask::updateTableAfterSaveAward($tenders->id, $url, $bidModel[0]['token'], $post, $tenders->response, $post['awardId']);
                Yii::$app->session->setFlash('message', Yii::t('app', Yii::t('app','Файли завантажуються. Iнформацiя буде оновлена протягом 5 хвилин.')));
                return $this->redirect(Url::toRoute('/seller/tender/award/' . $id, true));
            }
        }
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
        $company = new Companies();
        $company->scenario = 'test';
        return $this->render('test', [
            'company' => $company
        ]);

//        $tenderId = Yii::$app->request->post();
//        return VarDumper::dump($tenderId, 10, true);
    }

    public function beforeAction($action)
    {
        if ($action->id == 'fileupload' || $action->id == 'filedelete') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    public function actionAddmoney($amount)
    {

        $model = new CashFlow();
        $model->way = 'in';
        $model->amount = $amount;
        $model->balance_id = Yii::$app->user->identity->company_id;
        if($model->save(false)){
            echo 'Added to balanse '.$model->amount. '<br/>';
        }else{
            echo 'hren tebe';
        }

        $company = Companies::findOne(Yii::$app->user->identity->company_id);
        if($company){
            $company->balance = $company->balance + $model->amount;
            if($company->save(false)){
                echo $company->balance. '   Balanse OK';
            }else{
                echo 'Balanse Fail';
            }
        }

    }


    public function actionEuprequalification($id)
    {
        $tenders = Tenders::getModelById($id);
//        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders), 'eu_prequalification');
        $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id),'eu_prequalification');  //если не передать сценарий, то отпадают валидаторы

        return $this->render('euprocedure/_eu_prequalification', [
            'tenders' => $tenders,
            'tender' => $tender
        ]);
    }



    public function actionTenderecp()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ErrorException('Что то не так.');
        }

        $post = Yii::$app->request->post();
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
            case ('contracts'):
                $tenders = Tenders::find()->where(['tender_id' => $post['tender_id']])->limit('1')->asArray()->all();
                $uploadUrl = $tenders[0]['tender_id'] . '/contracts/' . $post['contractId'];
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
        $model->title = 'sign.p7s';

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

            //обновляем json плана
            $json = Yii::$app->opAPI->plans(null, $post['tender_id'], null, null);

            // прийдется еще раз брать модель
            $plan = Plans::findOne(['plan_id' => $post['tender_id']]);
            $plan->response = $json['raw'];
            $plan->save();
//            Yii::$app->VarDumper->dump($json['raw'], 10, true);die;
//            exec(".." . DIRECTORY_SEPARATOR . "yii cron-task/force-plan-document " . $model->file, $output, $return);
        } else {
            $model->save(false);
            exec(".." . DIRECTORY_SEPARATOR . "yii cron-task/force-document " . $model->file, $output, $return);
        }


        if (!$return) {
            return true;
        } else {
            return false;
        }
    }

    public function actionUpdatebid($id)
    {

        $bidModel = Bids::findOne(['tid' => $id, 'company_id'=>Yii::$app->user->identity->company_id]);
        $tenders = Tenders::getModelById($id);
//        Yii::$app->VarDumper->dump($bidModel, 10, true);die;

        try {
            $response = Yii::$app->opAPI->getBids(
                null,
                $tenders->tender_id,
                $bidModel->token,
                $bidModel->bid_id,
                $bidModel->token

            );
//                    Yii::$app->VarDumper->dump($response, 10, true);die;
        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }

        $bidModel->answer = $response['raw'];
        if ($bidModel->save(false)) {
            return $bidModel->answer;
        } else {
            echo 'no';
        }


    }

    public function actionBidFinancialViability() {
    //public function actionBid_financial_viability($id) {
        //
        if(!Yii::$app->request->isAjax) {
            return json_encode(['error' => true, 'message' => Yii::t('app', 'Error! Ajax Only!'), 'data'=> []]); }

        if (!$post = Yii::$app->request->post()) {
            return json_encode(['error' => true, 'message' => Yii::t('app', 'Error! Post Only!'), 'data'=> []]); }

        //return json_encode($post);
        //return json_encode(['error' => true, 'message' => Yii::t('app', 'To Many....'), 'data'=> []]);

        {
            /** ID тендера, на которой производилась ставка */
            $tmp_ref = explode('?', $_SERVER['HTTP_REFERER']);
            $tmp_ref = explode('/', $tmp_ref[0]);
            $tmp_ref = $tmp_ref[count($tmp_ref)-1];
        }

        $id = $tmp_ref;

        $data = json_decode($post['json']);

        $lots = null;
        foreach($data AS $lot) {
            if ($lot->id) {
                $lots[] = $lot->id; }
            if ($lot->tid != $id) {
                return json_encode(['error' => true, 'message' => Yii::t('app', 'Error! Bad request! Tender ID not valid '. $lot->tid .' '. $id), 'data'=> []]); } }

        //$tenders = Tenders::getModelById($id);

        //print_r($lots); die();

        $result = Yii::$app->finance->isMembershipAvailable( $id, $lots );

        if (isset($result['error']) && $result['error']) {
            return json_encode(['error' => true, 'message' => Yii::t('app', 'Error! FinancialViability! '. $result['code']) . ', '. Yii::t('app', 'expected').' '. $result['error'].' '.Yii::t('app','гривень'), 'data'=> []]);
        } else if (isset($result['available']) && $result['available']) {
            return json_encode(['error' => false, 'message' => '', 'data'=> []]);
        } else {
            return json_encode(['error' => true, 'message' => Yii::t('app', 'Error! Check Failed! Contact the operator!'), 'data'=> []]); }

        //echo '<pre>'; print_r($result); die();
        //return json_encode($data);
    }

}