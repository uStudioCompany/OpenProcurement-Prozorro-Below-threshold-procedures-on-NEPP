<?php

namespace app\modules\seller\helpers;

use app\models\Contracts;
use yii\db\Query;
use yii\helpers\Json;
use Yii;
use app\models\User;
use app\models\Countries;
use app\models\Regions;
use app\models\Persons;
use app\models\TenderUpdateTask;
use app\models\Tenders;


class BidConvertOut
{
    public static function prepareToAPI($post, $tender, $tenders)
    {
        $multi = true;

//        Yii::$app->VarDumper->dump($post, 10, true);die;

        if ($tenders->tender_type == 2) {

            foreach ($post['Bid']['lotValues'] as $k => $lotValue) {
//                Yii::$app->VarDumper->dump($tenders->tender_method, 10, true, true);
                if(in_array($tenders->tender_method,['open_competitiveDialogueUA', 'open_competitiveDialogueEU']) && $lotValue['competitive_lot'] == '1'){

                    $lotValue['value']['amount'] = '0';
                }

                if($lotValue['value']['amount'] == '') continue;




                 $tmp=[
                    'relatedLot' => $k,
                    'value' => [
                        'valueAddedTaxIncluded' => $tender->value->valueAddedTaxIncluded,
                        'amount' => $lotValue['value']['amount'],
                        'currency' => $tender->value->currency
                    ],
                ];

                //подрядчик
                if($tenders->tender_method != 'open_belowThreshold' && isset($lotValue['subcontractingDetails'])) {
                    $tmp['subcontractingDetails'] = $lotValue['subcontractingDetails'];
                }

                $data['data']['lotValues'][] = $tmp;

                //собираем для лота неценовые показатели
                if(isset($post['parameter'])){
                    foreach ($post['parameter'] as $key=>$param) {
                        if($param['related_id']==$k){
                            $data['data']['parameters'][$key]['code']= $param['code'];
                            $data['data']['parameters'][$key]['value']= $param['value']/100;
                        }
                    }

                }


            }
//            $data['data']['parameters'] = array_values( (array)$data['data']['parameters'] );

            //собираем неценовые тендера
            if(isset($post['parameter'])){
                foreach ($post['parameter'] as $key=>$param) {
                    if($param['related_id']==''){
                        $data['data']['parameters'][$key]['code']= $param['code'];
                        $data['data']['parameters'][$key]['value']= $param['value']/100;
                    }
                }
            }




        } else {
            $data['data']['value'] = [
                'amount' => isset($post['Bid']['value']['amount']) ? $post['Bid']['value']['amount'] : 0,
                'valueAddedTaxIncluded' => $tender->value->valueAddedTaxIncluded,
                'currency' => $tender->value->currency,
            ];


        }

        //подтверждение квалификации


        $data['data']['tenderers'][0] = self::getSellerProcuringEntity();

        //собираем неценовые тендера
        if(isset($post['parameter'])){
            foreach ($post['parameter'] as $key=>$param) {
                if($param['related_id']==''){
                    $data['data']['parameters'][$key]['code']= $param['code'];
                    $data['data']['parameters'][$key]['value']= $param['value']/100;
                }
            }
        }

//        Yii::$app->VarDumper->dump($data, 10, true);        die;
//        ApiHelper::FormatDate($data,true);
        return $data;
    }

    /**
     * @param $post
     */
    public static function prepareToAPICancel($post)
    {
        $lot_ids = [];
        $items_ids = [];

        $data['data'] = $post['Tender'];

        unset ($data['data']['documents']);

    }

    /**
     * Функция для конвертации данных из ЦБД для формы редактирования тендера.
     *
     * @param string $token
     * @return array
     */
    public static function ConvertDataFromCBDToPost($token)
    {
        $query = (new Query())->select('response')->from('tenders')->where(['token' => $token])->all();
//        VarDumper::dump($query[0]['response'], 10, true);
        $res = json_decode($query[0]['response']);
        unset($res['access']);

        return $res;
    }

