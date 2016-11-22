<?php


namespace app\commands;

use app\models\Bids;
use app\models\BidUpdateTask;
use app\models\Contracting;
use app\models\ContractingUpdateTask;
use app\models\Log;
use app\models\Notifications;
use app\models\Plans;
use app\models\PlanUpdateTask;
use app\models\Tenders;
use app\models\User;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii;
use yii\console\Controller;
use app\components\apiDataException;
use app\components\apiException;
use yii\base\ErrorException;
use app\models\TenderUpdateTask;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronTaskController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        echo "cron-task/documents\n";
    }

    /**
     * Выполняет проверку доступности ЦБД и записывает статус в файл ../cdbAvailable.txt
     */
    public function actionCheckCdbAvailable()
    {
        try {
            $file = dirname(__DIR__).'/cdbAvailable.txt';
            if(Yii::$app->opAPI->ping()){
                $status = 1;
            }
        } catch (\Exception $e) {
            $status = 0;
        } finally {
            file_put_contents($file, $status);
        }
    }

    /**
     * Выполняет загрузку документов из таблицы document_upload_task
     */
    public function actionDocuments()
    {
        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\DocumentUploadTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

        //file_put_contents(__DIR__.'/exeption.txt', date('Y-m-d H:i:s') ." -d-\n", FILE_APPEND);

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        if (Yii::$app->params['DS']) {
                            if ($task->file) {
                                $task->sendFileApiViaDS();
                            }
                            if (!$task->file) {
                                $task->updateFileApiViaDS();
                            }
                        } else {
                            if ($task->file) {
                                $task->sendFileApi();
                            }
                            if ($task->title) {
                                $task->updateFileApi();
                            }
                        }

                        $task->executeFileApi();

                        if (Yii::$app->params['deleteFile']) {
                            $task->deleteUploadedFile();
                            //$task->CleanTable();
                        }


                        \app\models\TenderUpdateTask::addTaskByDocument($task);
                        \app\models\BidUpdateTask::addBidTaskByDocument($task);
                    }else{
                        break;
                    }
                } catch (\app\components\apiDataException $e) {
                    if ($task) {
                        $task->status = $model->_error_code;
                        $task->transaction_id = '';
                        $task->api_answer = $task->api_answer . '####' . "\n" . ' Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                        $task->save(false);
                    }
                    //throw new \Exception('apiDataException ' . $e->getMessage(), $e->getCode(), $e);
                } catch (\app\components\apiException $e) {
                    if ($task) {
                        $task->status = $model->_error_code;
                        $task->transaction_id = '';
                        $task->api_answer = $task->api_answer . '####' . "\n" . ' Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                        $task->save(false);
                    }
                    //throw new \Exception('apiException ' . $e->getMessage(), $e->getCode(), $e);
                }
                usleep(rand(1, 1000));
            }
        } catch (\Exception $e) {
            if ($task) {
                $task->status = $model->_error_code;
                $task->transaction_id = '';
                $task->api_answer = $task->api_answer . '####' . "\n" . ' Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $task->save(false);
            }
            //file_put_contents(__DIR__.'/exeption.txt', '='. print_r($e,1) ."\n\n", FILE_APPEND);
        }
    }

    /**
     * Выполняет загрузку документа для тендера
     *
     * @param $file string Имя файла
     */
    public function actionForceDocument($file)
    {
        $model = \app\models\DocumentUploadTask::findOne(['file' => $file]);

        try {

            if (Yii::$app->params['DS']) {
                $model->sendFileApiViaDS();
            } else {
                $model->sendFileApi();
                $model->updateFileApi();
            }

            $model->executeFileApi();

            if  (Yii::$app->params['deleteFile']) {
                $model->deleteUploadedFile();
                //$model->CleanTable();
            }

        } catch (\Exception $e) {
            if ($model) {
                $model->status = $model->_error_code;
                $model->transaction_id = '';
                $model->api_answer = $model->api_answer . '####' . "\n" . ' Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $model->save(false);
            }
        }
    }

    /**
     * Выполняет загрузку тендеров из таблицы tender_update_task
     */
    public function actionTenders()
    {
//        file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', 'tenders_start'.date('m-d-Y  H:i:s'), FILE_APPEND);
        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\TenderUpdateTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        $model->updateTenderApi($task);
                    }else{
                        break;
                    }
                } catch (\app\components\apiDataException $e) {
                    throw new \Exception('apiDataException ', $e->getCode(), $e);
                } catch (\app\components\apiException $e) {
                    throw new \Exception('apiException ', $e->getCode(), $e);
                }
