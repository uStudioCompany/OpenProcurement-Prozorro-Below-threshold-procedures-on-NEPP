<div class="admin-tender-index">
    <h1><?= $this->context->action->uniqueId ?></h1>

    <pre>
        <?

        switch (true) {
            case (true):
                echo '=aaaa=';
                continue;

            case (true):
                echo '-bbbb-';
                break;

            case (true):
                echo '(cccc)';

        }
        echo '/dddd/';
        echo '<br />'; echo '<br />';

//        echo '<br />';
//        echo Yii::$app->formatter->asDatetime('01/02/1016', "php:c");
//        echo '<br />';
//        echo date('d.m.Y H:i') .' - '. date('c');
//        echo '<br />';
//        echo Yii::$app->formatter->asDatetime('03.02.2016 12:15', "php:c");
//        echo '<br />';
//        echo date('d.m.Y H:i');
//        echo '<br />';


//        $tmp = date('d.m.Y H:i:s', strtotime('14.03.2016 16:00'));
//
//        echo $tmp;
//        echo '<br />';
//        echo date('c', strtotime(  $tmp .' +15 Days' ));
//        echo '<br />';
//        echo date('c', strtotime( str_replace('/','.', '08/05/2016' .' 16:00') ));
//        echo '<br />';
//        echo date('c', strtotime( str_replace('/','.', '14/03/2016' .' 16:00') ));
//        echo '<br />';
//        echo date('c', strtotime( str_replace('/','.', '30/04/2016' .' 16:00') ));

//        $abr = \app\models\DocumentType::getType('debarments');
//        print_r($abr);

        //"id": "15233132cda94ea99f095fd561965612",
        //"token": "6cc565aa5a124dfdb5d6da04065631b4"

        //"id": "b27837d5c37843428924928c300eb288",
        //"token": "c8b453ba1d60408baba76ee316dc7cee"

        //"id": "a709eb5ee6964ff481919cdc9ecccebf",
        //"token": "7f323f0b1ceb4d1ea8696b6b7c6381b0"

        //"id": "c3c55f77f2154362926ba12322ea1bdc",
        //"token": "3a703d1295ec43baa7717af425a86d57"

        // 2...
        //"id": "0a5cae9e46504bdf9fdcd663d878eb81"
        //"token": "731e883280e6484f9525582023f8dcfb"

        // 24
        //"id": "96b61adb0de94ec58a34d71531a7d1d3",
        //"token": "707e859083ff49df9b921363659197f9"

        $a = 'a';

        if (true && false && $a = 'b') {}

        echo "[$a]";

        if (true && true && $a = 'b') {}

        echo "[$a]";

        $a == 'b' && $a = 'c';

        echo "[$a]";

        $b = null;

        $a = $b ?: 'x';

        echo "[$a]";

        $b = 'bbbbb';

        $a = $b ?: 'x';

        echo "[$a]";


        $tmp_arr = ['aaaaaa','bbbbbbbbb','cccccccccc','ddddddddddd'];

        foreach ($tmp_arr AS $i=>&$key) {
            if ($i == 2) {
                unset( $tmp_arr[$i] );
            }
        }
        unset($key);
        $key = '333';

        print_r($tmp_arr);

        //*
        //$data = Yii::$app->opAPI->getTest();

        //print_r($data);

        //file_put_contents('data.json', $data);

        //$data = file_get_contents('data-test.json');

        //$data =json_decode($data,true);

        //file_put_contents('data-test1.php',var_export($data,true));

//        $url    =  Yii::$app->params['urlDkpp'];
//        $model  = new \app\models\Dkpp();
//        $params = $model->helperLoadCodes($url);


        /**
         * Create
         */
//        $tender = [];
//        include 'D:\_OpenServer_\domains\mark.loc\tests\codeception\_data\api\_data-test_v08.php';
//        $data = json_encode($tender); // print_r($data); die;
//        //$response = Yii::$app->opAPI->tenders($data,null,null,null);  print_r($response);


        /**
         * Update
         */
