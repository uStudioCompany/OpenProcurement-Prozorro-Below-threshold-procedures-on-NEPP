<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contracts".
 *
 * @property integer $id
 * @property resource $text
 * @property integer $company_id
 * @property integer $create_at
 *
 * @property Companies $company
 */
class ContractsTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contracts_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'name','description'], 'required'],
            [['text', 'name','description'], 'string'],
            [['create_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'name'),
            'text' => Yii::t('app', 'Text'),
            'create_at' => Yii::t('app', 'Create At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }
}
