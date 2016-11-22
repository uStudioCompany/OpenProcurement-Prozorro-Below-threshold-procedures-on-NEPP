<?php

namespace app\controllers;

use app\commands\CronTaskController;
use app\components\HTender;
use app\components\HAward;
use app\models\Plans;
use app\models\tenderModels\Question;
use app\models\TenderUpdateTask;
use Yii;
use app\components\ApiHelper;
use app\components\HtmlHelper;
use app\components\SimpleTenderConvertOut;
use app\components\SimpleTenderConvertIn;
use app\models\DocumentUploadTask;
use app\models\FileUpload;
use app\models\Persons;
use app\models\tenderModels\Award;
use app\models\Tenders;
use app\models\User;
use app\models\Companies;
use yii\base\ErrorException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use app\components\apiDataException;
use app\components\apiException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\tenderModels\Tender;
use app\models\tenderModels\Cancellation;
use yii\web\UploadedFile;
use app\models\tenderModels\Complaint;

/**
 * Class TenderController
 * @package app\controllers
 */
class TenderController extends Controller
{


    public function actionView($id)
    {
        // print_r(Yii::$app->finance->isMembershipAvailable( $id )); exit;
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($tenders->id));

        return $this->render('success', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);

    }



    public function actionQuestions($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['question_submit'])) {

            $this->SendQuestion($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
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

            self::update($tenders->id);

        } catch (apiDataException $e) {
            throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        } catch (apiException $e) {
            throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
        }
    }

    public function actionComplaints($id)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

        if (isset($post['complaint_submit'])) {

            $this->SendComplaintsAnswer($post, $tenders);
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));

        } elseif (isset($post['award_complaint_submit'])) {
            if ($this->SendComplaintsAnswer($post, $tenders, 'award')) {
                return 'ok';
            }

        } else {
            $tender = HTender::update(SimpleTenderConvertIn::getSimpleTender($id));
        }

        return $this->render('_complaints', [
            'tender' => $tender,
            'tenders' => $tenders,
            'tenderId' => $id,
        ]);
    }

    public function actionPrequalificationComplaints($id, $prequalification)
    {
        $post = Yii::$app->request->post();
        $tenders = Tenders::getModelById($id);

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

        $awardComplaints = null;
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
            'currentAwardModel'=>$currentAwardModel,
        ]);
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

            if ($type === 'active' || $type === 'unsuccessful') {

                $data = array_values($post['Award'])[0];

                //если не нажата галочка в допороговом тендере "Я хочу подписать"

                $json = [];
                if (!isset($post['need_award_esign']) && $tenders->tender_method == 'open_belowThreshold') {
                    $json = [
                        'data' => [
                            'status' => $type,
                        ]
                    ];
                }


                if ($type == 'active') {


                } elseif ($type == 'unsuccessful') {

                    $title = implode(', ', $post['Award']['cause']);
                }

                $url = $tenders->tender_id . '/awards/' . $post['awardId'];

                if (isset($post['documents'])) {
                    DocumentUploadTask::updateTableAfterSaveAward($tenders->id, $url, $tenders->token, $post, $tenders->response, $post['awardId']);
                    system(".." . DIRECTORY_SEPARATOR . "yii cron-task/force-document " . $post['documents'][0]['realName']);
                }

                if (count($json)) {
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

                        // -------------------------------------

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
                    Yii::$app->opAPI->awards(
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
                        'status' => 'resolved',
                        'tendererAction' => $post['tendererAction'],
                    ]
                ];

                $url = $tenders->tender_id . '/awards/' . $post['awardId'] . '/complaints/' . $post['complaintId'];

                try {
                    $response = Yii::$app->opAPI->tenders(
                        Json::encode($json),
                        $url,
                        $tenders->token
                    );

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

        $company = Companies::findOne(['id' => $tenders->company_id]);

        return $this->render('award', [
            'tender' => $tender,
            'tenders' => $tenders,
            'company' => $company,
            'tendersId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);
    }


    public function actionFileupload()
    {
        $model = new FileUpload();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if (in_array($model->file->extension, Yii::$app->params['uploadFiles']['disabledExt'])) {
                $fileArr = [
                    'error' => Yii::t('app', 'Do not upload files with the extension "'). implode(',',Yii::$app->params['uploadFiles']['disabledExt']).'"!'
                ];
            } else if ($model->file && $model->validate()) {

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
                    'model' => $model->file,
                    'fileName' => $model->file->baseName,

                ];
            }
        }
        return Json::encode($fileArr);
    }

    public function actionProtokol($id){
        $tenders = Tenders::getModelById($id);
        $tender = HTender::load(SimpleTenderConvertIn::getTenderInfo($tenders));

        $company = Companies::findOne(['id' => $tenders->company_id]);

        return $this->render('award', [
            'tender' => $tender,
            'tenders' => $tenders,
            'company' => $company,
            'tendersId' => $id,
            'published' => !empty($tenders->tender_id),
        ]);
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

    public function actionTest($id)
    {
        $tenders = Tenders::findOne(['id' => $id]);
        $oldTenderData = '';

        $response['raw'] = '';

        TenderUpdateTask::SendNotifications(Json::decode($oldTenderData),  Json::decode($response['raw']), $tenders);


    }

    public function beforeAction($action)
    {
        if ($action->id == 'fileupload' || $action->id == 'filedelete') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
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


}