//        $tender = [];
//        include 'D:\_OpenServer_\domains\mark.loc\tests\codeception\_data\api\_data-test-edit_v08.php';
//        $data = json_encode($tender); // print_r($data); die;
//        $tender_id    = '2520c178d293485d9280c36cf527022a';
//        $token        = 'ddaddf5898e5443ab8a270c8e9fbb0f0';
//        // 2016-01-12T13:15:03.393315+02:00
//        //$response = Yii::$app->opAPI->tenders($data,$tender_id,$token,null);  print_r($response);


        /**
         * Upload file
         */
//        $tender_id    = '48dcb014d003454b8875975bb531147f';
//        $token        = '190341f641584b3287f862b467a0574e';
//        $document_id  = null;
//        $data         = null;
//
//        $file = [
//            'name'=>'D:\_OpenServer_\OpenServer\domains\mark.loc\tests\codeception\_data\api\testest.pdf',
//            'title'=>'Страшное кирилическое имя',
//            'format'=>'image/jpeg'
//        ];

        //$response = Yii::$app->opAPI->tendersDocuments($file,$data,$tender_id,$token,$document_id); print_r($response);


        /**
         * Update file, without upload!!!
         */
//        $tender_id    = '48dcb014d003454b8875975bb531147f';
//        $token        = '190341f641584b3287f862b467a0574e';
//        $document_id  = '0b97782fe5c24851b2e229f73b85b48c';
//        $data         = json_encode( ['data'=>[/*'documentOf'=>'tender',*/'documentType'=>'notice','title'=>'zzzzzzzzz',/*'relatedItem'=>''*/]] );
//        $file         = null;

        //$response = Yii::$app->opAPI->tendersDocuments($file,$data,$tender_id,$token,$document_id); print_r($response);



        //*/


//        $task  = null;
//        $model = new \app\models\DocumentUploadTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

//        try {
//            for ($i = 0; $i < 1; $i++) {
//                try {
//                    if ($task = $model->getNextTask()) {
//
//                        //$model->sendFileApi($task);
//
//                        $model->updateFileApi($task);
//
//                        /**
//                         * @TOTO: Update TENDERS !!!
//                         */
//                    }
//                } catch (\app\components\apiDataException $e) {
//                    throw new \Exception('apiDataException '. $e->getMessage(),$e->getCode(),$e);
//                } catch (\app\components\apiException $e) {
//                    throw new \Exception('apiException '. $e->getMessage(),$e->getCode(),$e);
//                }
//                usleep(rand(1, 100));
//            }
//        } catch (\Exception $e) {
//            if ($task) {
//                $task->status         = $model->_error_code;
//                $task->transaction_id = '';
//                $task->api_answer     = 'Exception [' . $e->getCode() . '] ' . $e->getMessage() ."\nTrace:\n". $e->getTraceAsString();
//                $task->save(false);
//            }
//        }

        //print_r($task);

        //echo $task->update(false,0);



        /**
         * @var \app\models\TenderUpdateTask $task1
         */

        //$task1 = (new \app\models\TenderUpdateTask())->getNextTask();
        //$model  = new \app\models\TenderUpdateTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);
        //$task1 = $model->getNextTask();

        //$tender = $task1->updateTenderApi();

        //print_r($task1); print_r($tender);

        //echo date('c');


//        $response = Yii::$app->opAPI->tenders(
//            null,
//            '96ebd958d93341309858414d989e8254',
//            null,
//            //date('c',strtotime('- '. Yii::$app->params['tender_update_interval'] .' minute')));
//            date('c',strtotime('- 5 days')));
//            //null);
//
//        print_r($response);

        /*
        if (count($response['body']['data'])) {
            foreach ($response['body']['data'] AS $row) {
                $tender = app\models\Tenders::find()
                    ->where(['tender_id'=>$row['id']])
                    ->andWhere(['<>','date_modified',$row['dateModified']])
                    ->one();

                if ($tender !== null) {

                    print_r($tender);


                } else {
                    //echo "-- $row[id] \n";
                }
            }
        }*/

        //$model = new app\models\TenderUpdateTask();
        //$model->getChangesApi();

        //print_r($response);

        //$file = app\models\DocumentUploadTask::addFile('aaaaaaaa','zzzzzzzzz');

        //app\models\DocumentUploadTask::removeFile('aaaaaaaa');

        //print_r($file);

        ?>
    </pre>

</div>
