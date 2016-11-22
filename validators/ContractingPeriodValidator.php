<?php
namespace app\validators;

use yii\validators\Validator;
use Yii;
use app\components\ApiHelper;

class ContractingPeriodValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = [
            0 => \Yii::t('app', '"Дата зміни не може бути менше дати останньої зміни"'),
            1 => \Yii::t('app', '"Дата зміни не може бути менше дати підписання контракту"'),
            2 => \Yii::t('app', '"Дата зміни не може бути більше поточної дати"'),
        ];
    }

    public function validateAttribute($model, $attribute)
    {
        $post = Yii::$app->request->post();
        $action = Yii::$app->controller->action->id;
        ApiHelper::FormatDate($post, true);
        if ($action == 'update') {
            $signedDate = strtotime($post['Validate']['dateSigned']);
            $lastUpdateDate = strtotime($post['Validate']['dateModified']);
            $currentChanges = strtotime($post['Changes']['dateSigned']);
            if (isset($signedDate) && isset($lastUpdateDate)) {
                if ((isset($lastUpdateDate) && $lastUpdateDate) && ($currentChanges < $lastUpdateDate)) {
                    $model->addError($attribute, $this->message[0]);
                }
                if ($currentChanges < $signedDate) {
                    $model->addError($attribute, $this->message[1]);
                }
                if ($currentChanges > strtotime('+0')) {
                    $model->addError($attribute, $this->message[2]);
                }
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $action = Yii::$app->controller->action->id;
        return <<<JS
            if ('$action' == 'update') {
                var signedDate = ComareDateFormat($('#signed-date').val());
                var lastUpdateDate = ComareDateFormat($('#last-update-date').val());
                var currentChanges = ComareDateFormat($('#changes-datesigned').val());
                if ((lastUpdateDate !== undefined && lastUpdateDate != '') && (currentChanges < lastUpdateDate)){
                    messages.push({$this->message[0]});
                }
                if (currentChanges < signedDate) {
                    messages.push({$this->message[1]});
                }
                if (currentChanges > new Date()) {
                    messages.push({$this->message[2]});
                }
            }
JS;
    }
}