    /**
     * @param $contactPoint
     * @return array
     */
    public static function getProcuringEntity($contactPoint, $tenderMethodType)
    {

        $out = [
            'name' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id, 'name'),
            'address' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id),
            'contactPoint' => self::getContactsForTender($contactPoint, $tenderMethodType),
            'identifier' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id, 'id'),
            'kind' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id, 'kind')
        ];
        return ($out);
    }

    /**
     * @param $contactPoint
     * @return array
     */
    public static function getSellerProcuringEntity($tenderMethodType = 'belowThreshold')
    {
//        $data['fio'] = Yii::$app->user->id;
        $data['fio'] = Persons::findOne(['company_id'=>Yii::$app->user->identity->company_id])->id; // пока берем первое контактное лицо в компании пользователя

        $out = [
            'name' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id, 'name'),
            'address' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id),
            'contactPoint' => self::getContactsForTender($data, $tenderMethodType),
            'identifier' => self::getCompanyByUserIdForSimplyTender(Yii::$app->user->id, 'id'),
        ];
        return $out;
    }

    /**
     * Формирует часть массива для передачи в ЦБД.
     *
     * @param $uid
     * @param string $param
     * @return array
     */
    public static function getCompanyByUserIdForSimplyTender($uid, $param = 'all')
    {
//        предполагаем, что пользователь связан с одной компанией
        $res = User::find()
            ->where(['id' => $uid])
            ->with('company')
//            ->asArray()
            ->all();

        $data = [];

        if ($param == 'all') {
            foreach ($res[0]['company'] as $k => $v) {
                if ($v != '') {
                    if ($k == 'registrationCountryName') {
                        $data['countryName'] = Countries::findOne($v)->name;
                    } elseif ($k == 'region') {
                        $data['region'] = Regions::findOne($v)->name;
                    } elseif ($k == 'locality' || $k == 'postalCode' || $k == 'streetAddress') {
                        $data[$k] = $v;
                    }
                }
            }

            return $data;

        } elseif ($param == 'name') {

            return $res[0]['company']['legalName'];

        } elseif ($param == 'id') {

            $data['id'] = $res[0]['company']['identifier'];
            $data['scheme'] = $res[0]->company->countryNameSheme->name;

            return $data;
        } elseif ($param == 'name_en') {

            return $res[0]['company']['legalName_en'];
        } elseif ($param == 'kind') {

            return $res[0]->company->companyCustomerType->id;
        }
    }

    /**
     * Формирует часть массива для передачи в ЦБД.
     *
     * @param $data
     * @param $tenderMethodType
     * @return array
     */


    public static function getContactsForTender($data, $tenderMethodType)
    {

        if ($data['fio'] != '') {

            $res = Persons::findOne($data['fio']);
//            Yii::$app->VarDumper->dump($res, 10, true);die;
            $data['email'] = $res['email'];
            $data['telephone'] = $res['telephone'];
            $data['faxNumber'] = $res['telephone'];

            if(isset($data['url']) && $data['url'] != ''){
                $data['url'] = $res['url'];
            }

            $data['name'] = $res['userSurname'] . ' ' . $res['userName'] . ' ' . $res['userPatronymic'];

            unset($data['fio']);



        } else {
            unset($data['fio']);
        }
        return $data;
    }

    /**
     * Формирует часть массива для передачи в ЦБД.
     *
     * @param array $array
     * @param $needle
     */
