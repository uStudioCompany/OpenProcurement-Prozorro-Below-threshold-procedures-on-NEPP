<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bid_update_task".
 *
 * @property integer $id
 * @property integer $bid
 * @property string $bid_id
 * @property string $tid
 * @property string $bid_token
 * @property string $created_at
 * @property string $transaction_id
 * @property string $api_answer
 * @property integer $status
 */
class BidUpdateTask extends \yii\db\ActiveRecord
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bid_update_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bid', 'bid_id', 'bid_token', 'transaction_id', 'api_answer', 'status'], 'required'],
            [['bid', 'status','tid'], 'integer'],
            [['created_at'], 'safe'],
            [['api_answer'], 'string'],
            [['bid_id', 'bid_token', 'transaction_id'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bid' => Yii::t('app', 'Bid'),
            'bid_id' => Yii::t('app', 'Bid ID'),
            'bid_token' => Yii::t('app', 'Bid Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'api_answer' => Yii::t('app', 'Api Answer'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getErrorTask()
    {
        $task = DocumentUploadTask::find()
            ->where(['status'=>$this->_error_code])
            ->orderBy('created_at')
            ->all();
        return $task;
    }

    public function getFindNew()
    {
        $task = self::find()
            ->where(['status'=>0,'transaction_id'=>''])
            ->orderBy('created_at')
            ->one();
        return $task;
    }

    public function lockRecord($id)
    {
        if (!$this->_transaction_id) {
            $this->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);
        }
        $sql   = 'UPDATE  `'. self::tableName() ."`  SET  `transaction_id` = '{$this->_transaction_id}'  WHERE  `id` = '{$id}' AND `transaction_id` = '';";
        $query = $this->getDb()->createCommand($sql);

        return $query->bindValues([':id'=>$id,':transaction_id'=>$this->_transaction_id])->execute();
    }

    /**
     * @return false|DocumentUploadTask
     */
    public function getNextTask() {
        if ($task = $this->getFindNew()) {
            if ($lock = $this->lockRecord($task->id)) {
                $task->transaction_id = $this->_transaction_id;
                return $task;
            }
        }
        return false;
    }

    /**
     * @param $task PlanUpdateTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function updateBidApi($task=null, $mail=true)
    {
        if (!$task)
            $task = $this;

        if (($bid = Bids::findOne(['id'=>$task->bid])) === null) {
            return false;
        }

        $tenders = Tenders::findOne($bid->tid);
        $url = $tenders->tender_id.'/bids/'.$bid->bid_id;

        $response = Yii::$app->opAPI->getBids(
            null,
            $tenders->tender_id,
            $bid->token,
            $bid->bid_id
        );

        $bid->answer      = $response['raw'];
        $bid->date_modified = $response['body']['data']['date'];
        $bid->save();

        if ($response['body']['data']['status'] === 'invalid') {
            /** @TODO: Добавить уведомление о необходимости подтвердить ставку */
        }


        $money_back = false;



        /** Ставка осталась в черновике */
        if ($tenders && $tenders->status === 'active.auction' && $response['body']['data']['status'] === 'draft ') {
            $money_back = true; }


        /** Возвращаем деньги ... */
        if ($money_back) {
            $res = Yii::$app->finance->refundMus($tenders->id, $bid->getLotsArr(), $bid->company_id);
        }

        $task->api_answer = $response['raw'];
        $task->status     = 2;
        $task->delete();


    }

    public static function addBidTaskByDocument($document_task)
    {
        return self::addBidTask($document_task->tid);
    }

    public static function addBidTask($tid)
    {
        $issetUpdate = self::find()->where(['tid' => $tid, 'status' => '0'])->all();
//        file_put_contents(Yii::getAlias('@root').'/bids.txt', var_dump($issetUpdate));
        if (!$issetUpdate) {
            $bids = Bids::find()->where(['tid'=>$tid])->all();
            if($bids){
                foreach ($bids as $b=>$bid) {
                    $update = new BidUpdateTask();
                    $update->bid = $bid->id;
                    $update->bid_id = $bid->bid_id;
                    $update->tid = $tid;
                    $update->bid_token = $bid->token;
                    $update->save(false);
                }
                return true;

            }

        }
        return true;
    }

}
