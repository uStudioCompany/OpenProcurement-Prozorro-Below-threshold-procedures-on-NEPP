<?php

namespace app\models\tenderModels;

use app\models\tenderModels\Organization;
use app\models\tenderModels\Value;
use app\models\tenderModels\Item;
use app\models\tenderModels\Document;
use app\models\tenderModels\Complaint;
use app\models\tenderModels\Period;
use app\models\tenderModels\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

class Award extends BaseModel
{
    public $id;
    public $bid_id;
    public $title;
    public $description;
    public $date;
    public $status;
    public $value;              // class Value
    public $suppliers;          // array of Organization
    public $items;              // array of Item
    public $documents;          // array of Document
    public $complaints;         // array of Complaint
    public $complaintPeriod;    // class Period
    public $lotID;
    public $qualified;
    public $eligible;
    public $cause;
    public $subcontractingDetails;

    public function __construct($scenario = 'default')
    {
        $this->value = new Value($scenario);
        $this->complaintPeriod = new Period($scenario);

        $this->suppliers = ['iClass' => Organization::className()];
        $this->items = ['iClass' => Item::className()];
        $this->documents = ['iClass' => Document::className()];
        $this->complaints = ['iClass' => Complaint::className()];

        parent::__construct($scenario);
    }

    public function formName()
    {
        return 'Award';
    }

    public function rules()
    {
        return [
            [['id'], 'safe'],
            [['bid_id'], 'safe'],
            [['title'], 'safe'],
            [['description'], 'safe'],
            [['date'], 'safe'],
            [['status'], 'safe'],
            [['lotID'], 'safe'],
            [['subcontractingDetails'], 'string'],

            [['qualified', 'eligible'], 'required', 'requiredValue' => 1, 'message' => 'Необхiдно пiдтвердити', 'whenClient' => 'function (attribute, value) { return $(attribute.input).is(":visible"); }', 'except' => 'limitedavards'],
            [['cause'], 'required', 'message' => 'Необхiдно визначити хоча б одну причину', 'whenClient' => 'function (attribute, value) { return $(\'.cause\').is(":visible"); }', 'except' => 'limitedavards'],
            [['description'], 'required', 'whenClient' => 'function (attribute, value) { return $(\'.cause\').is(":visible"); }', 'except' => 'limitedavards'],

            [['id'], 'safe', 'on' => 'limitedavards'],


        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('app', 'Назва рiшення'),
            'description' => Yii::t('app', 'Опис рiшення'),
            'status' => Yii::t('app', 'Поточний статус рiшення'),
            'date' => Yii::t('app', 'Дата рiшення про пiдписання договору'),
            'value' => Yii::t('app', 'Загальна вартiсть згiдно цього рiшення'),
            'suppliers' => Yii::t('app', 'Постачальники, що були визнанi переможцями згiдно цього рiшення'),
            'items' => Yii::t('app', 'Товари та послуги, що розглядались цим рiшенням'),
            'documents' => Yii::t('app', "документи та додатки пов'язанi з рiшенням"),
            'complaintPeriod' => Yii::t('app', 'Перiод часу, пiд час якого можна подавати скарги'),
            'qualified' => Yii::t('app', 'вiдповiдає квалiфiкацiйним критерiям, встановленим замовником в тендернiй документацiї'),
            'eligible' => Yii::t('app', 'вiдсутнi пiдстави для вiдмови в участi згiдно  ст. 17 Закону України ”Про Публiчнi закупiвлi”'),
            'cause' => Yii::t('app', 'Причина дисквалiфiкацii'),
            'subcontractingDetails' => Yii::t('app', 'інформація про підрядника'),
        ];
    }

    public function getStatusDescr()
    {
        // switch ($this->status) {
        //     case 'pending':
        //         return 'переможець розглядається квалiфiкацiйною комiсiєю';
        //     case 'unsuccessful':
        //         return 'квалiфiкацiйна комiсiя вiдмовила переможцю';
        //     case 'active':
        //         return 'закупiвлю виграв учасник з пропозицiєю bid_id';
        //     case 'cancelled':
        //         return 'орган, що розглядає скарги, вiдмiнив результати закупiвлi';

        //     default:
        //         return 'undefined';
        // }
    }

    public static function getAwardById($tender, $awardId)
    {
        foreach ($tender->awards as $k => $award) {
            if ($award->id == $awardId) {
                return $award;
            }
        }
    }

    public static function checkAllowedCancelAward($award)
    {
        if (isset($award->complaints) && count($award->complaints)) {
            foreach ($award->complaints as $k => $v) {
                if (!in_array($v->status, ['resolved', 'stopped', 'declined', 'invalid','mistaken'])) {
                    return false;
                }
            }
            return true;
        } else {
            return true;
        }
    }

    public static function checkAllowedQualificationAward($award, $tenders)
    {
        return true;
    }

    public static function isCancelableAward($tenders, $award)
    {

        if (\app\models\Companies::checkCompanyIsTenderOwner($tenders->id, $tenders)) {
//        Yii::$app->VarDumper->dump($tenders, 10, true);die;
            if ($award->status == 'pending') return false;
//        Yii::$app->VarDumper->dump($tenders->tender_method, 10, true, true);
            if ($award->status == 'active' && $tenders->tender_method != 'limited_reporting' && empty($award->complaints)) return true;

            if (count($award->complaints)) {
                foreach($award->complaints AS $complaint) {
                    if ($complaint->status == 'satisfied') {
                        return true;
                    }
                }
            }

            switch ($tenders->tender_method) {
                case 'open_belowThreshold':
                    if ($award->status == 'unsuccessful') {
                        //ищем жалобы
                        if (empty($award->complaints)) return false;
                        foreach ($award->complaints as &$v) {
                            if (in_array($v['status'], ['draft'])) {
                                continue;
                            } else {
                                return true;
                            }
//                        if (in_array($v['status'], ['claim', 'answered', 'pending', 'resolved',])) return true;
                        }
                        unset($v);
                    } elseif ($award->status == 'active') return true;
                    break;

                default:

            }
            return false;
        }
        return false;
    }

    /** Delete lotID from dropdown list, if awards with lotID has status active
     *
     * @param $tender \app\models\tenderModels\Tender
     * @return array
     */
    public static function checkLotAwardsForDropdown($tender)
    {
        $array = ArrayHelper::map($tender->lots, 'id', 'title');
        foreach ($tender->awards as $award) {
            if (in_array($award->status, ['active'])) {
                unset($array[$award->lotID]);
            }
        }
        return $array;
    }


    /** Добавляет недостающую инфу в бид из аварда
     * Костыль для мультилотовой переговорной процедуры
     *
     * @param $bid
     * @param $tender
     * @return mixed
     */
    public static function createBidFromAward($bid, $tender)
    {
        $i = 0;
        foreach ($tender->awards as $award) {
            if ($award->bid_id == $bid->id && in_array($award->status, ['pending', 'active', 'cancelled'])) {
                $bid->lotValues[$i]->value = $award->value;
                $bid->lotValues[$i]->relatedLot = $award->lotID;
                return $bid;
            }
        }
        return false;
    }
}
