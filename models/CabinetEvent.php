<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "cabinet_event".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $events
 */
class CabinetEvent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cabinet_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id'], 'integer'],
            [['events'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'company_id' => Yii::t('app', 'Company ID'),
            'events' => Yii::t('app', 'Events'),
        ];
    }

    /**
     * Create company event's in db
     *
     * @param integer $company_id
     * @param string $events
     * @return boolean
     */
    public static function createCompanyEvent($company_id, $events)
    {
        $cabinetEvent = CabinetEvent::findOne(['company_id' => $company_id]);
        if (!isset($cabinetEvent) || is_null($cabinetEvent)) {
            $cabinetEvent = new CabinetEvent();
            $cabinetEvent->company_id = $company_id;
            $cabinetEvent->events = $events;
        } else {
            $cabinetEvent->events = $events;
        }
        return $cabinetEvent->save();
    }

    /**
     * Проверяет, есть ли id компании из таблицы в новой выборке нотификации по тендерам
     * И если нет, то удаляет старую информацию
     *
     * @param $newCompaniesId
     */
    public static function updateTableCabinetEvent($newCompaniesId)
    {
        $companiesId = CabinetEvent::find()->select('company_id')->all();
        foreach ($companiesId as $companyId){
            if (!in_array($companyId['company_id'], $newCompaniesId)){
                $oldCompaniesId[] = $companyId['company_id'];
            }
        }
        if (isset($oldCompaniesId)) {
            self::deleteCompaniesEvents($oldCompaniesId);
        }
    }

    /**
     * Удаляет нотификации комапании, если ее id нет в новых данных о тендерах
     *
     * @param $companiesId
     * @return integer the number of rows deleted
     */
    public static function deleteCompaniesEvents($companiesId)
    {
        return CabinetEvent::deleteAll(['in', 'company_id',  $companiesId]);
    }

    /**
     * Return all events of Company
     *
     * @param integer $company_id
     * @return mixed
     */
    public static function getCompanyEvent($company_id)
    {
        return CabinetEvent::findOne(['company_id' => $company_id]);
    }

    /**
     * Return array of Company events.
     *
     * @return mixed
     */
    public static function getUserEvent()
    {
        return CabinetEvent::getCompanyEvent(Yii::$app->user->identity->company_id);
    }
    
}
