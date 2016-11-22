<?php

namespace app\models;

use app\components\apiDataException;
use app\components\ApiHelper;
use app\models\tenderModels\Document;
use app\modules\seller\helpers\HBid;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "document_upload_task".
 *
 * @property integer $id
 * @property integer $tid
 * @property string $file
 * @property string $title
 * @property string $type
 * @property string $mime
 * @property string $document_id
 * @property string $tender_id
 * @property string $tender_token
 * @property integer $document_type
 * @property string $document_of
 * @property string $related_item
 * @property string $exec_json
 * @property string $extra_path_data
 * @property string $created_at
 * @property string $upload_at
 * @property string $transaction_id
 * @property string $api_answer
 * @property integer $status
 */
class DocumentUploadTask extends \yii\db\ActiveRecord
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
     * @var string
     */
    public $_delimiter = '###DELIMITER###';

    /**
     * @var array
     */
    public $_document_types = ['tender', 'item', 'lot', 'cancelation', 'award', 'complaint'];

    /**
     * @var array
     */
    public $_document_types_add_to_url = ['cancelation', 'award', 'complaint']; // ['cancelations','awards','complaints'];

    public static $_mime_types = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'xls' => 'application/vnd.ms-excel',
        'xlcx' => 'application/vnd.ms-excel',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'rtf' => 'application/rtf',
        'zip' => 'application/x-zip-compressed',
    ];

    public static $_mime_def = 'text/plain';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document_upload_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'tid'], 'integer'],
            [['created_at', 'upload_at', 'exec_json'], 'safe'],
            [['file', 'title'], 'string', 'max' => 255],
            [['mime'], 'string', 'max' => 50],
            [['document_id', 'tender_id', 'tender_token', 'related_item', 'transaction_id'], 'string', 'max' => 32],
            [['document_of', 'document_type'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file' => Yii::t('app', 'File'),
            'title' => Yii::t('app', 'Title'),
            'type' => Yii::t('app', 'Type'),
            'mime' => Yii::t('app', 'Mime'),
            'document_id' => Yii::t('app', 'Document ID'),
            'tender_id' => Yii::t('app', 'Tender ID'),
            'tender_token' => Yii::t('app', 'Tender Token'),
            'document_type' => Yii::t('app', 'Document Type'),
            'document_of' => Yii::t('app', 'Document Of'),
            'related_item' => Yii::t('app', 'Related Item'),
            'created_at' => Yii::t('app', 'Created At'),
            'upload_at' => Yii::t('app', 'Upload At'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public static function addFile($file, $tid, $params = null) // $mime=null, $tenderId=null, $tenderToken=null, $json=null)
    {
        $rec = new DocumentUploadTask();
        $rec->file = $file;
        $rec->tid = $tid;
        $rec->setAttributes($params);
        $rec->save(false);
        return $rec;
    }

    public static function getFile($file)
    {
        return DocumentUploadTask::findOne(['file' => $file]);
    }

    /**
     * @param $file
     * @return false|int
     * @throws \Exception
     */
    public static function removeFile($file)
    {
        if ($row = DocumentUploadTask::findOne(['file' => $file])) {
            $row->delete();
        }
    }

    /**
     * @param $tid int
     * @param $tenderId string
     * @param $token string
     * @param $post array
     * @param null $response
     * @throws \Exception
     */
    public static function updateTableAfterSave($tid, $tenderId, $token, $post, $response = null)
    {
        if (!isset($post['Tender']['documents'])) return;

        $errors = 0;
        $item_ids = [];
        $lot_ids = [];
        $documents_ids = [];
        $documents_list = [];
        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['documents'])) {
//                $newDocs = Document::getLatestDocuments($response['data']['documents']);
                $newDocs = $response['data']['documents'];
                foreach ($newDocs AS $d) {
                    $documents_ids[] = $d['id'];
                    /**
                     * @TODO: Исправить костыль [relatedItem in documents]
                     * Костыль от/для ЦБД, в ЦБД не очищают `relatedItem` после смены `documentOf` на 'tender'
                     */
                    if ($d['documentOf'] === 'tender') {
                        $d['relatedItem'] = '';
                    }
                    // === Костыль

                    $documents_list[$d['id']] = $d;
                }
            }
        }

        foreach ($post['Tender']['items'] AS $item) {
            $item_ids[] = $item['id'];
        }

        if (isset($post['Tender']['lots'])) {
            foreach ($post['Tender']['lots'] AS $lot) {
                $lot_ids[] = $lot['id'];
            }
        }

        if (count($post['Tender']['documents'])) {
            foreach ($post['Tender']['documents'] AS $document) {

                if ($document['documentOf'] === 'tender' && $document['relatedItem'] === 'tender') $document['relatedItem'] = '';

                if (isset($document['realName']) && $document['realName']) {

                    if (!($update_task = self::getFile($document['realName']))) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }
                    //$errors++; continue; }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }
                    //$errors++; continue; }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }

                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }

                    //$errors++; continue; }

                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||

                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem'])) ||
                        (!isset($documents_list[$document['id']]['relatedItem']) && ($document['documentOf'] != 'tender'))
                    ) {

//                        if ($document['realName']) echo '[realName]';
//                        if ($document['title']        != $documents_list[$document['id']]['title']) echo '[title]';
//                        if ($document['documentType'] != $documents_list[$document['id']]['documentType']) echo '[documentType]';
//                        if (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem'])) echo '[relatedItem]';
//                        if(!isset($documents_list[$document['id']]['relatedItem']) && ($document['documentOf'] != 'tender')) echo 'last';
////
////
//                        echo '<pre>'; print_r($document); print_r($documents_list[$document['id']]); echo '</pre>'; die();
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }

                if ($document['documentOf'] == 'item' && in_array($document['relatedItem'], $item_ids)) {
                    //
                } else if ($document['documentOf'] == 'lot' && in_array($document['relatedItem'], $lot_ids)) {
                    //
                } else {
                    $document['documentOf'] = 'tender';
                    $document['relatedItem'] = '';
                }

                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title']; //str_replace('.'.$document['ext'],'',$document['title']) .'.'. $document['ext'];
                $update_task->type = 'tender';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = $document['id'];
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = $document['documentType'];
                $update_task->document_of = $document['documentOf'];
                $update_task->related_item = $document['relatedItem'];
//echo '<pre>'; print_r($update_task); die();
                $update_task->save(false);
            }
        }
    }

    /**
     * @param $tid int
     * @param $tenderId string
     * @param $token string
     * @param $post array
     * @param null $response
     * @throws \Exception
     */
    public static function updateTableAfterSaveCancel($tid, $tenderId, $token, $post, $response = null)
    {
        //echo '<pre>'; print_r($post['Tender']['documents']); die();
        if (!isset($post['documents'])) return;

        $errors = 0;
        $item_ids = [];
        $lot_ids = [];
        $documents_ids = [];
        $documents_list = [];

        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['documents'])) {
                foreach ($response['data']['documents'] AS $d) {
                    $documents_ids[] = $d['id'];
                    $documents_list[$d['id']] = $d;
                }
            }
            if (isset($response['data']['cancellations'])) {
                foreach ($response['data']['cancellations'] AS $c) {
                    if (isset($c['documents'])) {
                        foreach ($c['documents'] AS $d) {
                            $documents_ids[] = $d['id'];
                            $documents_list[$d['id']] = $d;
                        }
                    }
                }
            }
        }


        if (isset($post['items'])) {
            foreach ($post['items'] AS $item) {
                $item_ids[] = $item['id'];
            }
        }

        if (isset($post['lots'])) {
            foreach ($post['lots'] AS $lot) {
                $lot_ids[] = $lot['id'];
            }
        }

        //echo '<pre>'; print_r($post['documents']); die();
        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!($update_task = self::getFile($document['realName']))) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }
                    //$errors++; continue; }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }
                    //$errors++; continue; }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }

                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }
                    //$errors++; continue; }

                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||
                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem'])) ||
                        (!isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != 'tender'))
                    ) {

//                        if ($document['realName']) echo '[realName]';
//                        if ($document['title']        != $documents_list[$document['id']]['title']) echo '[title]';
//                        if ($document['documentType'] != $documents_list[$document['id']]['documentType']) echo '[documentType]';
//                        if (($document['relatedItem']  != $documents_list[$document['id']]['relatedItem']) && isset($documents_list[$document['id']]['relatedItem'])) echo '[relatedItem]';
//
//
//                        echo '<pre>'; print_r($document); print_r($documents_list[$document['id']]); echo '</pre>'; die();
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }

                if (isset($document['documentOf']) && $document['documentOf'] == 'item' && in_array($document['relatedItem'], $item_ids)) {
                    //
                } else if (isset($document['documentOf']) && $document['documentOf'] == 'lot' && in_array($document['relatedItem'], $lot_ids)) {
                    //
                } else {
                    $document['documentOf'] = 'tender';
                    $document['relatedItem'] = '';
                }

                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title']; //str_replace('.'.$document['ext'],'',$document['title']) .'.'. $document['ext'];
                $update_task->type = 'tender';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = $document['id'];
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @$document['documentType'];
                $update_task->document_of = $document['documentOf'];
                $update_task->related_item = $document['relatedItem'];
