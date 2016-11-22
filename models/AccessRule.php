<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

class AccessRule
{
    public $tenders;

    public static function checkAccess($action)
    {
        $tenderID = Yii::$app->request->get('id');
        $actionID = str_replace('-', '', $action->id);
        $methods = get_class_methods(AccessRule::class);
        $accessRule = new AccessRule();
        if (isset($tenderID)) {
            $accessRule->tenders = Tenders::findOne($tenderID);
        }

        $commonCheck = Companies::checkAllowedCompanyStatusToWork(Yii::$app->user->identity->company_id);
        if (in_array($actionID, $methods)) {
            $check = $accessRule->$actionID();
        } else {
            $check = false;
        }
        $check = $check && $commonCheck;
        return $check;
    }

    public function view()
    {
        if (Companies::checkCompanyIsTenderOwner($this->tenders->id, $this->tenders)) {
            return true;
        } elseif ($this->tenders->status != 'draft') {
            return true;
        }
        return false;
    }

    public function update()
    {
        $check = Companies::checkCompanyIsTenderOwner($this->tenders->id, $this->tenders) &&
            in_array($this->tenders->status, Yii::$app->params['allowed.tender.update.status']);
        return $check;
    }

    public function cancel()
    {
        $check = Companies::checkCompanyIsTenderOwner($this->tenders->id, $this->tenders) &&
            in_array($this->tenders->status, Yii::$app->params['allowed.tender.cancelation.status']);
        return $check;
    }

    public function limitedavards()
    {
        $checkTenderMethod = strripos($this->tenders->tender_method, 'limited');
        $check = true && !in_array($this->tenders->status, ['draft']);
        if ($checkTenderMethod === false) {
            $check = false;
        }
        return $check;
    }


    public function questions()
    {
        $checkTenderMethod = strripos($this->tenders->tender_method, 'limited');
        $check = true;
        if ($checkTenderMethod !== false || in_array($this->tenders->status, ['draft'])) {
            $check = false;
        }
        return $check;
    }

    public function addtender()
    {
        return true;
    }

    public function award()
    {
        $checkTenderMethod = strripos($this->tenders->tender_method, 'limited');
        if ($checkTenderMethod !== false && !in_array($this->tenders->status, ['draft'])) {
            return true;
        }
        if (in_array($this->tenders->status, ['active.qualification', 'active.awarded', 'complete'])) {
            return true;
        }
        return false;
    }

    public function complaints()
    {
        if (!in_array($this->tenders->status, ['draft'])) {
            return true;
        }
        return false;
    }

    public function complaintscreate()
    {
        $tenderID = Yii::$app->request->get('tid');
        $targetID = Yii::$app->request->get('target_id');
        if (isset($tenderID)) {
            $tenders = Tenders::findOne($tenderID);
            if ($targetID == '') {
                if ($tenders->status == 'active.tendering') {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public function award_form()
    {
        if (in_array($this->tenders->status, ['active.qualification', 'active.awarded', 'active.pre-qualification', 'active.pre-qualification.stand-still'])) {
            return true;
        }
        return false;
    }

    public function contract()
    {
        //добавить чтобы только владелец мог это делать
        if (!in_array($this->tenders->status, array_merge(Yii::$app->params['actual.status.tender'], Yii::$app->params['archive.status.tender']))) {
            return true;
        }
        return false;
    }

    public function create()
    {
        return true;
    }

    public function euprequalification()
    {
        if (!in_array($this->tenders->status, Yii::$app->params['allowed.tender.euprocedure.status'])) {
            return false;
        }

        return false;
    }

    public function filedelete()
    {
        return true;
    }

    public function fileupload()
    {
        return true;
    }

    public function json()
    {
        return true;
    }

    public function planecp()
    {
        return true;
    }

    public function prequalificationcomplaints()
    {
        //тут добавил open_competitiveDialogueUA, потому что кажется, что он использует тоже этот экшн перед вторым этапом
        if (!in_array($this->tenders->status, ['draft']) && in_array($this->tenders->tender_method, ['open_competitiveDialogueEU', 'open_competitiveDialogueUA', 'open_aboveThresholdEU', 'selective_competitiveDialogueEU.stage2'])) {
            return true;
        }
        return false;
    }

    public function qualificationcomplaints()
    {
        return true;
//        $contracts = Json::decode($this->tenders->response)['data']['contracts'];
//        $qualification = $targetID = Yii::$app->request->get('qualification');
//        foreach ($contracts as $contract) {
//            if ($qualification == $contract['awardID']) {
//                return true;
//            }
//        }
//        return false;
    }

    public function setawardstatus()
    {
        if (in_array($this->tenders->status, Yii::$app->params['allowed.tender.awards.status']) && Companies::checkCompanyIsTenderOwner($this->tenders->id, $this->tenders)) {
            return true;
        }
        return false;
    }

    public function setcontractingecp()
    {
        return true;
    }

    public function setcontractsign()
    {
        $post = Yii::$app->request->post();
        if (Companies::checkCompanyIsTenderOwner($post['tender_id'], $this->tenders)) {
            return true;
        }
        return false;
    }

    public function setqualificationstatus()
    {
        $post = Yii::$app->request->post();
        if (Companies::checkCompanyIsTenderOwner($post['tenderId'], $this->tenders)) {
            return true;
        }
        return false;
    }

    public function tenderecp()
    {
        $post = Yii::$app->request->post();
        if (Companies::checkCompanyIsTenderOwner($post['tender_id'])) {
            return true;
        }
        return false;
    }

    public function bidfinancialviability()
    {
        return true;
    }

    public function updatebid()
    {
        return true;
    }

    public function addmoney()
    {
        return true;
    }

    public function cancelprequalificationcomplaints()
    {
        return true;
    }
}