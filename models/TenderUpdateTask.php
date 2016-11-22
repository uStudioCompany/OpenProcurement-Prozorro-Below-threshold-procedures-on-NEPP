<?php

namespace app\models;

use app\models\tenderModels\Tender;
use Yii;
use app\components\apiDataException;
use yii\helpers\Json;

/**
 * This is the model class for table "tender_update_task".
 *
 * @property integer $id
 * @property integer $tid
 * @property string $tender_id
 * @property string $tender_token
 * @property string $created_at
 * @property string $transaction_id
 * @property string $api_answer
 * @property integer $status
 */
class TenderUpdateTask extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    public $_transaction_id = '';

    /**
     * @var string
     */
    public $_error_code = 8;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tender_update_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid'], 'required'],
            [['created_at'], 'safe'],
            [['api_answer'], 'string'],
            [['tid', 'status'], 'integer'],
            [['tender_id', 'tender_token', 'transaction_id'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tender_id' => Yii::t('app', 'Tender ID'),
            'tender_token' => Yii::t('app', 'Tender Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'api_answer' => Yii::t('app', 'Api Answer'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getErrorTask()
    {
        $task = DocumentUploadTask::find()
            ->where(['status' => $this->_error_code])
            ->orderBy('created_at')
            ->all();
        return $task;
    }

    public function getFindNew()
    {
        $task = self::find()
            ->where(['status' => 0, 'transaction_id' => ''])
            ->orderBy('created_at')
            ->limit(1)
            ->one();
        return $task;
    }

    public function lockRecord($id)
    {
        if (!$this->_transaction_id) {
            $this->_transaction_id = time() . ' ' . Yii::$app->security->generateRandomString(5);
        }
        $sql = 'UPDATE  `' . self::tableName() . "`  SET  `transaction_id` = '{$this->_transaction_id}'  WHERE  `id` = '{$id}' AND `transaction_id` = '';";
        $query = $this->getDb()->createCommand($sql);

        return $query->bindValues([':id' => $id, ':transaction_id' => $this->_transaction_id])->execute();
    }

    /**
     * @return false|DocumentUploadTask
     */
    public function getNextTask()
    {
        if ($task = $this->getFindNew()) {
            if ($lock = $this->lockRecord($task->id)) {
                $task->transaction_id = $this->_transaction_id;
                return $task;
            }else{
                return $this->getNextTask();
            }
        }
        return false;
    }

    /**
     * @param $task TenderUpdateTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function updateTenderApi($task = null)
    {
        if (!$task)
            $task = $this;

        //print_r($task); die();

        if (($tenders = Tenders::findOne(['id' => $task->tid])) === null) {
            return false;
        }

        // присваиваем переменной старые данные тендера(до обновления) для сравнения статусов для писем.
        $oldTenderData = $tenders->response;

        $response = Yii::$app->opAPI->tenders(
            null,
            $tenders->tender_id);

        $tenders->status = $response['body']['data']['status'];
        $tenders->response = $response['raw'];
        $tenders->date_modified = $response['body']['data']['dateModified'];
        $tenders->title = Json::decode($response['raw'])['data']['title'];
        $tenders->tender_cbd_id = Json::decode($response['raw'])['data']['tenderID'];
        $tenders->tender_method = Json::decode($response['raw'])['data']['procurementMethod'] . '_' . Json::decode($response['raw'])['data']['procurementMethodType'];

        if (isset(Json::decode($response['raw'])['data']['lots'])) {
            $tenders->tender_type = 2;

            if ($response['body']['data']['status'] == 'active.auction'){
                foreach ($response['body']['data']['lots'] as $k=> $lot) {
                    if (isset($lot['auctionPeriod']['startDate'])) {
                        $tenders->auction_date = $lot['auctionPeriod']['startDate'];
                    }
                }
            }

        } else {
            $tenders->tender_type = 1;

            if ($response['body']['data']['status'] == 'active.auction' && isset($response['body']['data']['auctionPeriod']['startDate'])) {
                    $tenders->auction_date = $response['body']['data']['auctionPeriod']['startDate'];
            }
        }

        if (isset($response['body']['data']['mode']) && $response['body']['data']['mode'] == 'test') {
            $tenders->test_mode = 1;
        } else {
            $tenders->test_mode = 0;
        }

        if (isset(Json::decode($response['raw'])['data']['description'])) {
            $tenders->description = Json::decode($response['raw'])['data']['description'];
        }

        $tenders->save(false);

        //цепляем компетентный диалог 2 часть
        if ($response['body']['data']['status'] == 'complete' &&
            in_array($response['body']['data']['procurementMethodType'], ['competitiveDialogueUA', 'competitiveDialogueEU']) && $tenders->token
        ) {

            if (isset($response['body']['data']['stage2TenderID']) && $response['body']['data']['stage2TenderID']) {
                $tendersStep2 = Tenders::find()->where(['tender_id' => $response['body']['data']['stage2TenderID']])->one();
                if (!(isset($tendersStep2->token) && $tendersStep2->token)) {

                    if (!$tendersStep2) {
                        $tendersStep2 = new Tenders();
                    }
                    $tendersStep2->tender_id = $response['body']['data']['stage2TenderID'];
                    $tendersStep2->user_id = $tenders->user_id;
                    $tendersStep2->company_id = $tenders->company_id;
                    $tendersStep2->ecp = $tenders->ecp;


                    $tendersStep2Response = Yii::$app->opAPI->tenders(
                        null,
                        $tendersStep2->tender_id . '/credentials',
                        $tenders->token
                    );

                    if ($tendersStep2Response != NULL) { // сохраняем новый тендер
                        $tendersStep2->status = $tendersStep2Response['body']['data']['status'];
                        $tendersStep2->response = $tendersStep2Response['raw'];
                        $tendersStep2->date_modified = $tendersStep2Response['body']['data']['dateModified'];
                        $tendersStep2->title = $tendersStep2Response['body']['data']['title'];
                        $tendersStep2->description = $tendersStep2Response['body']['data']['description'];
                        $tendersStep2->tender_cbd_id = $tendersStep2Response['body']['data']['tenderID'];
                        $tendersStep2->token = $tendersStep2Response['body']['access']['token'];
                        $tendersStep2->tender_method = $tendersStep2Response['body']['data']['procurementMethod'] . '_' . $tendersStep2Response['body']['data']['procurementMethodType'];
                        $tendersStep2->save(false);


//                        if ($tendersStep2->save(false)) {
//                            // Выхватываем ставки для старого тендера, копируем и привязываем к новому.
//                            $bids = Bids::find()->where(['tid' => $tenders->id])->all();
//                            if (isset($bids) && count($bids)) {
//                                foreach ($bids as $k => $bid) {
//                                    $bidStep2 = new \app\models\Bids();
//                                    $bidStep2->setAttributes($bid->attributes);
//                                    $bidStep2->tid = $tendersStep2->id;
//                                    $bidStep2->save(false);
//                                }
//                            }
//                        }

                    }

                }

            }
        }

        //////////////////////////




        /** @var Bids[] $bids */
        $bids = Bids::find()->where(['tid'=>$tenders->id])->all();



        /** Если есть ставки на тендер */
        if ($bids) {

            $need_to_del_bids = false;
            $lots_arr = null;

            /** Если отменен весь Тендер */
            if ($tenders->status === 'cancelled' || $tenders->status === 'unsuccessful') {
                $need_to_del_bids = true;
                if ($tenders->tender_type === 2) {
                    foreach ($response['body']['data']['lots'] AS $k => $lot) {
                        $lots_arr[] = $lot['id'];
                    }
                }
            } else {
                if ($tenders->tender_type === 2) {
                    foreach ($response['body']['data']['lots'] AS $k => $lot) {
                        /** Ищем отмененные лоты */
                        if ($lot['status'] === 'cancelled' || $lot['status'] === 'unsuccessful') {
                            $need_to_del_bids = true;
                            $lots_arr[] = $lot['id'];
                        }
                    }
                }
            }

            if ($need_to_del_bids) {
                foreach($bids AS $bid) {
                    Yii::$app->finance->refundMus($tenders->id, $lots_arr, $bid->company_id);
                }
            }

            if ($tenders->status === 'active.auction') {
                BidUpdateTask::addBidTask($tenders->id);
            }
        }


        $task->api_answer = $response['raw'];
        $task->status = 2;
        $task->delete();

            //смотрим, цепляем контрактинги
//             ContractingUpdateTask::GetContracting(Json::decode($response['raw']), $tenders);

            // отправляем email
            if (Yii::$app->params['sendNotifications']) {
                self::SendNotifications(Json::decode($oldTenderData),  Json::decode($response['raw']), $tenders);
            }
    }

    public static function SendNotifications($oldData, $data, $tenders)
    {


        $sellers = Bids::find()
            ->select('user_id')
            ->with(['user' => function ($query) {
                $query->select('username');
            },])
            ->where(['tid' => $tenders->id])->all();
//        Yii::$app->VarDumper->dump($sellers, 10, true);die;
        $buyer = User::findOne(['id' => $tenders->user_id]);

        // нотификации о неотвеченных вопросах и жалобах для покупателя
        if($buyer){
            Notifications::SendOneDayNotification($buyer, $data, $tenders);
        }

        if($sellers){
            //нотификация о отмене торгов
            Notifications::SendCancelTender($sellers, $oldData, $data);

            //нотификация о результатах преквалификации
            Notifications::SendPrequalificationResult($sellers, $oldData, $data);

            //нотификация о результатах квалификации
            Notifications::SendQualificationResult($sellers, $oldData, $data);
        }

        if($buyer && $sellers){
            //нотификация о результатах действий органа обжалования
            Notifications::SendOrganResult($sellers, $buyer, $oldData, $data, $tenders->id);
        }

    }

    public function getChangesApi()
    {
        $count = 0;

        $response = Yii::$app->opAPI->tenders(
            null,
            null,
            null,
            //date('c',strtotime('- '. Yii::$app->params['tender_update_interval'] .' minute')));
            date('c', strtotime('- 1 day')));

        //return print_r($response,1);

        if (count($response['body']['data'])) {
            foreach ($response['body']['data'] AS $row) {
                $tender = Tenders::find()
                    ->where(['tender_id' => $row['id']])
                    ->andWhere(['<>', 'date_modified', $row['dateModified']])
                    ->one();

                //print_r($tender);

                if ($tender !== null) {
                    $this->addTask($tender->id, $tender->tender_id);
                    $count++;
                }
            }
        }
        return $count;
    }

    public static function addTaskByDocument($document_task)
    {
        return self::addTask($document_task->tid, $document_task->tender_id);
    }

    public static function addTask($tid, $tender_id)
    {
        $tender = self::find()->where(['tender_id' => $tender_id, 'status' => '0'])->all();
        if (!$tender) {
            $tender = new TenderUpdateTask();
            $tender->tid = $tid;
            $tender->tender_id = $tender_id;
            return $tender->save(false);
        }
        return null;
    }

}