//                usleep(rand(1, 1000));
            }
        } catch (\Exception $e) {
            if ($task) {
                $task->status = $model->_error_code;
                $task->transaction_id = '';
                $task->api_answer = 'Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $task->save(false);
            }
        }
//        file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', 'tenders_end'.date('m-d-Y  H:i:s'), FILE_APPEND);
    }


    /**
     * Выполняет загрузку тендеров из таблицы tender_update_task
     */
    public function actionContracts()
    {

        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\ContractingUpdateTask();

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        $model->updateApi($task);
                    }else{
                        break;
                    }
                } catch (\app\components\apiDataException $e) {
                    throw new \Exception('apiDataException ', $e->getCode(), $e);
                } catch (\app\components\apiException $e) {
                    throw new \Exception('apiException ', $e->getCode(), $e);
                }
                usleep(rand(1, 1000));
            }
        } catch (\Exception $e) {
            if ($task) {
                $task->status = $model->_error_code;
                $task->transaction_id = '';
                $task->api_answer = 'Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $task->save(false);
            }
        }
    }

    /**
     * Выполняет обновление ставок из таблицы bid_update_task
     */
    public function actionBids()
    {
        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\BidUpdateTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        $model->updateBidApi($task);
                    }else{
                        break;
                    }
                } catch (\app\components\apiDataException $e) {
                    throw $e;
                } catch (\app\components\apiException $e) {
                    if ($e->getCode() == 404) {
                        $task->status = $model->_error_code;
                        $task->api_answer = $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                        $task->save(false);
                    } else {
                        throw $e;
                    }
                }
                usleep(rand(1, 1000));
            }
        } catch (\Exception $e) {
            if ($task) {
                $task->status = $model->_error_code;
                $task->transaction_id = '';
                $task->api_answer = 'Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $task->save(false);
            }
        }
    }

    /**
     * Выполняет загрузку планов из таблицы plan_update_task
     */
    public function actionPlans()
    {

        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\PlanUpdateTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        $model->updatePlanApi($task);
                    }else{
                        break;
                    }
                } catch (\app\components\apiDataException $e) {
                    throw new \Exception('apiDataException ', $e->getCode(), $e);
                } catch (\app\components\apiException $e) {
                    throw new \Exception('apiException ', $e->getCode(), $e);
                }
                usleep(rand(1, 1000));
            }
        } catch (\Exception $e) {
            if ($task) {
                $task->status = $model->_error_code;
                $task->transaction_id = '';
                $task->api_answer = 'Exception [' . $e->getCode() . '] ' . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
                $task->save(false);
            }
        }
    }

    public function actionCheckStatus()
    {
        $doc_model = new \app\models\DocumentUploadTask();
        $doc_err = $doc_model->getErrorTask();
        $tend_model = new \app\models\DocumentUploadTask();
        $tend_err = $tend_model->getErrorTask();
        if (count($doc_err) or count($tend_err)) {
            /**
             * @TODO Отправляем емаил администратору, с колвом ошибок!!! count(err)
             */
        }
    }

    /**
     * Выполняет заполнение таблицы plan_update_task для обновления планов.
     */
    public function actionGetPlanUpdateList()
    {

        function SetPlanQuery($lastModified)
        {

            try {
                $response = Yii::$app->opAPI->plans(
                    null,//data plans
                    null,//id plans
                    null, //token plans
                    $lastModified . '&mode=_all_'//&descending=1
                );

                if ($response['body']['data']) {

                    foreach ($response['body']['data'] as $k => $plan) {
                        if($plans = Plans::findOne(['plan_id'=>$plan['id']])){
//                            if($tenders->update_at < strtotime($tender['dateModified'])){
                            $updateModel = new PlanUpdateTask();
                            $updateModel->plan_id = $plan['id'];
                            $updateModel->pid = $plans->id;
//                                $updateModel->tender_token = $tenders->token;
                            $updateModel->save(false);
//                            }

                        }else {
                            $plans = new Plans();
                            $plans->plan_id = $plan['id'];
                            $plans->save(false);

                            $updateModel = new PlanUpdateTask();
                            $updateModel->plan_id = $plan['id'];
                            $updateModel->pid = $plans->id;
                            $updateModel->save(false);
                        }
                    }

//                    $changeIds = [];
//                    foreach ($response['body']['data'] as $k => $plan) {
//                        $changeIds[] = $plan['id'];
//                    }
//
//                    if (count($changeIds)) {
//
//                        $plans = (new \yii\db\Query())
//                            ->select(['id', 'plan_id', 'token'])
//                            ->from('plans')
//                            ->where(['in', 'plan_id', $changeIds])
//                            ->all();
//
//                        if ($plans) {
//                            foreach ($plans as $plan) {
//                                $updateModel = new PlanUpdateTask();
//                                $updateModel->pid = $plan['id'];
//                                $updateModel->plan_id = $plan['plan_id'];
//                                $updateModel->plan_token = $plan['token'];
//                                $updateModel->save();
//                            }
//                        }
//                    }

                    if (isset($response['body']['next_page']['path']) && $response['body']['next_page']['path']) {
                        $offset = explode('=', explode('&', $response['body']['next_page']['path'])[1])[1];
                        SetPlanQuery($offset);
                    }


                } else {
                    file_put_contents(Yii::getAlias('@root').'/plans_last_modified.txt', strtotime('now -2 minutes'));
                    return;
                }


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }

        $date = file_get_contents(Yii::getAlias('@root').'/plans_last_modified.txt');
        if (preg_match('/^\d{10}$/', $date)) {
            $lastModified = date('c', $date);
//            var_dump($lastModified);
            SetPlanQuery($lastModified);
        } else {
            $lastModified = date('c', strtotime('now -5 minutes'));
            SetPlanQuery($lastModified);
        }

    }

    public function actionGetAllTendersUpdateList()
    {

        function SetAllQuery($lastModified)
        {

            try {
                $response = Yii::$app->opAPI->tenders(
//                $response = Yii::$app->opAPI->publicTendersPoint( // публичная точка доступа
                    null,//data tenders
                    null,//id tenders
                    null, //token tenders
                    $lastModified . '&mode=_all_'//&descending=1
                );

//                file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', print_r($response,1), FILE_APPEND);
                if ($response['body']['data'] && count($response['body']['data'])) {

                    foreach ($response['body']['data'] as $k => $tender) {
                        if($tenders = Tenders::findOne(['tender_id'=>$tender['id']])){
//                            if($tenders->update_at < strtotime($tender['dateModified'])){
                            TenderUpdateTask::addTask($tenders->id,$tender['id']);
//                            $updateModel = new TenderUpdateTask();
//                            $updateModel->tender_id = $tender['id'];
//                            $updateModel->tid = $tenders->id;
////                                $updateModel->tender_token = $tenders->token;
//                            $updateModel->save(false);
//                            }

                        }else {
                            $tenders = new Tenders();
                            $tenders->tender_id = $tender['id'];
                            $tenders->save(false);

                            TenderUpdateTask::addTask($tenders->id,$tender['id']);
//                            $updateModel = new TenderUpdateTask();
//                            $updateModel->tender_id = $tender['id'];
//                            $updateModel->tid = $tenders->id;
//                            $updateModel->save(false);
                        }
                    }

                    if (isset($response['body']['next_page']['path']) && $response['body']['next_page']['path']) {
                        $offset = explode('=', explode('&', $response['body']['next_page']['path'])[1])[1];
//                        $offset = $response['body']['next_page']['offset'];
                        file_put_contents(Yii::getAlias('@root').'/tenders_all_last_modified.txt', $offset);
                        SetAllQuery($offset);
                    }


                } else {
                    file_put_contents(Yii::getAlias('@root').'/tenders_all_last_modified.txt', date('c',strtotime('now -2 minutes')));
//                    file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', 'end'.date('m-d-Y  H:i:s').strtotime('now -2 minutes'), FILE_APPEND);
                    return;
                }


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }
//        file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', date('m-d-Y  H:i:s'), FILE_APPEND);

        if($date = file_get_contents(Yii::getAlias('@root').'/tenders_all_last_modified.txt')){
            if (preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $date)) {
                SetAllQuery($date);
                return;
            } else {
                $date = date('c', strtotime('now -2 minutes'));
                file_put_contents(Yii::getAlias('@root').'/tenders_all_last_modified.txt', $date);
                SetAllQuery($date);
            }
        }else{
            SetAllQuery(date('c',strtotime('now -2 minutes')));
        }

    }


    public function actionGetContractingUpdateList()
    {

        function SetContractingQuery($lastModified)
        {

            try {
                $response = Yii::$app->opAPI->contracts(
                    null,//data tenders
                    null,//id tenders
                    null, //token tenders
                    $lastModified . '&mode=_all_'//&descending=1
                );

                //echo '<pre>'; print_r($response); DIE();

//                file_put_contents(Yii::getAlias('@root').'/test_cookie.txt', print_r($response,1), FILE_APPEND);
                if ($response['body']['data']) {

                    foreach ($response['body']['data'] as $k => $contract) {

                        $updateModel = new ContractingUpdateTask();

                        if($contracts = Contracting::findOne(['contract_id'=>$contract['id']])){

                            $updateModel->cid = $contracts->id;
                            $updateModel->tid = $contracts->tid;

                        } else {

                            $contracts = new Contracting();
                            $contracts->contract_id = $contract['id'];
                            $contracts->save(false);

                            $updateModel->cid = $contracts->id;
                        }

                        $updateModel->contract_id = $contract['id'];
                        $updateModel->created_at  = date('Y-m-d H:i:s',strtotime(Yii::$app->params['getContractingToken']));
                        $updateModel->modified    = $contract['dateModified'];
                        $updateModel->save(false);

                    }

                    if (isset($response['body']['next_page']['path']) && $response['body']['next_page']['path']) {
                        $offset = explode('=', explode('&', $response['body']['next_page']['path'])[1])[1];
                        SetContractingQuery($offset);
                    }


                } else {
                    file_put_contents(Yii::getAlias('@root').'/contracting_last_modified.txt', strtotime('now -2 minutes'));
                    return;
                }


            } catch (apiDataException $e) {
                throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            } catch (apiException $e) {
                throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
            }
        }

        if($date = file_get_contents(Yii::getAlias('@root').'/contracting_last_modified.txt')){
            if (preg_match('/^\d{10}$/', $date)) {
                $lastModified = date('c', $date);
                SetContractingQuery($lastModified);
            } else {
                $lastModified = date('c', strtotime('now -2 minutes'));
                file_put_contents(Yii::getAlias('@root').'/contracting_last_modified.txt', $lastModified);
                SetContractingQuery($lastModified);
            }
        }else{
            file_put_contents(Yii::getAlias('@root').'/contracting_last_modified.txt', strtotime('now -2 minutes'));
        }

    }


    /**
     *  Запускается 1 раз в день
     *
     *  Письмо про сегодняшний аукцион
     */
    public function actionAuctionNotice()
    {
        Notifications::SendAuctionNotice();
        Notifications::AddEventToCabinet();
        $this->actionClean();
    }


//    public function actionGetAllTendersUpdateTest(){
//
//        for ($i = 0; $i <= 1000; $i++) {
//            $model = new Log();
//            $model->message = 1;
//            $model->save(false);
//        }
//    }
//
//    public function actionGetAllTendersUpdateTest2(){
//
//        for ($i = 0; $i <= 1000; $i++) {
//            $model = new Log();
//            $model->message = 1;
//            $model->save(false);
//        }
//    }

//    public function actionSyncTest()
//    {
//        $this->actionGetAllTendersUpdateTest();
//        $this->actionGetAllTendersUpdateTest2();
//    }

    public function actionSync()
    {
        $this->actionCheckCdbAvailable();
        $this->actionDocuments();
        $this->actionGetAllTendersUpdateList();
        $this->actionTenders();
        $this->actionBids();

        $this->actionGetContractingUpdateList();
        $this->actionContracts();

        $this->actionGetPlanUpdateList();
        $this->actionPlans();

//        $this->actionClean();
    }
    public function actionClean()
    {
        // таблица обновлений тендеров, очищаем таски, которым больше 3 дней
//        TenderUpdateTask::deleteAll(['<','created_at', date('Y.m.d 00:00:00',strtotime('now -3days'))]);
        Log::deleteAll(['<','log_time', strtotime('now -3days')]);
    }

//    public function SendAuctionNotification() {
//        Bids::find()->with('tenders t')->where(['t.status'=>'active.auction']);
//    }


    public function actionMailQueue()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('MailQueue', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            $new = yii\helpers\Json::decode($msg->body, true);
            echo $new['to'];
            echo Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['mail_sender_full'])
                ->setTo($new['to'])
                ->setSubject($new['subject'])
                ->setHtmlBody($new['body'])
                ->send();
        };

        $channel->basic_consume('MailQueue', '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }

}