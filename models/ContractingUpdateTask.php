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
 * @property integer $cid
 * @property string $contract_id
 * @property string $contract_token
 * @property string $tender_token
 * @property string $created_at
 * @property string $transaction_id
 * @property string $api_answer
 * @property integer $status
 * @property integer $modified
 */
class ContractingUpdateTask extends \yii\db\ActiveRecord
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
        return 'contracting_update_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid'], 'required'],
            [['created_at', 'modified'], 'safe'],
            [['api_answer'], 'string'],
            [['tid', 'status'], 'integer'],
            [['contract_id', 'contract_token', 'transaction_id'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'contract_id' => Yii::t('app', 'Contract ID'),
            'contract_token' => Yii::t('app', 'Contract Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'api_answer' => Yii::t('app', 'Api Answer'),
            'status' => Yii::t('app', 'Status'),
            'modified' => Yii::t('app', 'modified'),
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
            ->andWhere(['<', 'created_at', date('Y-m-d H:i:s', strtotime(Yii::$app->params['getContractingToken']))])
            ->orderBy('created_at')
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
            }
        }
        return false;
    }

    /**
     * @param $task ContractingUpdateTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function updateApi($task = null)
    {
        if (!$task)
            $task = $this;

        $contracts = Contracting::findOne(['id' => $task->cid]);
        if (!$contracts) {
            $contracts = Contracting::findOne(['contract_id' => $task->contract_id]);
        }

        if ($contracts) {
            // update
            $id = $contracts->contract_id;
            $token = null;
        } else {
            // create
            $contracts = new Contracting();
            $id = $task->contract_id;
            $token = null;
        }

        $response = Yii::$app->opAPI->contracts(
            null,
            $id,
            null
        );

        $tenders = Tenders::findOne(['id' => $task->tid]);

        if (!$tenders && isset($response['body']['data']['tender_id'])) {
            $tenders = Tenders::findOne(['tender_id' => $response['body']['data']['tender_id']]);
        }

        if ($tenders) {
            $contracts->user_id = $tenders->user_id;
            $contracts->company_id = $tenders->company_id;
            $contracts->tid = $tenders->id;
            //echo '--'. $tenders->id .']_['. $response['body']['data']['tender_id'] ."--\n";
            if ($tenders->token && !$contracts->token) {
                $id = $task->contract_id . '/credentials';
                $token = $tenders->token;
                $response = Yii::$app->opAPI->contracts(
                    null,
                    $id,
                    $token
                );
            }
        }

        if (isset($response['body']['access']['token'])) {
            $contracts->token = $response['body']['access']['token'];
            $contracts->contract_id = $response['body']['data']['id'];
        }

        $contracts->status = $response['body']['data']['status'];
        $contracts->response = $response['raw'];
        $contracts->date_modified = $response['body']['data']['dateModified'];
        $contracts->title = Json::decode($response['raw'])['data']['title'];
        if (is_null($contracts->contract_cbd_id)) {
            $contracts->contract_cbd_id = Json::decode($response['raw'])['data']['contractID'];
        }
        $contracts->save(false);

        $task->api_answer = $response['raw'];
        $task->status = 2;
        $task->delete();


    }

//    public static function GetContracting($data, $tenders)
//    {
//        if (isset($data['data']['awards']) && count($data['data']['awards'])) {
//            foreach ($data['data']['awards'] as $a => $award) {
//                if (in_array($award['status'], ['active'])) {
//                    if($contract = self::getAwardContract($award['id'], $data)){
//
//                         try {
//                             $response = Yii::$app->opAPI->contracts(
//                                 null,
//                                 $tenders->tender_id . '/credentials',
//                                 $tenders->token
//                             );
////                Yii::$app->VarDumper->dump($response, 10, true);die;
//                             return true;
//
//                         } catch (apiDataException $e) {
//                             throw new ErrorException('Отправлены не корректные данные -' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                         } catch (apiException $e) {
//                             throw new ErrorException('Сетевые проблемы.' . $e->getMessage(), $e->getCode(), 1, __FILE__, __LINE__, $e);
//                         }
//                    }
//                }
//            }
//        }
//    }

//    public static function getAwardContract($awardId, $data){
//        if (isset($data['data']['contracts']) && count($data['data']['contracts'])) {
//            foreach ($data['data']['contracts'] as $c=>$contract) {
//                if($data['data']['contracts']['awardID'] == $awardId){
//                    return $contract;
//                }
//            }
//            return false;
//
//        }
//    }

//    public function getChangesApi()
//    {
//        $count = 0;
//
//        $response = Yii::$app->opAPI->tenders(
//            null,
//            null,
//            null,
//            //date('c',strtotime('- '. Yii::$app->params['tender_update_interval'] .' minute')));
//            date('c', strtotime('- 1 day')));
//
//        //return print_r($response,1);
//
//        if (count($response['body']['data'])) {
//            foreach ($response['body']['data'] AS $row) {
//                $tender = Tenders::find()
//                    ->where(['tender_id' => $row['id']])
//                    ->andWhere(['<>', 'date_modified', $row['dateModified']])
//                    ->one();
//
//                //print_r($tender);
//
//                if ($tender !== null) {
//                    $this->addTask($tender->id, $tender->tender_id);
//                    $count++;
//                }
//            }
//        }
//        return $count;
//    }

    public static function addTaskByDocument($document_task)
    {
        return self::addTask($document_task->tid, $document_task->tender_id);
    }

    public static function addTask($tid, $contract_id, $cid = null, $token = null, $tender_token = null)
    {
        $contract = null;
        if (!$tender_token) {
            // Проверка на дубли
            $contract = self::find()->where(['contract_id' => $contract_id, 'status' => '0'])->all();
        }
        if (!$contract) {
            $contract = new ContractingUpdateTask();
            $contract->tid = $tid;
            $contract->cid = $cid;
            $contract->contract_id = $contract_id;
            $contract->contract_token = $token;
            $contract->tender_token = $tender_token;
            return $contract->save(false);
        }
        return null;
    }

}