//echo '<pre>'; print_r($update_task); die();
                $update_task->save(false);
            }
            if (isset($update_task->tid)) {
                $update_task->exec_json = '{"data":{"status":"active"}}';
                $update_task->save(false);
            }
        }
        //echo '<pre>'; print_r($post); print_r($update_task); die();
    }

    public static function updateTableAfterSaveContract($tid, $tenderId, $token, $post, $response = null, $contractId)
    {

        if (!isset($post['documents'])) return;

        $documents_ids = [];


        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['contracts'])) {
                foreach ($response['data']['contracts'] AS $c) {

                    if ($c['id'] == $contractId) {
                        if (isset($c['documents'])) {
                            foreach ($c['documents'] AS $d) {
                                $documents_ids[] = $d['id'];
                                $documents_list[$d['id']] = $d;
                            }
                        }


                    }
                }
            }

        }

        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }


                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }
                    //$errors++; continue; }

                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||
                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem']))
                    ) {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'aw_contract';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @$document['documentType'];
                $update_task->document_of = 'tender';

                $update_task->save(false);
            }
//            if (isset($update_task->tid)) {
//                $update_task->exec_json = '{"data":{"status":"pending"}}';
//                $update_task->save(false);
//            }
        }
    }

    public static function updateTableAfterSaveAward($tid, $tenderId, $token, $post, $response = null, $awardId)
    {
        if (!isset($post['documents'])) return;

        $documents_ids = [];


        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['awards'])) {
//                Yii::$app->VarDumper->dump($response['data']['awards'], 10, true);die;
                foreach ($response['data']['awards'] AS $c) {
                    if ($c['id'] == $awardId) {
                        if (isset($c['documents'])) {
                            foreach ($c['documents'] AS $d) {

                                $documents_ids[] = $d['id'];
                                $documents_list[$d['id']] = $d;
                            }
                        }


                    }
                }
            }

        }

        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }


                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }
                    //$errors++; continue; }
