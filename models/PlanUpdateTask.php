<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tender_update_task".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $plan_id
 * @property string $plan_token
 * @property string $created_at
 * @property string $transaction_id
 * @property string $api_answer
 * @property integer $status
 */
class PlanUpdateTask extends \yii\db\ActiveRecord
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
        return 'plan_update_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid'], 'required'],
            [['created_at'], 'safe'],
            [['api_answer'], 'string'],
            [['pid', 'status'], 'integer'],
            [['plan_id', 'plan_token', 'transaction_id'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'plan_id' => Yii::t('app', 'Tender ID'),
            'plan_token' => Yii::t('app', 'Tender Token'),
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
    public function updatePlanApi($task=null, $mail=true)
    {
        if (!$task)
            $task = $this;

        if (($plan = Plans::findOne(['id'=>$task->pid])) === null) {
            throw new \Exception('План не найден в таблице планов...', 0);
        }

        $response = Yii::$app->opAPI->plans(
            null,
            $plan->plan_id
        );
        $plan->description   = $response['body']['data']['budget']['description'];
        $plan->status        = 'published';
        $plan->response      = $response['raw'];
        $plan->date_modified = $response['body']['data']['dateModified'];
        $plan->save();

        $task->api_answer = $response['raw'];
        $task->status     = 2;
        $task->save();
//        if($task->save()) {
//
//            if ($mail) {
//                $data = Json::decode($response['raw']);
////                Yii::$app->VarDumper->dump($data, 10, true);die;
//                if(isset($data['data']['questions'])){
//                    foreach ($data['data']['questions'] as $q => $v) {
//                        if (isset($v['answer'])){
//                            continue;
//                        } else{
//                            $send = true;
//                        }
//
//                    }
//                } else if(isset($data['data']['awards'])){
//                    foreach ($data['data']['awards'] as $a => $v) {
//                        if (isset($v['status']) && $v['status'] == 'active' && isset($v['complaints'])){
//                            foreach ($v['complaints'] as $complaint) {
//                                if($complaint['status'] != 'answered'){
//                                    $send = true;
//                                }
//
//                            }
//                        }
//                    }
//                }
//
//                //если необходима отсылка и письмо отправлялось больше, чем сутки назад
//                if((isset($send) && $send) && ($plan->mail_send_at == NULL || $plan->mail_send_at < strtotime('-1 day'))) {
//
//                    //достаем email овнера компании
//                    $ownerEmail = User::getOwnerByCompanyId($plan->company_id)->username;
//
//                    //отсылаем письмо овнеру компании о неотвеченых вопросах и замечаниях
//                    $isSend = Yii::$app->mailer->compose('_new_questions', [
//                        'tender' => $data['data']
//                    ])
//                        ->setFrom([Yii::$app->params['mail_sender'] =>'DZO'])
//                        ->setTo($ownerEmail)
//                        ->setSubject('Информация по тендеру ' . $data['data']['title'])
//                        ->send();
//
//                    if($isSend){
//                        $plan->mail_send_at = strtotime('now');
//                        $plan->save(false);
//                    }
//                }
//            }
//        }

    }

//    public function getChangesApi()
//    {
//        $count = 0;
//
//        $response = Yii::$app->opAPI->tenders(
//            null,
//            null,
//            null,
//            //date('c',strtotime('- '. Yii::$app->params['tender_update_interval'] .' minute')));
//            date('c',strtotime('- 1 day')));
//
//        //return print_r($response,1);
//
//        if (count($response['body']['data'])) {
//            foreach ($response['body']['data'] AS $row) {
//                $tender = Tenders::find()
//                    ->where(['tender_id'=>$row['id']])
//                    ->andWhere(['<>','date_modified',$row['dateModified']])
//                    ->one();
//
//                //print_r($tender);
//
//                if ($tender !== null) {
//                    $this->addTask($tender->id,$tender->tender_id);
//                    $count++;
//                }
//            }
//        }
//        return $count;
//    }

//    public static function addTaskByDocument($document_task)
//    {
//        return self::addTask($document_task->tid,$document_task->tender_id);
//    }

//    public static function addTask($tid,$tender_id)
//    {
//        $tender = self::find()->where(['tender_id'=>$tender_id,'status'=>'0'])->all();
//        if (!$tender) {
//            $tender = new TenderUpdateTask();
//            $tender->tid = $tid;
//            $tender->tender_id = $tender_id;
//            return $tender->save(false);
//        }
//        return null;
//    }

}
