<?php

namespace app\modules\buyer\controllers;

use app\models\Plans;
use app\models\PlansSearch;
use app\models\User;
use app\models\Companies;
use yii\base\ErrorException;
use yii\web\Controller;
use Yii;
use app\components\ApiHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use app\models\planModels\Plan;

/**
 * Class TenderController
 * @package app\controllers
 */
class TestController extends Controller
{



    /**
     * Lists all Invite models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tCount = Yii::$app->params['docTaskCount'];
        $task = null;
        $model = new \app\models\TenderUpdateTask();
        //$model->_transaction_id = time() .' '. Yii::$app->security->generateRandomString(5);

        try {
            for ($i = 0; $i < $tCount; $i++) {
                try {
                    if ($task = $model->getNextTask()) {
                        $model->updateTenderApi($task);
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


}