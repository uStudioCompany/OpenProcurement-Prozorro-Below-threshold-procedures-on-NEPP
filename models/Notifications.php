<?php

namespace app\models;

use app\components\HTender;
use app\components\SimpleTenderConvertIn;
use app\modules\buyer\controllers\TenderController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use yii\base\Model;
use yii\base\View;
use yii\bootstrap\Html;
use yii\db\Query;
use yii\helpers\Json;


class Notifications extends Model
{
    /** Выбирает способ отправки сообщений: стандартный или через rabbitMQ
     *
     * стандартный - выполнение задачи (отправка письма) по ходу выполнения кода
     * через rabbitMQ - отправка задачи на выполнение другому серверу
     *
     * @param $to
     * @param $subject
     * @param $body
     */
    public static function sendEmail($to, $subject, $body)
    {
        if (Yii::$app->params['isOn.rabbitMQ']) {
            self::mailRabbit($to, $subject, $body);
        } else {
            Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['mail_sender_full'])
                ->setTo($to)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();
        }
    }

    /** Отправляет задачу(отправку письма) для ее выполнения на другой сервер
     *
     * @param $to
     * @param $subject
     * @param $msg
     * @param string $queueName
     */
    public static function mailRabbit($to, $subject, $msg, $queueName = 'MailQueue')
    {
        $configRMQ = Yii::$app->params['config.rabbitMQ'];
        $connection = new AMQPStreamConnection($configRMQ['host'], $configRMQ['port'], $configRMQ['login'], $configRMQ['password']);
        $channel = $connection->channel();
        $channel->queue_declare($queueName, false, false, false, false);
        $info['to'] = $to;
        $info['subject'] = $subject;
        $info['body'] = $msg;
//        for ($i = 0; $i < 100; $i++) {
            $ret = new AMQPMessage(Json::encode($info));
            $channel->basic_publish($ret, '', $queueName);
//        }
        $channel->close();
        $connection->close();
    }

    public static function needSend($data)
    {


        if (isset($data['data']['complaints']) && count($data['data']['complaints'])) {
            return true;
        } else if (isset($data['data']['qualifications']) && count($data['data']['qualifications'])) {
            foreach ($data['data']['qualifications'] as $q => $qualification) {
                if (isset($qualification['complaints']) && count($qualification['complaints'])) {
                    return true;
                }
            }
        } else if (isset($data['data']['awards']) && count($data['data']['awards'])) {
            foreach ($data['data']['awards'] as $a => $award) {
                if (isset($award['complaints']) && count($award['complaints'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function SendOneDayNotification($buyer, $data, $tenders)
    {

//                если письмо отправлялось больше, чем сутки назад
        if ($tenders->mail_send_at == NULL || $tenders->mail_send_at < strtotime('-1 day')) {


            if (isset($buyer->username) && $buyer->username) {
                $ownerEmail = $buyer->username;

                if (self::needSend($data)) {

                    $isSend = Yii::$app->mailer->compose('_new_questions', [
                        'tender' => $data['data']
                    ])
                        ->setFrom(Yii::$app->params['mail_sender_full'])
                        ->setTo($ownerEmail)
                        ->setSubject('Информация по тендеру ' . $data['data']['title'])
                        ->send();

                    if ($isSend) {
                        $tenders->mail_send_at = strtotime('now');
                        $tenders->save(false);
                    }
                }

            }
        }


    }

    public static function SendCancelTender($sellers, $oldData, $data)
    {
        if ($sellers) {

            if (isset($data['data']['lots']) && count($data['data']['lots'])) { // мультилот

            } else { // простой

                $tenderOldStatus = $oldData['data']['status'];
                $tenderCurrentStatus = $data['data']['status'];

                if ($tenderCurrentStatus == 'cancelled' && $tenderOldStatus != $tenderCurrentStatus) {
                    foreach ($sellers as $s => $seller) {

                        $isSend = Yii::$app->mailer->compose('tender_cancelled', [
                            'tender' => $data['data']
                        ])
                            ->setFrom(Yii::$app->params['mail_sender_full'])
                            ->setTo($seller->user->username)
                            ->setSubject('Информация по тендеру ' . $data['data']['title'])
                            ->send();
                        if ($isSend) {
//                            echo $s;
                        }

                    }
                }
            }


        }
    }

    public static function SendPrequalificationResult($sellers, $oldData, $data)
    {
        if ($sellers) {
            $tenderOldStatus = $oldData['data']['status'];
            $tenderCurrentStatus = $data['data']['status'];
            if ($tenderCurrentStatus == 'active.pre-qualification.stand-still' && $tenderOldStatus != $tenderCurrentStatus) {
                foreach ($sellers as $s => $seller) {
                    foreach ($data['data']['qualifications'] as $q => $qualification) {
                        if ($qualification['status'] == 'cancelled') continue;

                        if ($qualification['status'] == 'active') {

                            $isSend = Yii::$app->mailer->compose('prequalification/prequalification_active', [
                                'tender' => $data['data'],
                                'qualification' => $qualification
                            ])
                                ->setFrom(Yii::$app->params['mail_sender_full'])
                                ->setTo($seller->user->username)
                                ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                ->send();

                        } elseif ($qualification['status'] == 'unsuccessful') {

                            $isSend = Yii::$app->mailer->compose('prequalification/prequalification_unsuccessful', [
                                'tender' => $data['data'],
                                'qualification' => $qualification
                            ])
                                ->setFrom(Yii::$app->params['mail_sender_full'])
                                ->setTo($seller->user->username)
                                ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                ->send();
                        }
                    }
                }
            }
        }
    }

    public static function SendQualificationResult($sellers, $oldData, $data)
    {
        if ($sellers) {

            foreach ($sellers as $s => $seller) {
                if (isset($data['data']['awards']) && count($data['data']['awards'])) {
                    foreach ($data['data']['awards'] as $a => $award) {
                        if (self::CheckOldAward($oldData, $award['id'], 'check')) {
                            $oldAwardStatus = self::CheckOldAward($oldData, $award['id'], 'status');
                            if (in_array($award['status'], ['active', 'unsuccessful', 'cancelled']) && $oldAwardStatus != $award['status']) {

                                $isSend = Yii::$app->mailer->compose('qualification/qualification_result', [
                                    'tender' => $data['data'],
                                    'award' => $award
                                ])
                                    ->setFrom(Yii::$app->params['mail_sender_full'])
                                    ->setTo($seller->user->username)
                                    ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                    ->send();


                            }
                        } else {
                            if (in_array($award['status'], ['active', 'unsuccessful', 'cancelled'])) {

                                $isSend = Yii::$app->mailer->compose('qualification/qualification_result', [
                                    'tender' => $data['data'],
                                    'award' => $award
                                ])
                                    ->setFrom(Yii::$app->params['mail_sender_full'])
                                    ->setTo($seller->user->username)
                                    ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                    ->send();


                            }
                        }
                    }
                }
            }
        }
    }

    public static function CheckOldAward($data, $awardId, $type)
    {
        if ($type == 'check') {
            foreach ($data['data']['awards'] as $a => $award) {
                if ($award['id'] == $awardId) {
                    return true;
                }
            }
            return false;
        }

        if ($type == 'status') {
            foreach ($data['data']['awards'] as $a => $award) {
                if ($award['id'] == $awardId) {
                    return $award['status'];
                }
            }
        }
    }

    public static function SendOrganResult($sellers, $buyer, $oldData, $data, $tid)
    {
        $complaintUsers = Complaints::find()->with('user')->where(['tid' => $tid])->all();

        if ($complaintUsers) {

            foreach ($complaintUsers as $u => $user) {

                // жалобы на этапе обсуждения
                if (isset($data['data']['complaints']) && count($data['data']['complaints'])) {

                    foreach ($data['data']['complaints'] as $c => $complaint) {
                        $buyerMail = $c == 0 ? $buyer->username : '';
                        if (in_array($complaint['status'], ['accepted', 'satisfied', 'declined', 'invalid', 'mistaken'])) {

                            if (self::CheckOldComplaint($oldData, $complaint['id'], 'check', 'complaints')) {// проверяем была ли жалоба раньше
                                $oldComplaintStatus = self::CheckOldComplaint($oldData, $complaint['id'], 'status', 'complaints');
                                if ($oldComplaintStatus != $complaint['status']) {
                                    $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                        'tender' => $data['data'],
                                        'complaint' => $complaint
                                    ])
                                        ->setFrom(Yii::$app->params['mail_sender_full'])
                                        ->setTo([$user->user->username, $buyerMail])
                                        ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                        ->send();
                                    if ($isSend) {
                                        echo 5;
                                    }
                                }
                            } else {
                                $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                    'tender' => $data['data'],
                                    'complaint' => $complaint
                                ])
                                    ->setFrom(Yii::$app->params['mail_sender_full'])
                                    ->setTo([$user->user->username, $buyerMail])
                                    ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                    ->send();
                                if ($isSend) {
                                    echo 6;
                                }
                            }

                        }
                    }
                }


                // жалобы на этапе преквалификации

                if (isset($data['data']['qualifications']) && count($data['data']['qualifications'])) {
                    foreach ($data['data']['qualifications'] as $q => $qualification) {

                        if (in_array($qualification['status'], ['cancelled'])) continue;

                        if (isset($qualification['complaints']) && count($qualification['complaints'])) {

                            foreach ($qualification['complaints'] as $c => $complaint) {
                                $buyerMail = $c == 0 ? $buyer->username : '';
                                if (in_array($complaint['status'], ['accepted', 'satisfied', 'declined', 'invalid', 'mistaken'])) {

                                    if (self::CheckOldComplaint($oldData, $complaint['id'], 'check', 'prequalification')) {// проверяем была ли жалоба раньше
                                        $oldComplaintStatus = self::CheckOldComplaint($oldData, $complaint['id'], 'status', 'prequalification');
                                        if ($oldComplaintStatus != $complaint['status']) {
                                            $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                                'tender' => $data['data'],
                                                'complaint' => $complaint
                                            ])
                                                ->setFrom(Yii::$app->params['mail_sender_full'])
                                                ->setTo([$user->user->username, $buyerMail])
                                                ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                                ->send();
                                            if ($isSend) {

                                            }
                                        }
                                    } else {
                                        $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                            'tender' => $data['data'],
                                            'complaint' => $complaint
                                        ])
                                            ->setFrom(Yii::$app->params['mail_sender_full'])
                                            ->setTo([$user->user->username, $buyerMail])
                                            ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                            ->send();
                                        if ($isSend) {

                                        }
                                    }

                                }
                            }
                        }
                    }
                }


                // жалобы на этапе авардов

                if (isset($data['data']['awards']) && count($data['data']['awards'])) {
                    foreach ($data['data']['awards'] as $a => $award) {

                        if (in_array($award['status'], ['cancelled'])) continue;

                        if (isset($award['complaints']) && count($award['complaints'])) {

                            foreach ($award['complaints'] as $c => $complaint) {
                                $buyerMail = $c == 0 ? $buyer->username : '';
                                if (in_array($complaint['status'], ['accepted', 'satisfied', 'declined', 'invalid', 'mistaken'])) {

                                    if (self::CheckOldComplaint($oldData, $complaint['id'], 'check', 'qualification')) {// проверяем была ли жалоба раньше
                                        $oldComplaintStatus = self::CheckOldComplaint($oldData, $complaint['id'], 'status', 'qualification');
                                        if ($oldComplaintStatus != $complaint['status']) {
                                            $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                                'tender' => $data['data'],
                                                'complaint' => $complaint
                                            ])
                                                ->setFrom(Yii::$app->params['mail_sender_full'])
                                                ->setTo([$user->user->username, $buyerMail])
                                                ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                                ->send();
                                            if ($isSend) {

                                            }
                                        }
                                    } else {
                                        $isSend = Yii::$app->mailer->compose('complaints/organ_result', [
                                            'tender' => $data['data'],
                                            'complaint' => $complaint
                                        ])
                                            ->setFrom(Yii::$app->params['mail_sender_full'])
                                            ->setTo([$user->user->username, $buyerMail])
                                            ->setSubject('Информация по тендеру ' . $data['data']['title'])
                                            ->send();
                                        if ($isSend) {

                                        }
                                    }

                                }
                            }
                        }
                    }
                }


            }
        }
    }

    public static function CheckOldComplaint($data, $complaintId, $type, $stage)
    {
        if ($stage == 'complaints') {
            foreach ($data['data']['complaints'] as $c => $complaint) {
                if ($complaint['id'] == $complaintId) {
                    return $type == 'status' ? $complaint['status'] : true;
                }
            }
            return false;
        } elseif ($stage == 'prequalification') {
            foreach ($data['data']['qualifications'] as $q => $qualification) {
                if ($qualification['status'] == 'cancelled') continue;
                if (isset($qualification['complaints']) && count($qualification['complaints'])) {
                    foreach ($qualification['complaints'] as $c => $complaint) {
                        if ($complaint['id'] == $complaintId) {
                            return $type == 'status' ? $complaint['status'] : true;
                        }
                    }
                }

            }
            return false;
        } elseif ($stage == 'qualification') {
            foreach ($data['data']['awards'] as $a => $award) {
                if ($award['status'] == 'cancelled') continue;
                if (isset($award['complaints']) && count($award['complaints'])) {
                    foreach ($award['complaints'] as $c => $complaint) {
                        if ($complaint['id'] == $complaintId) {
                            return $type == 'status' ? $complaint['status'] : true;
                        }
                    }
                }

            }
            return false;
        }
    }


    public static function SendBid($tenders, $data, $action)
    {


        // отправляем мыло о создании тендера
//        Notifications::SendBid($tenders, $this);

//        $tender = HTender::load(SimpleTenderConvertIn::getSimpleTender($tenders->id));
//        $html = $model->renderPartial('success', [
//            'tender' => $tender,
//            'tenders' => $tenders,
//            'tenderId' => $tenders->id,
//            'published' => !empty($tenders->tender_id),
//        ]);
//Yii::$app->VarDumper->dump($data, 10, true);die;

        $isSend = Yii::$app->mailer->compose('bid_sended', [
            'tenders' => $tenders,
            'data' => $data,
            'action' => $action
        ])
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo(Yii::$app->user->identity->username)
            ->setSubject(Yii::t('app', 'Информация по тендеру') . ' ' . $tenders->title)
            ->send();

//        if ($isSend) {
//            $tenders->mail_send_at = strtotime('now');
//            $tenders->save(false);
//        }
    }

    public static function SendComplaintCancel($tender, $complaintId, $stage, $reason)
    {
        $complaint = Complaints::getComplaintByid($tender, $complaintId, $stage);

        $isSend = Yii::$app->mailer->compose('complaint_cancel', [
            'tender' => $tender,
            'complaint' => $complaint,
            'reason' => $reason
        ])
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo(Yii::$app->user->identity->username)
            ->setSubject(Yii::t('app', 'Информация по тендеру') . ' ' . $tender['title'])
            ->send();

//        if ($isSend) {
//            $tenders->mail_send_at = strtotime('now');
//            $tenders->save(false);
//        }
    }

    public static function SendAuctionNotice()
    {
        /** @var @TODO: Переделать дату `auction_date` на !DATE! */
        $t_arr = Tenders::find()->select(['id', 'company_id', 'tender_cbd_id', 'auction_date'])->andWhere(['like', 'auction_date', date('Y-m-d')])->asArray()->all(); //date('Y-m-d'). //'2016-08-30'
        //
        //echo '<pre>'; print_r($t_arr); DIE();
        foreach ($t_arr AS $t) {

            /** @TODO: Убрать заглушку после исправления даты */
            if (is_string($t['auction_date']) && strpos($t['auction_date'], 'T') !== false) {
                $tmp_auc_date = explode('T', $t['auction_date']); //2016-08-31T12:23:55+03:00
                $auction_date = ['date' => $tmp_auc_date[0], 'time' => str_replace('+03:00', '', $tmp_auc_date[1])];
            } else if (is_int($t['auction_date'])) {
                $auction_date = ['date' => date('d.m.Y', $t['auction_date']), 'time' => date('H:i:s', $t['auction_date'])];
            } else {
                $auction_date = ['date' => $t['auction_date'], 'time' => ''];
            }

            $send['buyer'] = [];
            if ($t['company_id']) {
                $u_arr = User::find()->select(['username'])->where(['company_id' => $t['company_id']])->asArray()->all();
                foreach ($u_arr AS $u) {
                    $send['buyer'][] = $u['username'];
                }
            }

            $send['seller'] = [];
            $b_arr = Bids::find()->select(['company_id'])->where(['tid' => $t['id']])->asArray()->all();
            foreach ($b_arr AS $b) {
                if ($b['company_id']) {
                    $u_arr = User::find()->select(['username'])->where(['company_id' => $b['company_id']])->asArray()->all();
                    foreach ($u_arr AS $u) {
                        $send['seller'][] = $u['username'];
                    }
                }
            }

            foreach ($send AS $type => $s) {
                if (count($s)) {
                    //$s = ['nikolay.bnm@gmail.com'];
                    $isSend = Yii::$app->mailer
                        ->compose('auction_notice', [
                            'link' => Yii::$app->params['site_url'] . $type . '/tender/view/' . $t['id'],
                            'cbdId' => $t['tender_cbd_id'],
                            'type' => $type, 'date' => $auction_date])
                        ->setFrom(Yii::$app->params['mail_sender_full'])
                        ->setTo(Yii::$app->params['mail_sender_full'])
                        ->setBcc($s)
                        ->setSubject(Yii::t('app', 'Інформація по тендеру ') . $t['tender_cbd_id'])
                        ->send();
                    if (!$isSend) {/*если письмо не ушло..., что-то делаем*/
                    }
                }
            }
        }
    }

    public static function SendMailCancelTenderOrLotToRequester($data, $post, $tenders)
    {
        $cancel = $post['Tender']['cancellations']['relatedLot'];
        if (isset($data['data']['questions']) && count($data['data']['questions'])) {
            $questionsId = [];
            foreach ($data['data']['questions'] as $question) {
                if (($question['questionOf'] == 'lot') || ($question['questionOf'] == 'item')) {
                    $questionsId[$question['id']] = $question['relatedItem'];
                }
                if ($question['questionOf'] == 'tender') {
                    continue;
                }
            }
        }

        if (isset($data['data']['complaints']) && count($data['data']['complaints'])) {
            $complaintsId = [];
            foreach ($data['data']['complaints'] as $complaint) {
                if (isset($complaint['relatedLot'])) {
                    $complaintsId[$complaint['id']] = $complaint['relatedLot'];
                } else {
                    $complaintsId[$complaint['id']] = $data['data']['id'];
                }
            }
        }

        if (!empty($questionsId) || !empty($complaintsId)) {
            if (!empty($questionsId)) {
                $sellersQ = Questions::find()
                    ->select(['user_id', 'question_id'])
                    ->where(['in', 'question_id', array_keys($questionsId)])
                    ->joinWith(['user'])
                    ->all();
            }
            if (!empty($complaintsId)) {
                $sellersC = Complaints::find()
                    ->select(['user_id', 'complaint_id'])
                    ->where(['in', 'complaint_id', array_keys($complaintsId)])
                    ->joinWith(['user'])
                    ->all();
            }


            $sellers = [];
            foreach ($sellersQ as $sellerQ) {
                if (!in_array($sellerQ->user->username, $sellers)) {
                    $sellers[$sellerQ->question_id] = $sellerQ->user->username;
                }
            }
            foreach ($sellersC as $sellerC) {
                if (!in_array($sellerC->user->username, $sellers)) {
                    $sellers[$sellerC->complaint_id] = $sellerC->user->username;
                }
            }
            if ($cancel == 'tender') {
                foreach ($sellers as $s => $seller) {
                    Yii::$app->mailer->compose('_cancelTenderOrLot', [
                        'tender' => $data['data'],
                        'canceledTender' => Yii::t('app', 'tender'),
                        'link' => Yii::$app->params['site_url'] . 'seller/tender/view/' . $tenders->id,
                    ])
                        ->setFrom(Yii::$app->params['mail_sender_full'])
                        ->setTo($seller)
                        ->setSubject(Yii::t('app', 'Інформація по тендеру ') . $data['data']['title'])
                        ->send();
                }
            } else {
                if (isset($data['data']['lots']) && count($data['data']['lots'])) { // мультилот
                    if (isset($cancel) && (in_array($cancel, $questionsId) || in_array($cancel, $complaintsId))) {
                        foreach ($sellers as $key => $seller) {
                            if (in_array($key, array_keys($questionsId)) || in_array($key, array_keys($complaintsId))) {
                                Yii::$app->mailer->compose('_cancelTenderOrLot', [
                                    'tender' => $data['data'],
                                    'canceledQ' => $questionsId,
                                    'canceledC' => $complaintsId,
                                    'link' => Yii::$app->params['site_url'] . 'seller/tender/view/' . $tenders->id,
                                ])
                                    ->setFrom(Yii::$app->params['mail_sender_full'])
                                    ->setTo($seller)
                                    ->setSubject(Yii::t('app', 'Інформація по тендеру ') . $data['data']['title'])
                                    ->send();
                            }
                        }
                    }
                }
            }
        }


    }

    public static function SentEmailTenderQuestion($post, $tender, $tenderEmail = NULL)
    {
        // Письмо селлеру
        $tenderArr = explode('_', $post['Question']['questionOf']);
        $tenderData['tenderId'] = $tender['tenderID'];
        $tenderData['title'] = $tender['title'];
        switch ($tenderArr[0]) {
            case 'item':
                foreach ($tender['items'] as $item) {
                    if (isset($tenderArr[1])) {
                        if ($item['id'] == $tenderArr[1]) {
                            $tenderData['item']['title'] = $item['description'];
                            $tenderData['lot'] = false;
                            break;
                        }
                    }
                }
                break;
            case 'lot':
                foreach ($tender['lots'] as $item) {
                    if (isset($tenderArr[1])) {
                        if ($item['id'] == $tenderArr[1]) {
                            $tenderData['item'] = false;
                            $tenderData['lot']['title'] = $item['title'];
                            break;
                        }
                    }
                }
                break;
        }

        $itemTitle = ($tenderData['item']) ? 'До товару: "' . $tenderData['item']['title'] . '"' : '';
        $lotTitle = ($tenderData['lot']) ? 'До лоту: "' . $tenderData['lot']['title'] . '"' : '';
        $titleSelect = ($itemTitle) ? $itemTitle : $lotTitle;
        $body = Yii::$app->mailer->render('email_tender_request', [
            'post' => $post,
            'tenderData' => $tenderData,
            'titleSelect' => $titleSelect,
            'messageTo' => 'seller',
        ]);
        $to = Yii::$app->user->identity->username;
        $subject = Yii::t('app', 'The question by tender №') . ': ' . $tenderData['tenderId'];
        self::sendEmail($to, $subject, $body);

        // Письмо байеру
        if ($tenderEmail) {
            $body = Yii::$app->mailer->render('email_tender_request', [
                'post' => $post,
                'tenderData' => $tenderData,
                'titleSelect' => $titleSelect,
                'messageTo' => 'buyer',
            ]);
            $subject = Yii::t('app', 'The question by tender №') . ': ' . $tenderData['tenderId'];
            self::sendEmail($tenderEmail, $subject, $body);
        }
    }

    public static function SentEmailTenderCompare($post, $tender, $tenderEmail = NULL)
    {
        // Письмо селлеру

        $tenderArr = explode('_', $post['Complaint']['relatedLot']);
        $tenderData['tenderId'] = $tender['tenderID'];
        $tenderData['title'] = $tender['title'];
        $tenderData['messageStatus'] = ($post['Complaint']['status'] == 'claim') ? 'Вимога' : 'Вимога/Скарга';
        $tenderData['messageStatusPad'] = ($post['Complaint']['status'] == 'claim') ? 'вимогу' : 'вимогу/скаргу';
        if ($post['Complaint']['status'] == 'pending') {
            $tenderData['messageStatus'] = 'Скарга';
            $tenderData['messageStatusPad'] = 'скаргу';
        }
        switch ($tenderArr[0]) {
            case 'item':
                foreach ($tender['items'] as $item) {
                    if (isset($tenderArr[1])) {
                        if ($item['id'] == $tenderArr[1]) {
                            $tenderData['item']['title'] = $item['description'];
                            $tenderData['lot'] = false;
                            break;
                        }
                    }
                }
                break;
            case 'lot':
                foreach ($tender['lots'] as $item) {
                    if (isset($tenderArr[1])) {
                        if ($item['id'] == $tenderArr[1]) {
                            $tenderData['item'] = false;
                            $tenderData['lot']['title'] = $item['title'];
                            break;
                        }
                    }
                }
                break;
        }

        $itemTitle = ($tenderData['item']) ? 'До товару: "' . $tenderData['item']['title'] . '"' : '';
        $lotTitle = ($tenderData['lot']) ? 'До лоту: "' . $tenderData['lot']['title'] . '"' : '';
        $titleSelect = ($itemTitle) ? $itemTitle : $lotTitle;

        $body = Yii::$app->mailer->render('email_tender_request', [
            'post' => $post,
            'tenderData' => $tenderData,
            'titleSelect' => $titleSelect,
            'messageTo' => 'seller',
        ]);
        $to = Yii::$app->user->identity->username;
        $subject = Yii::t('app', $tenderData['messageStatus'] . " за тендером №") . $tenderData['tenderId'];
        self::sendEmail($to, $subject, $body);

        // Письмо байеру
        if ($tenderEmail) {
            $body = Yii::$app->mailer->render('email_tender_request', [
                'post' => $post,
                'tenderData' => $tenderData,
                'titleSelect' => $titleSelect,
                'messageTo' => 'buyer',
            ]);
            $subject = Yii::t('app', $tenderData['messageStatus'] . " за тендером №") . $tenderData['tenderId'];
            self::sendEmail($tenderEmail, $subject, $body);
        }
    }

    public static function AddEventToCabinet()
    {
        $tenders = Tenders::find()
            ->andWhere(['!=', 'status', 'cancelled'])
            ->andWhere(['!=', 'status', 'unsuccessful'])
            ->andWhere(['!=', 'status', 'complete'])
            ->andWhere(['!=', 'company_id', 'null'])->all();
        $companyEvents = [];
        foreach ($tenders as $tender) {
            $data = Json::decode($tender->response)['data'];

            //Запитання
            if (isset($data['questions'])) {
                //Запитання без відповіді
                foreach ($data['questions'] as $q => $v) {
                    if (isset($v['answer']) && $v['answer']) continue;
                    $companyEvents[$tender->company_id][$tender->id]['questions'][] = $v;
                    if (!isset($companyEvents[$tender->company_id][$tender->id]['tender'])) {
                        $companyEvents[$tender->company_id][$tender->id]['tender'] = Tenders::getUsefulTenderInformation($tender);
                    }
                }
            }

            //Вимоги та скарги
            if (isset($data['complaints'])) {
                foreach ($data['complaints'] as $c => $complaint) {

                    if ($complaint['status'] == 'draft') continue;
                    if (in_array($complaint['status'], ['answered', 'satisfied', 'declined', 'invalid', 'mistaken'])) continue;
                    $companyEvents[$tender->company_id][$tender->id]['complaints'][] = $complaint;
                    if (!isset($companyEvents[$tender->company_id][$tender->id]['tender'])) {
                        $companyEvents[$tender->company_id][$tender->id]['tender'] = Tenders::getUsefulTenderInformation($tender);
                    }
                }
            }

            //скарги на передкфаліфікацію
            if (isset($data['qualifications']['complaints'])) {
                foreach ($data['qualifications']['complaints'] as $c => $complaint) {
                    if ($complaint['status'] == 'draft') continue;
                    if (!in_array($complaint['status'], ['claim', 'pending'])) continue;
                    $companyEvents[$tender->company_id][$tender->id]['qualifications']['complaints'] = $complaint;
                    if (!isset($companyEvents[$tender->company_id][$tender->id]['tender'])) {
                        $companyEvents[$tender->company_id][$tender->id]['tender'] = Tenders::getUsefulTenderInformation($tender);
                    }
                }
            }

            //скарги на кваліфікацію
            if (isset($data['awards'])) {
                foreach ($data['awards'] as $a => $award) {
                    if (($award['status'] == 'active' || $award['status'] == 'unsuccessful') && isset($award['complaints'])) {
                        foreach ($award['complaints'] as $c => $complaint) {
                            if ($complaint['status'] == 'draft') continue;
                            if (in_array($complaint['status'], ['satisfied', 'declined', 'invalid', 'mistaken'])) continue;
                            $companyEvents[$tender->company_id][$tender->id]['awards']['complaints'][$complaint['id']] = $complaint;
                            $companyEvents[$tender->company_id][$tender->id]['awards']['complaints'][$complaint['id']]['award_id'] = $award['id'];
                            if (!isset($companyEvents[$tender->company_id][$tender->id]['tender'])) {
                                $companyEvents[$tender->company_id][$tender->id]['tender'] = Tenders::getUsefulTenderInformation($tender);
                            }
                        }
                    }
                }
            }
        }
        foreach ($companyEvents as $company_id => $events) {
            CabinetEvent::createCompanyEvent($company_id, Json::encode($events));
            $companiesId[] = $company_id;
        }
        //удаляем старые записи, например, если информация по тендеру уже не неактуальна
        if (isset($companiesId)) {
            CabinetEvent::updateTableCabinetEvent($companiesId);
        } else {
            CabinetEvent::deleteAll();
        }
    }

    /** Надсилає лист про створення тендеру
     *
     * @param $tenders Tenders
     * @return bool
     */
    public static function createTender($tenders)
    {
        return Yii::$app->mailer->compose('email_create_tender', [
            'tenders' => $tenders,
        ])
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo([Yii::$app->user->identity->username])
            ->setSubject(Yii::t('app', 'You have created a tender'))
            ->send();
    }

    /** Надсилає лист-підтвердження реєстрації
     *
     * @param $user User
     * @return bool
     */
    public static function confirmRegistration($user)
    {
        return Yii::$app->mailer->compose('_registration', [
            'user' => $user
        ])
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo($user->username)
            ->setSubject(Yii::t('app', 'Подтверждение регистрации'))
//                ->setHtmlBody(Html::a('activation code', Yii::$app->urlManager->createAbsoluteUrl(['/register/confirm', 'activationcode' => $user->activationcode])))
            ->send();
    }

    /** Надсилає лист про запит приєднання до компанії
     *
     * @param $join
     * @param string $ownerEmail
     * @return bool
     */
    public static function joinRegistration($join, $ownerEmail)
    {
        return Yii::$app->mailer->compose('_registration_join', [
            'user' => $join
        ])
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo($ownerEmail)
            ->setSubject(Yii::t('app', 'Запрос на присоеденение'))
//                    ->setHtmlBody(Html::a('activation code', Yii::$app->urlManager->createAbsoluteUrl(['/register/confirm', 'activationcode' => $user->activationcode])))
            ->send();
    }

    /** Надсилає лист запрошення
     *
     * @param string $email
     * @param string $token
     * @return bool
     */
    public static function sendInvite($email, $token)
    {
        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo($email)
            ->setSubject(Yii::t('app', 'invite.Subject'))
            ->setTextBody(Yii::t('app', 'invite.TextBody') . Yii::$app->urlManager->createAbsoluteUrl(['/invite/register', 'token' => $token]))
            ->setHtmlBody(Yii::t('app', 'invite.HtmlBody') . Html::a(Yii::t('app', 'invite.link to invite'), Yii::$app->urlManager->createAbsoluteUrl(['/invite/register', 'token' => $token])))
            ->send();
    }

    /** Надсилає лист-скидання пароля
     *
     * @param $user User
     * @param string $username
     * @return bool
     */
    public static function passwordReset($user, $username)
    {
        return Yii::$app->mailer->compose(
            ['html' => 'passwordResetToken-html'],
            ['user' => $user]
        )
            ->setFrom(Yii::$app->params['mail_sender_full'])
            ->setTo($username)
            ->setSubject(Yii::t('app', 'Сброс пароля') . ' ' . \Yii::$app->name)
            ->send();
    }
}
