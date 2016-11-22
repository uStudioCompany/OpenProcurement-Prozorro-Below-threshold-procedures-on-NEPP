<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "cabinet_event_seller".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property integer $tid
 * @property string $event_id
 * @property integer $status
 */
class CabinetEventSeller extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cabinet_event_seller';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'tid', 'status'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['event_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'tid' => Yii::t('app', 'Tid'),
            'event_id' => Yii::t('app', 'Event ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Create user event's in db
     *
     * @param integer $user_id
     * @param string $type
     * @param integer $tid
     * @param string $event_id
     * @param integer $status
     * @return boolean
     */
    public static function createSellerEvent($user_id, $type, $tid, $event_id, $status = 1)
    {
        $event = new CabinetEventSeller();
        $event->user_id = $user_id;
        $event->type = $type;
        $event->tid = $tid;
        $event->event_id = $event_id;
        $event->status = $status;
        return $event->save();
    }

    /**
     * Help function for buyer action answer. Create user event's in db
     *
     * @param integer $tid
     * @param string $type
     * @param string $event_id
     * @return boolean
     */
    public static function createEventForSellerFromTender($tid, $type, $event_id)
    {
        if (isset($tid) && isset($type) && isset($event_id)) {
            if ($type == 'question') {
                $user_id = Questions::findOne(['question_id' => $event_id])->user_id;
            } elseif ($type == 'complaint') {
                $user_id = Complaints::findOne(['complaint_id' => $event_id, 'type' => $type])->user_id;
            } elseif ($type == 'award') {
                $user_id = Complaints::findOne(['complaint_id' => $event_id, 'type' => $type])->user_id;
            } else {
                return false;
            }
            return self::createSellerEvent($user_id, $type, $tid, $event_id);
        } else {
            return false;
        }
    }

    /**
     * Return all events of User
     *
     * @param integer $user_id
     * @return mixed
     */
    public static function getUserEvents($user_id)
    {
        return CabinetEventSeller::findAll(['user_id' => $user_id, 'status' => 1]);
    }

    /**
     * Delete all events of Tender, if it has contract
     *
     * @param integer $tid
     */
    public static function deleteTendersEventsIfContract($tid)
    {
        $events = CabinetEventSeller::find()->where(['tid' => $tid])->andWhere('type != :type', [':type' => 'protokol'])->all();
        foreach ($events as $event){
            $event->delete();
        }
    }

    /**
     * Read all events of User (when click on button 'read all')
     *
     * @param integer $userId
     */
    public static function readAllSellerEvents($userId)
    {
        CabinetEventSeller::updateAll(['status' => 0], ['user_id' => $userId, 'status' => 1]);
        CabinetEventSeller::deleteAll(['status' => 0]);
    }

    /**
     * Read one event of user (when click on button 'detail')
     *
     * @param integer $userId
     * @param string $eventId
     * @param string $type
     * @return bool
     */
    public static function readSellerEvent($userId, $eventId, $type)
    {
        $event = CabinetEventSeller::findOne(['user_id' => $userId, 'type' => $type, 'event_id' => $eventId]);
        return $event->delete();
    }

    /**
     * Return array of User events.
     * All events grouped by complaint, question.
     *
     * @return array
     */
    public function getSellerEvents()
    {
        $cabinetEvent = new CabinetEventSeller();
        $events = $cabinetEvent->getUserEvents(Yii::$app->user->identity->id);
        $tendersId = [];
        $protokolsId = [];
        foreach ($events as $event) {
            if (!in_array($event->tid, $tendersId)) {
                $tendersId[] = $event->tid;
            }
            if ($event->type == 'complaint') {
                $complaintsId[] = $event->event_id;
            }
            if ($event->type == 'question') {
                $questionsId[] = $event->event_id;
            }
            if ($event->type == 'award') {
                $awardsId[] = $event->event_id;
            }
            if ($event->type == 'protokol') {
                $protokolsId[] = $event->event_id;
            }
            if ($event->type == 'disqualification') {
                $disqualificationsId[] = $event->event_id;
            }
            if ($event->type == 'prequal_unsuc') {
                $unsuccessfulsId[] = $event->event_id;
            }
            if ($event->type == 'prequal_activate') {
                $activesId[] = $event->event_id;
            }
            //тут будет информация для тех кто сделал ставку, а тендер/лот отменили
            if ($event->type == 'cancel') {
                $bidsId[$event->tid] = $event->event_id;
            }
        }
        $tenders = Tenders::find()->where(['in', 'id', $tendersId])->all();
        foreach ($tenders as $tender) {
            $tids[$tender->id] = ['tender' => Tenders::getUsefulTenderInformation($tender), 'questions' => [], 'complaints' => [], 'awards' => []];
        }
        foreach ($tenders as $tender) {
            if (in_array($tender->id, $protokolsId)) {
                $tids[$tender->id]['protokol'] = $tender->id;
                self::deleteTendersEventsIfContract($tender->id);
                continue;
            }
            $response = Json::decode($tender->response);
            if (isset($response['data']['questions'])) {
                foreach ($response['data']['questions'] as $question) {
                    if (in_array($question['id'], $questionsId)) {
                        $tids[$tender->id]['questions'][] = $question;
                    }
                }
            }
            if (isset($response['data']['complaints'])) {
                foreach ($response['data']['complaints'] as $complaint) {
                    if (in_array($complaint['id'], $complaintsId)) {
                        $tids[$tender->id]['complaints'][] = $complaint;
                    }
                }
            }
            if (isset($response['data']['awards'])) {
                foreach ($response['data']['awards'] as $award) {
                    foreach ($disqualificationsId as $item) {
                        if ($award['id'] == $item) {
                            $tids[$tender->id]['disqualification'][$award['status']] = $item;
                        }
                    }
                    foreach ($award['complaints'] as $complaint) {
                        if (in_array($complaint['id'], $awardsId)) {
                            $tids[$tender->id]['awards'][$award['id']]['complaints'][] = $complaint;
                        }
                    }
                }
            }
            if (isset($response['data']['lots'])) {
                if (isset($response['data']['qualifications'])) {
                    foreach ($response['data']['qualifications'] as $qualification) {
                        if (in_array($qualification['bidID'], $unsuccessfulsId) && $qualification['status'] == 'unsuccessful') {
                            foreach ($response['data']['lots'] as $lot) {
                                if ($qualification['lotID'] == $lot['id']) {
                                    $tids[$tender->id]['unsuccessful'][$lot['id']] = ['title' => $lot['title'], 'bidId' => $qualification['bidID']];
                                }
                            }
                        }
                        if (in_array($qualification['bidID'], $activesId) && $qualification['status'] == 'active') {
                            foreach ($response['data']['lots'] as $lot) {
                                if ($qualification['lotID'] == $lot['id']) {
                                    $tids[$tender->id]['activate'][$lot['id']] = ['title' => $lot['title'], 'bidId' => $qualification['bidID']];
                                }
                            }
                        }
                    }
                }
            } else {
                if (count($response['data']['bids'])) {
                foreach (@$response['data']['bids'] as $bid) {
                    if (in_array($bid['id'], $unsuccessfulsId)) {
                        $tids[$tender->id]['unsuccessful'][] = ['title' => $tender['title'], 'bidId' => $bid['id']];
                    }
                    if (in_array($bid['id'], $activesId)) {
                        $tids[$tender->id]['activate'][] = ['title' => $tender['title'], 'bidId' => $bid['id']];
                    }
                } }
            }
            if (count($bidsId)) {
            foreach ($bidsId as $tid => $bid) {
                if  ($tid == $tender->id)
                    $tids[$tender->id]['cancel'][] = ['title' => $tender['title'], 'bidId' => $bid];
            } }
        }
        return $tids;
    }

    /**
     * Добавляет уведомление об отмене тендера всем участникам (те, которые задавали вопросы/жаловались/делали ставки)
     *
     * @param $data
     * @param $post
     * @param $tenders
     */
    public static function AddEventInCabinetIfTenderClose($data, $post, $tenders)
    {
        $cancel = $post['Tender']['cancellations']['relatedLot'];
        $multiLot = (isset($data['data']['lots']) && count($data['data']['lots'])) ? true : false;
//        if ($cancel == 'tender') {
//            if (isset($data['data']['questions']) && count($data['data']['questions'])) {
//                $questionsId = [];
//                foreach ($data['data']['questions'] as $question) {
//                    if ($question['questionOf'] == 'item') {
//                        $questionsId[$question['id']] = $question['relatedItem'];
//                    } elseif ($multiLot && $question['questionOf'] == 'lot') {
//                        foreach ($data['data']['lots'] as $lot) {
//                            if ($question['relatedItem'] == $lot['id'] && $lot['status'] != 'cancelled') {
//                                $questionsId[$question['id']] = $question['relatedItem'];
//                            }
//                        }
//                    } elseif ($question['questionOf'] == 'tender') {
//                        $questionsId[$question['id']] = $tenders->id;
//                    }
//                }
//            }
//            if (isset($data['data']['complaints']) && count($data['data']['complaints'])) {
//                $complaintsId = [];
//                foreach ($data['data']['complaints'] as $complaint) {
//                    if ($multiLot && isset($complaint['relatedLot'])) {
//                        foreach ($data['data']['lots'] as $lot) {
//                            if ($complaint['relatedLot'] == $lot['id'] && $lot['status'] != 'cancelled') {
//                                $complaintsId[$complaint['id']] = $complaint['relatedItem'];
//                            }
//                        }
//                    } else {
//                        $complaintsId[$complaint['id']] = $data['data']['id'];
//                    }
//                }
//            }
//        } elseif ($multiLot) { // мультилот
//            if (isset($data['data']['questions']) && count($data['data']['questions'])) {
//                $questionsId = [];
//                foreach ($data['data']['questions'] as $question) {
//                    if (($cancel == $question['relatedItem']) && ($question['questionOf'] == 'lot') || ($question['questionOf'] == 'item')) {
//                        $questionsId[$question['id']] = $question['relatedItem'];
//                    }
//                }
//            }
//            if (isset($data['data']['complaints']) && count($data['data']['complaints'])) {
//                $complaintsId = [];
//                foreach ($data['data']['complaints'] as $complaint) {
//                    if (($cancel == $complaint['relatedLot']) && isset($complaint['relatedLot'])) {
//                        $complaintsId[$complaint['id']] = $complaint['relatedLot'];
//                    }
//                }
//            }
//        }
        $dbBids = Bids::findAll(['tid' => $tenders->id]);
        if (!is_null($dbBids)) {
            foreach ($dbBids as $dbBid) {
                self::createSellerEvent($dbBid->user_id, 'cancel', $tenders->id, $dbBid->bid_id);
            }
        }
        if (!empty($questionsId) || !empty($complaintsId)) {
            if (!empty($questionsId)) {
                $sellersQ = Questions::find()
                    ->select(['user_id', 'question_id'])
                    ->where(['in', 'question_id', array_keys($questionsId)])
                    ->all();
            }
            if (!empty($complaintsId)) {
                $sellersC = Complaints::find()
                    ->select(['user_id', 'complaint_id', 'type'])
                    ->where(['in', 'complaint_id', array_keys($complaintsId)])
                    ->all();
            }
            foreach ($sellersQ as $sellerQ) {
                self::createSellerEvent($sellerQ->user_id, 'question', $tenders->id, $sellerQ->question_id);
            }
            foreach ($sellersC as $sellerC) {
                self::createSellerEvent($sellerC->user_id, $sellerC->type, $tenders->id, $sellerC->complaint_id);
            }
        }
    }

    /**
     * Отправляет всем участникам уведомление о выборе побетиля
     *
     * @param $post
     */
    public static function AddEventIfContractActivate($post)
    {
        $tid = $post['id'];
        $bids = Bids::find()->where(['tid' => $tid])->all();
        foreach ($bids as $bid) {
            self::createSellerEvent($bid->user_id, 'protokol', $tid, $tid);
        }
    }

    /**
     * Добавляет уведомление участнику, если статус его ставки изменен
     *
     * @param $tender
     * @param integer $awardId
     */
    public static function AddEventIfAwardChange($tender, $awardId)
    {
        $data = Json::decode($tender->response, true)['data'];
        foreach ($data['awards'] as $award) {
            if ($award['id'] == $awardId) {
                $bidId = $award['bid_id'];
                break;
            }
        }
        if (isset($bidId)) {
            $bid = Bids::findOne(['bid_id' => $bidId]);
            if (isset($bid)) {
                self::createSellerEvent($bid->user_id, 'disqualification', $tender->id, (string)$awardId);
            }
        }
    }

    /**
     * Добавляет уведомление участнику, о результате квалификации
     *
     * @param $tender
     * @param integer $qualificationId
     * @param string $type
     */
    public static function AddPrequalificationEvent($tender, $qualificationId, $type)
    {
        $data = Json::decode($tender->response, true)['data'];
        foreach ($data['qualifications'] as $qualification) {
            if ($qualification['id'] == $qualificationId) {
                $bidId = $qualification['bidID'];
                break;
            }
        }
        if ($type == 'unsuccessful') {
            $dbType = 'unsuc';
        } elseif ($type == 'active') {
            $dbType = 'activate';
        }
        if (isset($bidId)) {
            $bid = Bids::findOne(['bid_id' => $bidId]);
            if (!is_null($bid)) {
                self::createSellerEvent($bid->user_id, 'prequal_' . $dbType, $tender->id, $bidId);
            }
        }
    }
}
