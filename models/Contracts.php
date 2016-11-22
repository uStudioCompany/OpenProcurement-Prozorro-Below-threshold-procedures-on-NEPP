<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contracts".
 *
 * @property integer $id
 * @property string $contract_num
 * @property integer $create_at
 * @property integer $company_id
 * @property integer $template_id
 *
 * @property Companies $company
 */
class Contracts extends \yii\db\ActiveRecord
{
    const DOCNUM_LEN = 22;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_at', 'company_id','template_id'], 'integer'],
            [['contract_num'], 'string', 'max' => 50],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Companies::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'contract_num' => Yii::t('app', 'Contract Num'),
            'create_at' => Yii::t('app', 'Create At'),
            'company_id' => Yii::t('app', 'Company ID'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->create_at = time();
            $this->company_id = Yii::$app->user->identity->company_id;
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }
}
