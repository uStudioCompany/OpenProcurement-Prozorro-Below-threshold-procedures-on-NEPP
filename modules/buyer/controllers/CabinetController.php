<?php

namespace app\modules\buyer\controllers;

use app\models\Companies;
use app\models\CompanyChangesHistory;
use app\models\Contracts;
use app\models\ContractsTemplates;
use app\models\Notifications;
use Yii;
use app\models\Persons;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * CabinetController implements the CRUD actions for Persons model.
 */
class CabinetController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('Forbidden', Yii::t('app', "Для доступа к странице, нужно сначала авторизироваться"));
                    $this->goBack();
                },
//                'only' => ['logout','index'],
                'rules' => [
                    [
//                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Persons models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionContract()
    {
        $companyCurrentData = Companies::findOne(Yii::$app->user->identity->company_id);
//        Yii::$app->VarDumper->dump($companyCurrentData, 10, true);die;
        $contract = Contracts::findOne(['company_id' => Yii::$app->user->identity->company_id]);

        // если нет контракта, то создаем его и дату создания
        if (!$contract) {
            $template = ContractsTemplates::find()->orderBy('create_at DESC')->one();
            $contract = new Contracts();
            $contract->contract_num = $companyCurrentData->identifier . date('dmYHis');
            $contract->template_id = $template->id;
            $contract->save(false);

            $dateFrom = $contract->create_at;
        } else {
            $dateFrom = $contract->create_at;
            $template = ContractsTemplates::find()->where(['<=', 'create_at', $contract->create_at])->orderBy('create_at DESC')->one();
        }

        $contractData = Companies::getCompanyContractData($companyCurrentData,$dateFrom);
        ob_end_clean();
//        $html = '';
        $html = sprintf(
//            file_get_contents('../views/cabinet/contract.php'),
            $template->text,
            $contract->contract_num,
            Yii::$app->formatter->asDateTime($dateFrom, 'php:d'),
            Yii::$app->formatter->asDateTime($dateFrom, 'php:F'),
            Yii::$app->formatter->asDateTime($dateFrom, 'php:Y'),
            $contractData['legalName'],
            $contractData['userPosition'],
            $contractData['fio'],
            $contractData['userDirectionDoc'],

            $contractData['identifier'],
            $contractData['userPosition'],
            $contractData['fio'],
            $contractData['userDirectionDoc'],
            $contractData['locality'],
            $contractData['streetAddress'],
            $contractData['postalCode'],

            $contractData['identifier'],
            $contractData['userPosition'],
            $contractData['fio'],
            $contractData['userDirectionDoc'],
            $contractData['locality'],
            $contractData['streetAddress'],
            $contractData['postalCode']

        );

        //выбираем изменения

        $changes = CompanyChangesHistory::find()
            ->where(['company_id' => Yii::$app->user->identity->company_id])
            ->where(['>=', 'create_at', $dateFrom])
            ->orderBy('create_at')->all();

        $templateChanges = ContractsTemplates::find()
            ->where(['>=', 'create_at', $dateFrom])
            ->all();

//        Yii::$app->VarDumper->dump($changes, 10, true);        die;

        $changes = array_merge($changes, $templateChanges);
        ArrayHelper::multisort($changes, ['create_at'], [SORT_ASC]);

        if (count($changes)) {
            foreach ($changes as $change) {
                if ($change->formName() == 'ContractsTemplates') {

                    $html .= $this->renderPartial('extra_new_contract_template', [
                        'changes' => $change->description,
                        'contractNum' => $contract->contract_num,
                        'contractDate' => $dateFrom,
                        'legalName' => $companyCurrentData->legalName,
                        'userPosition' => $companyCurrentData->userPosition,
                        'fio' => $companyCurrentData->fio,
                        'userDirectionDoc' => $companyCurrentData->userDirectionDoc,
                        'create_at'=>$contract->create_at,
                        'identifier'=>$companyCurrentData->identifier,
                        'countryName'=>$companyCurrentData->countryName,
                        'locality'=>$companyCurrentData->locality,
                        'streetAddress'=>$companyCurrentData->streetAddress,
                        'postalCode'=>$companyCurrentData->postalCode,
                    ]);

                } elseif ($change->formName() == 'CompanyChangesHistory') {

                    $html .= $this->renderPartial('extra_contact_changes', [
                        'changes' => Json::decode($change->changes),
                        'contractNum' => $contract->contract_num,
                        'contractDate' => $dateFrom,
                        'legalName' => $companyCurrentData->legalName,
                        'userPosition' => $companyCurrentData->userPosition,
                        'fio' => $companyCurrentData->fio,
                        'userDirectionDoc' => $companyCurrentData->userDirectionDoc,
                        'create_at'=>$change->create_at,
                        'identifier'=>$companyCurrentData->identifier,
                        'countryName'=>$companyCurrentData->countryName,
                        'locality'=>$companyCurrentData->locality,
                        'streetAddress'=>$companyCurrentData->streetAddress,
                        'postalCode'=>$companyCurrentData->postalCode,
                    ]);

                }
            }
        }
        $this->layout = 'contract';
        $html = $this->renderContent($html);

        $options = new Options();
        $options->setFontDir('../fonts');
        $dompdf = new DOMPDF($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("contract", ["Attachment" => 0]);
    }


}