//    public static function FormatDate(array &$array, $needle)
//    {
//        foreach ($array as $key => &$value) {
//            if (is_array($value)) {
//                self::FormatDate($value, $needle);
//            } else if ($key === $needle) {
//                if (trim($value) != '') {
//                    if(preg_match('/^\d{2}\/\d{2}\/\d{4}$/i',trim($value))){
//                        $value = str_replace('/', '.', $value);
//                        $value = Yii::$app->formatter->asDatetime(strtotime($value), "php:c");
//                    }
//
//                } else {
////                    var_dump($key);die;
//                    unset($array[$key]);
//                }
//            }
//        }
//    }

    /**
     * @param $post array
     * @param $tender \app\models\tenderModels\Tender
     * @return bool
     */
    public static function prepareToValidate(&$post, &$tender)
    {

        unset($post['Tender']['items']['__EMPTY_ITEM__']);
        unset($post['Tender']['lots']['__EMPTY_LOT__']);

        if (isset($post['Tender']['documents']['__EMPTY_DOC__'])) {
            unset($post['Tender']['documents']['__EMPTY_DOC__']);
        }
        if (isset($post['Tender']['features']['__EMPTY_FEATURE__'])) {
            unset($post['Tender']['features']['__EMPTY_FEATURE__']);
        }
        if (isset($post['Tender']['cancellations']['__EMPTY_CANCEL__'])) {
            unset($post['Tender']['cancellations']['__EMPTY_CANCEL__']);
        }

        if (is_array($post['Tender']['items'])) {
            $post['Tender']['items'] = array_values($post['Tender']['items']);
        }
        if (is_array($post['Tender']['lots'])) {
            $post['Tender']['lots'] = array_values($post['Tender']['lots']);
        }
        if (is_array($post['Tender']['documents'])) {
            $post['Tender']['documents'] = array_values($post['Tender']['documents']);
        }
        if (is_array($post['Tender']['features'])) {
            $post['Tender']['features'] = array_values($post['Tender']['features']);
        }

        $tender = HTender::load($post);

//        if (!$status = $tender->load($post, 'Tender')) {
//            Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, load data'));
//            return $status;
//        }

//        unset($tender->items['__EMPTY_ITEM__']);
//        unset($tender->lots['__EMPTY_LOT__']);
//        unset($tender->features['__EMPTY_FEATURE__']);

        if (!count($post['Tender']['features'])) {
            //unset($tender->features);
            $tender->features = null;
        }

        foreach ($tender->items as $item) {
//            Yii::$app->VarDumper->dump($item->additionalClassifications[0]->dkType, 10, true);die;
            if ($item->additionalClassifications[0]->dkType == '000') {
                $item->additionalClassifications[0] = null;
            }
        }


        $tender->documents = null;
        $tender->cancellations = null;

        if (isset($post['tender_type']) && $post['tender_type'] == 1) {
            $tender->lots = null;
        }


        switch (true) {

            case ($post['procurementMethod']['method'] === 'open' && $post['procurementMethod']['method_type'] === 'belowThreshold'):

                /**
                 * Обычная процедура
                 */
                $tender->guarantee = null;
                break;





        }

        $tender->procuringEntity->address = null;

            //все процедуры, кроме европейской
            $tender->procuringEntity->additionalContactPoints = null;

//        $tender->validate();
//        Yii::$app->VarDumper->dump($tender, 10, true);die;
        if (!$status = $tender->validate()) {
            //echo '1<pre>'; print_r($tender); die;
            Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, check data'));
        }
        //echo '2<pre>'; print_r($tender); die;

        return $status;
    }

    public static function prepareToValidateCancel(&$post, $cancel)
    {

        if (isset($post['Tender']['cancellations']['documents']['__EMPTY_DOC__'])) {
            unset($post['Tender']['cancellations']['documents']['__EMPTY_DOC__']);
        }

        if (!$status = $cancel->load($post['Tender'], 'cancellations')) {
            Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, load data'));
            return $status;
        }
        //echo '<pre>LOAD<BR>'; print_r($cancel);

        if (!$status = $cancel->validate()) {
            //Yii::$app->session->setFlash('message_error', Yii::t('app', 'Error, check data')); }
            echo '<pre>NOT VALID ---------------<BR>';
            print_r($cancel);
        }

        return $status;
    }

    public static function sendCancellations($tid, $tenderId, $token, $post, $response = null)
    {
        if (!isset($post['Tender']['cancellations'])) return false;

        $errors = 0;
        $lot_ids = [];
        $cancellations_ids = [];
        $cancellations_list = [];

        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['cancellations'])) {
                foreach ($response['data']['cancellations'] AS $c) {
                    $cancellations_ids[] = $c['id'];
                    $cancellations_list[$c['id']] = $c;
                }
            }
        }

        if (isset($response['data']['lots'])) {
            foreach ($response['data']['lots'] AS $lot) {
                $lot_ids[] = $lot['id'];
            }
        }

        if (count($post['Tender']['cancellations'])) {
            $cancellation = $post['Tender']['cancellations'];

            if (isset($cancellation['documents']) && count($cancellation['documents']) >= 1) {
                $cancellation['status'] = 'pending';
            } else {
                $cancellation['status'] = 'active';
            }

            unset($cancellation['documents']);

            $cancellation['date'] = ApiHelper::convertDate($cancellation['date'], true);

            if (isset($cancellation['id']) && $cancellation['id']) {

                if (!in_array($cancellation['id'], $cancellations_ids)) {
                    //throw new \Exception('The requested document id does not exist: '.$document['id']); }
                    $errors++;
                    return false; /*continue;*/
                }

                if ($cancellations_list[$cancellation['id']]['status'] == 'active') {
                    return false; /*continue;*/
                }  // Уже активна

                if ($cancellation['status'] == $cancellations_list[$cancellation['id']]['status']) {
                    return $cancellation['id']; /*continue;*/
                } // Ничего не изменилось, пропускаем ...
            } else {
                $cancellation['id'] = null;
            }

            //echo '<pre>'; print_r($lot_ids); die();

            if (in_array($cancellation['relatedLot'], $lot_ids)) {
                $cancellation['cancellationOf'] = 'lot';
            } else {
                $cancellation['cancellationOf'] = 'tender';
                $cancellation['relatedLot'] = null;
            }

//            if(isset($post['Tender']['tender_method']) && strpos('above',$post['Tender']['tender_method']) == false){
//                unset($cancellation['reasonType']);
//            }

//            Yii::$app->VarDumper->dump($cancellation, 10, true);die;

            //-----------
            $cancel_response = Yii::$app->opAPI->cancellations(Json::encode(['data' => $cancellation]), $tenderId, $token, $cancellation['id']);

            if (isset($cancel_response['body']['data']['id'])) {
                // OK
                TenderUpdateTask::addTask($tid, $tenderId);
                return $cancel_response['body']['data']['id'];
            } else if (empty($cancel_response['raw']) || $cancel_response['raw'] === null || $cancel_response['raw'] === 'null') {
                return $cancellation['id'];
            } else {
                //throw new \Exception('The requested document id does not exist: '.$document['id']); }
                $errors++;
                return false; /* continue; */
            }
        }
        return false;
    }

    public static function sendCancellations_BEC($tid, $tenderId, $token, $post, $response = null)
    {
        if (!isset($post['Tender']['cancellations'])) return;

        //echo '<pre>'; print_r($post['Tender']['cancellations']); die();

        $errors = 0;
        $lot_ids = [];
        $cancellations_ids = [];
        $cancellations_list = [];

        if ($response) {
            $response = json_decode($response, 1);
            if (isset($response['data']['cancellations'])) {
                foreach ($response['data']['cancellations'] AS $c) {
                    $cancellations_ids[] = $c['id'];
                    //$c['date'] = ApiHelper::convertDate($c['date']);
                    $cancellations_list[$c['id']] = $c;
                }
            }
        }

        if (isset($post['Tender']['lots'])) {
            foreach ($post['Tender']['lots'] AS $lot) {
                $lot_ids[] = $lot['id'];
            }
        }

        if (count($post['Tender']['cancellations'])) {
            foreach ($post['Tender']['cancellations'] AS $cancellation) {

                $cancellation['date'] = ApiHelper::convertDate($cancellation['date'], true);

                if (isset($cancellation['id']) && $cancellation['id']) {

                    if (!in_array($cancellation['id'], $cancellations_ids)) {
                        //echo '<pre>'; echo $document['id'] ."\n\n"; print_r($response);  print_r($documents_list); print_r($documents_ids); die();
                        //throw new \Exception('The requested document id does not exist: '.$document['id']); }
                        $errors++;
                        continue;
                    }

                    if ($cancellations_list[$cancellation['id']]['status'] == 'active') continue; // Уже активна

                    if ($cancellation['reason'] == $cancellations_list[$cancellation['id']]['reason'] &&
                        $cancellation['date'] == $cancellations_list[$cancellation['id']]['date'] &&
                        $cancellation['status'] == $cancellations_list[$cancellation['id']]['status']
                    ) continue; // Ничего не изменилось, пропускаем ...
                } else {
                    //unset($cancellation['id']);
                    $cancellation['id'] = null;
                }

                if ($cancellation['cancellationOf'] == 'lot' && in_array($cancellation['relatedLot'], $lot_ids)) {
                    //
                } else {
                    $cancellation['cancellationOf'] = 'tender';
                    $cancellation['relatedLot'] = '';
                }

                //echo '<pre>'; print_r($cancellation); die();

                $cancel_response = Yii::$app->opAPI->cancellations(Json::encode(['data' => $cancellation]), $tenderId, $token, $cancellation['id']);

                if (isset($cancel_response['body']['data']['id'])) {
                    // OK
                } else {
                    echo '<pre>';
                    echo $cancellation . "\n\n";
                    print_r($cancel_response);
                    print_r($cancellations_list);
                    print_r($cancellations_ids);
                    die();
                    //throw new \Exception('The requested document id does not exist: '.$document['id']); }
                    $errors++;
                    continue;
                }
            }
            TenderUpdateTask::addTask($tid, $tenderId);
        }
    }

    public static function prepearLimitedAvards($id, $post)
    {
        unset($post['_csrf']);
        $tenderData = Json::decode(Tenders::findOne($id)->json);

        $data['data'] = $post['Award'];
        $data['data']['status'] = 'pending';
        $data['data']['date'] = date('c', strtotime('now'));

        $tax = (bool)$tenderData['Tender']['value']['valueAddedTaxIncluded'];
        $data['data']['value']['valueAddedTaxIncluded'] = $tax;

        return $data;

    }

}
