<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cash_flow_reason".
 *
 * @property integer $id
 * @property string $value
 * @property string $value_en
 * @property string $value_ru
 *
 * @property CashFlow[] $cashFlows
 */
class CashFlowReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cash_flow_reason';
    }

    /**
     * @inheritdoc
     *
     **/
    public function rules()
    {
        return [
            [['value', 'value_en', 'value_ru'], 'required'],
            [['value', 'value_en', 'value_ru'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'value' => Yii::t('app', 'Value'),
            'value_en' => Yii::t('app', 'Value En'),
            'value_ru' => Yii::t('app', 'Value Ru'),
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashFlows()
    {
        return $this->hasMany(CashFlow::className(), ['cash_flow_reason_id' => 'id']);
    }
}
