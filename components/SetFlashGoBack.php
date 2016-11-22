<?php
namespace app\components;

use Yii;
use app\models\Companies;
use app\models\Invoice;
use yii\base\Behavior;

class SetFlashGoBack extends Behavior{

    public function setFlashGoBack($message, $messageType = 'error', $uri = false){
        Yii::$app->session->setFlash($messageType, Yii::t('app', $message));
        if ($uri){
            return $this->owner->redirect([$uri]);
        }
        if(Yii::$app->user->returnUrl != '/') {
            return $this->owner->goBack();
        }
        else {
            return Yii::$app->request->referrer ? $this->owner->redirect(Yii::$app->request->referrer) : $this->owner->goHome();
        }
    }

}



?>