//                    Yii::$app->VarDumper->dump($document['documentType'], 10, true);
//                    Yii::$app->VarDumper->dump($documents_list[$document['id']], 10, true);die;
                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||
                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem']))
                    ) {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                } else {
                    $update_task = new DocumentUploadTask();
                }


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'tender';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @$document['documentType'];
                $update_task->document_of = 'tender';

                $update_task->save(false);
            }

        }
    }

    public static function updateTableAfterSavePrequalification($tid, $tenderId, $token, $post, $response = null, $prequalificationId)
    {
        if (!isset($post['documents'])) return;

        $documents_ids = [];


        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['awards'])) {
//                Yii::$app->VarDumper->dump($response['data']['awards'], 10, true);die;
                foreach ($response['data']['qualification'] AS $c) {
                    if ($c['id'] == $prequalificationId) {
                        if (isset($c['documents'])) {
                            foreach ($c['documents'] AS $d) {

                                $documents_ids[] = $d['id'];
                                $documents_list[$d['id']] = $d;
                            }
                        }


                    }
                }
            }

        }

        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }


                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }
                    //$errors++; continue; }
//                    Yii::$app->VarDumper->dump($document['documentType'], 10, true);
//                    Yii::$app->VarDumper->dump($documents_list[$document['id']], 10, true);die;
                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||
                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem']))
                    ) {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                } else {
                    $update_task = new DocumentUploadTask();
                }


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'tender';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @$document['documentType'];
//                $update_task->document_of = 'tender';

                $update_task->save(false);
            }

        }
    }

    public static function updateTableAfterSaveComplaint($tid, $tenderId, $token, $post, $type)
    {
//Yii::$app->VarDumper->dump($post, 10, true);die;
        if (!isset($post['documents'])) return;

        $documents_ids = [];


        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }


                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }


                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = $type;
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @$document['documentType'];
                $update_task->document_of = 'tender';

                $update_task->save(false);
            }
            if (isset($update_task->tid)) {
//Yii::$app->VarDumper->dump($post, 10, true);die;
//                if (isset($post['prequalification_complaint_submit'])){
//                    $update_task->exec_json = '{"data":{"status":"pending"}}';
//                }elseif(isset($post['award_complaint_submit'])){
//                    $update_task->exec_json = '{"data":{"status":"'.$post['Complaint']['status'].'"}}';
//                }else
                if(isset($post['answer_complaint_submit'])){
//                    if  (isset($post['tendererAction'])){
                        $update_task->exec_json = '{"data":{"status":"answered"}}';
//                    } else {
//                        $update_task->exec_json = null;
//                    }
                }elseif(isset($post['add_documents_to_complaints'])) {
                    $update_task->exec_json = null;
                }else{
                    // в период обсуждения для сверхпорогов можно выбирать тип жалобы.
                    if(isset($post['Complaint']['status']) && $post['Complaint']['status']){
                        $update_task->exec_json = '{"data":{"status":"'.$post['Complaint']['status'].'"}}';
                    }else{
                        $update_task->exec_json = '{"data":{"status":"claim"}}';
                    }
                }

                $update_task->save(false);
            }
        }
    }

    public static function updateTableAfterSaveBid($tid, $tenderId, $token, $post, $response = null, $bidId, $tenders, $bid)
    {

        if (!isset($post['documents'])) return;

        $documents_ids = [];

        if ($response) {
            $response = json_decode($response, 1);

            if (isset($response['data']['documents'])) {
                foreach ($response['data']['documents'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['financialDocuments'])) {
                foreach ($response['data']['financialDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['eligibilityDocuments'])) {
                foreach ($response['data']['eligibilityDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['qualificationDocuments'])) {
                foreach ($response['data']['qualificationDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

        }


        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {
                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;
                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }

                if (isset($document['id']) && $document['id']) {

                    if (!in_array($document['id'], $documents_ids)) {
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }

                    if (isset($document['relatedItem'])) {
                        if ($document['relatedItem'] != 'tender') {
                            $document['documentOf'] = 'lot';
                        } else {
                            $document['relatedItem'] = '';
                            $document['documentOf'] = 'tender';
                        }
                    }



                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        (isset($document['documentType']) && isset($documents_list[$document['id']]['documentType'])) && ($document['documentType'] != $documents_list[$document['id']]['documentType']) ||
                        (isset($documents_list[$document['id']]['relatedItem']) && $document['relatedItem'] != $documents_list[$document['id']]['relatedItem']) ||
                        (!isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != '')) ||
                        (isset($confidentiality) && isset($documents_list[$document['id']]['confidentiality'])) && ($confidentiality != $documents_list[$document['id']]['confidentiality'])
                    ) {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {

                    // New doc
                    $document['id'] = '';
                    if (isset($document['relatedItem'])) {
                        if ($document['relatedItem'] != 'tender') {
                            $document['documentOf'] = 'lot';
                        } else {
                            $document['relatedItem'] = '';
                            $document['documentOf'] = 'tender';
                        }
                    }


                }

                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }

                //костылим конверты документов
//                $tenderIdForUrl = HBid::getDocumentURL($tenderId, $document);


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'bid';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
//                $rrr = isset($document['mime']) ? $document['mime'] : $update_task->mime;
//                Yii::$app->VarDumper->dump($rrr, 10, true);
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
//                $update_task->tender_id = $tenderIdForUrl;
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @isset($document['documentType']) ? $document['documentType'] : '';
                $update_task->document_of = $document['documentOf'];
                $update_task->related_item = $document['relatedItem'];

                if (isset($confidentiality) && $confidentiality) {
                    if ($confidentiality == 'buyerOnly') {
                        $update_task->extra_path_data = $confidentiality . ',' . $confidentialityRationale;
                    } else {
                        $update_task->extra_path_data = $confidentiality;
                    }
                }

                $update_task->save(false);

            }
            // после загрузки файлов ставим задачу на обновление ставки

//            $bidTaskModel = new BidUpdateTask();
//            $bidTaskModel->bid = $bid->id;
//            $bidTaskModel->bid_id = $bid->bid_id;
//            $bidTaskModel->tid = $tenders->id;
//            $bidTaskModel->bid_token = $bid->token;
//            $bidTaskModel->save(false);
//            Yii::$app->VarDumper->dump($bidTaskModel->save(false), 10, true);die;
        }
    }

    public static function uploadAwardFiles($tid, $tenderId, $token, $post, $response = null, $tenders, $bid)
    {
//Yii::$app->VarDumper->dump($post, 10, true);die;
        
        if (!isset($post['documents'])) return;

        $documents_ids = [];

        if ($response) {
            $response = json_decode($response, 1);

            if (isset($response['data']['documents'])) {
                foreach ($response['data']['documents'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['financialDocuments'])) {
                foreach ($response['data']['financialDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['eligibilityDocuments'])) {
                foreach ($response['data']['eligibilityDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

            if (isset($response['data']['qualificationDocuments'])) {
                foreach ($response['data']['qualificationDocuments'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

        }


        if (count($post['documents'])) {
            foreach ($post['documents'] AS $document) {
                
                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;
                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }

                if (isset($document['id']) && $document['id']) {

                    if (!in_array($document['id'], $documents_ids)) {
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }

                    if (isset($document['relatedItem'])) {
                        if ($document['relatedItem'] != 'tender') {
                            $document['documentOf'] = 'lot';
                        } else {
                            $document['relatedItem'] = '';
                            $document['documentOf'] = 'tender';
                        }
                    }



                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        (isset($document['documentType']) && isset($documents_list[$document['id']]['documentType'])) && ($document['documentType'] != $documents_list[$document['id']]['documentType']) ||
                        (isset($documents_list[$document['id']]['relatedItem']) && $document['relatedItem'] != $documents_list[$document['id']]['relatedItem']) ||
                        (!isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != '')) ||
                        (isset($confidentiality) && isset($documents_list[$document['id']]['confidentiality'])) && ($confidentiality != $documents_list[$document['id']]['confidentiality'])
                    ) {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {

                    // New doc
                    $document['id'] = '';
                    if (isset($document['relatedItem'])) {
                        if ($document['relatedItem'] != 'tender') {
                            $document['documentOf'] = 'lot';
                        } else {
                            $document['relatedItem'] = '';
                            $document['documentOf'] = 'tender';
                        }
                    }


                }

                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }

                //костылим конверты документов
                $tenderIdForUrl = HBid::getDocumentURL($tenderId, $document);


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'tender';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
//                $rrr = isset($document['mime']) ? $document['mime'] : $update_task->mime;
//                Yii::$app->VarDumper->dump($rrr, 10, true);
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderIdForUrl;
                $update_task->tender_token = $token;
                $update_task->document_type = @isset($document['documentType']) ? $document['documentType'] : '';
                $update_task->document_of = $document['documentOf'];
                $update_task->related_item = $document['relatedItem'];

                if (isset($confidentiality) && $confidentiality) {
                    if ($confidentiality == 'buyerOnly') {
                        $update_task->extra_path_data = $confidentiality . ',' . $document['confidentialityRationale'];
                    } else {
                        $update_task->extra_path_data = $confidentiality;
                    }
                }

                $update_task->save(false);

            }
            // после загрузки файлов ставим задачу на обновление ставки

            $bidTaskModel = new BidUpdateTask();
            $bidTaskModel->bid = $bid->id;
            $bidTaskModel->bid_id = $bid->bid_id;
            $bidTaskModel->tid = $tenders->id;
            $bidTaskModel->bid_token = $bid->token;
            $bidTaskModel->save(false);
//            Yii::$app->VarDumper->dump($bidTaskModel->save(false), 10, true);die;
        }
    }

    public static function updateTableAfterSaveContracting($tid, $tenderId, $token, $post, $response = null, $relatedChangeId, $needActivate = true, $needTerminate = false)
    {
//Yii::$app->VarDumper->dump($post, 10, true, true);
        if (!isset($post['Contract']['documents'])) return;

        $documents_ids = [];


        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['documents'])) {
                foreach ($response['data']['documents'] AS $doc) {
                    $documents_ids[] = $doc['id'];
                    $documents_list[$doc['id']] = $doc;
                }
            }

        }

        if(isset($post['Contract']['documents']['__EMPTY_DOC__'])){
            unset($post['Contract']['documents']['__EMPTY_DOC__']);
        }

//        Yii::$app->VarDumper->dump($post, 10, true, true);
        if (count($post['Contract']['documents'])) {
            foreach ($post['Contract']['documents'] AS $document) {

                if (isset($document['realName']) && $document['realName']) {

                    if (!$update_task = self::getFile($document['realName'])) {
                        throw new \Exception('The requested file does not exist in DB: ' . $document['realName']);
                    }

                    if (!file_exists(Yii::$app->params['upload_dir'] . $document['realName'])) {
                        throw new \Exception('The requested file does not exist on disk: ' . $document['realName']);
                    }

                    $document['ext'] = pathinfo(Yii::$app->params['upload_dir'] . $document['realName'], PATHINFO_EXTENSION);
                    $document['mime'] = isset(self::$_mime_types[$document['ext']]) ? self::$_mime_types[$document['ext']] : self::$_mime_def;

                } else {
                    $document['realName'] = '';
                    $document['ext'] = '';
                }


                if (isset($document['id']) && $document['id']) {
                    // exist lot
                    if (!in_array($document['id'], $documents_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        throw new \Exception('The requested document id does not exist: ' . $document['id']);
                    }
                    //$errors++; continue; }

                    if ($document['realName'] ||
                        $document['title'] != $documents_list[$document['id']]['title'] ||
                        $document['documentType'] != $documents_list[$document['id']]['documentType'] ||
                        (isset($documents_list[$document['id']]['relatedItem']) && ($document['relatedItem'] != $documents_list[$document['id']]['relatedItem']))

                    )

                    {
//                        Yii::$app->VarDumper->dump(123, 10, true);die;
                        $document['ext'] = self::findExtension($documents_list[$document['id']]['title']);
                        $document['mime'] = $documents_list[$document['id']]['format'];

                    } else {
                        // Ничего не изменилось, пропускаем документ
                        continue;
                    }

                } else {
                    // New doc
                    $document['id'] = '';
                }


                if (isset($document['realName']) && $document['realName']) {
                    $update_task = self::getFile($document['realName']);
                    //echo 'zzzzzzzz'; print_r($document['realName']); print_r($update_task); die();
                } else {
                    $update_task = new DocumentUploadTask();
                }


                $update_task->tid = $tid;
                $update_task->file = $document['realName'];
                $update_task->title = $document['title'];
                $update_task->type = 'contracting';
                $update_task->mime = isset($document['mime']) ? $document['mime'] : $update_task->mime;
                $update_task->document_id = isset($document['id']) ? $document['id'] : '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = @isset($document['documentType']) ? $document['documentType'] : '';
                $update_task->document_of = $relatedChangeId ? 'change' : 'contract';
                $update_task->related_item = $relatedChangeId ? $relatedChangeId : '';

                $update_task->save(false);
            }

            if($needActivate) {
                // создаем запись для активации
                $update_task = new DocumentUploadTask();
                $update_task->tid = $tid;
                $update_task->file = '';
                $update_task->title = '';
                $update_task->type = 'contracting';
                $update_task->mime = 'image/jpeg';
                $update_task->document_id = '';
                $update_task->tender_id = $tenderId . '/changes/' . $relatedChangeId;
                $update_task->tender_token = $token;
                $update_task->document_type = '';
                $update_task->document_of = 'change';
                $update_task->exec_json = $update_task->exec_json = '{"data": {"status": "active", "dateSigned": "' . ApiHelper::convertDate($post['Changes']['dateSigned'], true) . '"}}';

                $update_task->save(false);
            }

            if($needTerminate) {
                // создаем запись для завершения контракта
                $update_task = new DocumentUploadTask();
                $update_task->tid = $tid;
                $update_task->file = '';
                $update_task->title = '';
                $update_task->type = 'contracting';
                $update_task->mime = 'image/jpeg';
                $update_task->document_id = '';
                $update_task->tender_id = $tenderId;
                $update_task->tender_token = $token;
                $update_task->document_type = '';
                $update_task->document_of = 'contract';
                $update_task->exec_json = '{"data":{"status":"terminated"}}';

                $update_task->save(false);
            }

//            if (isset($update_task->tid)) {
//                $update_task->exec_json = '{"data":{"status":"active"}}';
//                $update_task->save(false);
//            }
        }
    }


    public static function findExtension($file_name)
    {
        $tmp = explode('.', $file_name);
        return $tmp[count($tmp) - 1];
    }

    //*
    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['id' => 'document_type']);
    }//*/

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
            ->andWhere(['<>', 'tender_id', ''])
            ->orderBy('created_at')
            ->one();
        return $task;
    }

    public function lockRecord($id)
    {
        if (!$this->_transaction_id) {
            $this->_transaction_id = time() . ' ' . Yii::$app->security->generateRandomString(5) .' -new';
        }
        //$sql   = 'UPDATE  `'. self::tableName() .'`  SET  `transaction_id` = :transaction_id  WHERE  `id` = :id  AND `transaction_id` = \'\';';
        $sql = 'UPDATE  `' . self::tableName() . "`  SET  `transaction_id` = '{$this->_transaction_id}'  WHERE  `id` = '{$id}' AND `transaction_id` = '';";
        $query = $this->getDb()->createCommand($sql);

        return $query->bindValues([':id' => $id, ':transaction_id' => $this->_transaction_id])->execute();
        //return $query->execute();
    }

    /**
     * @return false|DocumentUploadTask
     */
    public function getNextTask()
    {
        if ($task = $this->getFindNew()) {
            if ($lock = $this->lockRecord($task->id)) {
                $task->transaction_id = $this->_transaction_id;
                if (in_array($task->document_of, $this->_document_types_add_to_url)) {
                    $task->tender_id .= '/' . $task->document_of . 's/' . $task->related_item;
                }
                return $task;
            }
        }
        return false;
    }

    /**
     * @param $task DocumentUploadTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function sendFileApiViaDS($task = null)
    {
        if (!$task)
            $task = $this;

        if($task->type == 'contracting'){
            $type = 'contracts';
        } else {
            $type = 'tenders';
        }

        $data['data']['title'] = $task->title;

        if ($task->document_type) {
            $data['data']['documentType'] = $task->document_type;
        }

        if ($task->document_of) {
            if (!in_array($task->document_of, $this->_document_types_add_to_url)) {
                $data['data']['documentOf'] = $task->document_of;
                if ($task->related_item) {
                    $data['data']['relatedItem'] = $task->related_item;
                } else {
                    $data['data']['relatedItem'] = null;
                }
            }
        }

        if ($task->extra_path_data) {
            $res = explode(',', $task->extra_path_data);
            if ($res) {
                $data['data']['confidentiality'] = $res[0];
                if (isset($res[1]) && $res[1]) {
                    $data['data']['confidentialityRationale'] = $res[1];
                }
            }
        }

        $response = Yii::$app->opAPI->AddTendersDocumentsInApiDS(
            [
                'name' => Yii::$app->params['upload_dir'] . $task->file,
                'title' => $task->title,
                'mime' => $task->mime],
            $data,
            $task->tender_id,
            $task->tender_token,
            $task->document_id,
            $type
        );

        if (!isset($response['body']['data']['id'])) {
            throw new apiDataException('Not exist document `id` in API response ' . print_r($response, 1), 0);
        } elseif($response['upload_url'] && !is_null($response['upload_url'])) {
            $raw = Yii::$app->opAPI->UploadTendersDocumentsDS([
                'name' => Yii::$app->params['upload_dir'] . $task->file,
                'title' => $task->title,
                'mime' => $task->mime
            ],
                $response['upload_url'],
                $response['body']['data']['id']
            );
        }
        $allResponse = ['AddInfoToTender' => Json::decode($response['raw'], true), 'uploadDocument' => Json::decode($raw['raw'], true)];
        $task->document_id = $response['body']['data']['id'];
        $task->api_answer = Json::encode($allResponse);
        $task->status = 1;

        return $task->save(false);
    }

    public function updateFileApiViaDS($task = null, $type = 'tenders')
    {
        if (!$task)
            $task = $this;

        if ($task->type == 'contracting') {
            $type = 'contracts';
        } else {
            $type = 'tenders';
        }

        $data['data']['title'] = $task->title;

        if ($task->document_type) {
            $data['data']['documentType'] = $task->document_type;
        }

        if ($task->document_of) {
            if (!in_array($task->document_of, $this->_document_types_add_to_url)) {
                $data['data']['documentOf'] = $task->document_of;
                if ($task->related_item) {
                    $data['data']['relatedItem'] = $task->related_item;
                } else {
                    $data['data']['relatedItem'] = null;
                }
            }
        }

        if ($task->extra_path_data) {
            $res = explode(',', $task->extra_path_data);
            if ($res) {
                $data['data']['confidentiality'] = $res[0];
                if (isset($res[1]) && $res[1]) {
                    $data['data']['confidentialityRationale'] = $res[1];
                }
            }

        }

        $response = Yii::$app->opAPI->tendersDocuments(
            null,
            Json::encode($data),
            $task->tender_id,
            $task->tender_token,
            $task->document_id,
            $type
        );

        if (!isset($response['body']['data']['id'])) {
            throw new apiDataException('Not exist document `id` in API response ' . print_r($response, 1), 0);
        }

        $task->api_answer = $task->api_answer . $this->_delimiter . $response['raw'];
        $task->status = 2;

        return $task->save(false);
    }
    /**
     * @param $task DocumentUploadTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function sendFileApi($task = null)
    {
        if (!$task)
            $task = $this;

        if($task->type == 'contracting'){
            $type = 'contracts';
        } else {
            $type = 'tenders';
        }

        $response = Yii::$app->opAPI->tendersDocuments(
            [
                'name' => Yii::$app->params['upload_dir'] . $task->file,
                'title' => $task->title,
                'mime' => $task->mime],
            null,
            $task->tender_id,
            $task->tender_token,
            $task->document_id,
            $type
        );

//        print_r($response);

        if (!isset($response['body']['data']['id'])) {
            throw new apiDataException('Not exist document `id` in API responce ' . print_r($response, 1), 0);
        }

        $task->document_id = $response['body']['data']['id'];
        $task->api_answer = $response['raw'];
        $task->status = 1;

        return $task->save(false);
    }
    
    /**
     * @param $task DocumentUploadTask
     * @throws \Exception if request failed
     * @return mixed
     */
    public function updateFileApi($task = null, $type = 'tenders')
    {
        if (!$task)
            $task = $this;

        if($task->type == 'contracting'){
            $type = 'contracts';
        }else{
            $type = 'tenders';
        }

        $data = [
            'data' => [
                'title' => $task->title,]]; //'documentType' => $task->document_type,

        if ($task->document_type) {
            $data['data']['documentType'] = $task->document_type;
        }

        if ($task->document_of) {
            if (!in_array($task->document_of, $this->_document_types_add_to_url)) {
                $data['data']['documentOf'] = $task->document_of;
                if ($task->related_item) {
                    $data['data']['relatedItem'] = $task->related_item;
                } else {
                    $data['data']['relatedItem'] = null;
                }
            }
        }

        if ($task->extra_path_data) {
            $res = explode(',', $task->extra_path_data);
            if ($res) {
                $data['data']['confidentiality'] = $res[0];
                if (isset($res[1]) && $res[1] != '') {
                    if (isset($res[2]) && $res[2] != '') { // 'open_competitiveDialogueUA', 'open_competitiveDialogueEU'
                        $data['data'][$res[2]] = $res[1];
                    }else{
                        $data['data']['confidentialityRationale'] = $res[1];
                    }

                }
            }
//            Yii::$app->VarDumper->dump($data, 10, true);die;
//            file_put_contents(Yii::getAlias('@root').'/extra_path_data.txt', print_r($data,1), FILE_APPEND);
        }


//        var_dump($data);

        // ---
        $response = Yii::$app->opAPI->tendersDocuments(
            null,
            json_encode($data),
            $task->tender_id,
            $task->tender_token,
            $task->document_id,
            $type
        );

        //print_r($response);

        if (!isset($response['body']['data']['id'])) {
            throw new apiDataException('Not exist document `id` in API responce ' . print_r($response, 1), 0);
        }

        $task->api_answer = $task->api_answer . $this->_delimiter . $response['raw'];
        $task->status = 2;

        return $task->save(false);
    }

    public function executeFileApi($task = null)
    {
        if (!$task)
            $task = $this;

        if (!$task->exec_json) {
            return false;
        }

        if($task->type == 'contracting'){

            $response = Yii::$app->opAPI->contracts(
                $task->exec_json,
                $task->tender_id,
                $task->tender_token
            );

        }else{

            $response = Yii::$app->opAPI->tenders(
                $task->exec_json,
                $task->tender_id,
                $task->tender_token
            );
        }



        // ---


        //print_r($response);

        if (!isset($response['body']['data']['id'])) {
            throw new apiDataException('Not exist document `id` in API responce ' . print_r($response, 1), 0);
        }

        $task->api_answer = $task->api_answer . $this->_delimiter . $response['raw'];
        $task->status = 3;

        return $task->save(false);

    }

    /**
     * Выполняет загрузку документа для тендера
     * @param $file
     */
    public static function forceDocument($file, $type='tenders')
    {
        $model = self::findOne(['file' => $file]);

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


    public static function GetUploadedDoc($tId, $type, $nameCondition, $ids = [],$tender_id=null)
    {
        $html = '';

        $query = DocumentUploadTask::find()
            ->where(['tid' => $tId])
            ->andWhere(['not in', 'status', [1, 2, 3]])
            ->andWhere(['!=', 'document_of', ''])
            ->andWhere(['type'=>$type])
            ->andFilterWhere(['document_of' => $nameCondition]);

        if  (isset($ids) && !empty($ids)) {
            $query->andFilterWhere(['related_item' => $ids]);
        }

        if (isset($tender_id) && $tender_id) {
            $query->andWhere(['tender_id'=>$tender_id]);
        }


        $res = $query->all();

        foreach ($res as $dKey => $dVal) {

            $html .= '<div class="form-group">
                        <label class="col-md-3 control-label">  ' . $dVal['title'] . '</label>
                        <div class="col-md-3">
                        ' . Yii::t('app', ' Файл завантажуеться...') . '
                        </div>
                        <div class="col-md-3"></div>
                      </div>
                      <br/>';
        }
        return $html;
    }

    public function CleanTable()
    {
        if ($this->status != 0 &&
            $this->status != 8 &&
            isset($this->transaction_id) && $this->transaction_id != '' &&
            isset($this->api_answer)
        ) {
            $this->delete();
        }
    }

    public function deleteUploadedFile()
    {
        if ($this->status != 0 &&
            $this->status != 8 &&
            isset($this->transaction_id) && $this->transaction_id != '' &&
            isset($this->api_answer) &&
            isset($this->file)
        ) {
            if (file_exists(Yii::$app->params['upload_dir'] . $this->file)) {
                unlink(Yii::$app->params['upload_dir'] . $this->file);
            }
        }
    }
}
