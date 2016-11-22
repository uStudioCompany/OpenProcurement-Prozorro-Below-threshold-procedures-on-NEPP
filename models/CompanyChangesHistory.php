<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "company_changes".
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $changes
 * @property integer $create_at
 */
class CompanyChangesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_changes_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id','create_at'], 'integer'],
            [['changes'], 'string'],
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
            'changes' => Yii::t('app', 'Changes'),
            'create_at' => Yii::t('app', 'create'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->company_id = Yii::$app->user->identity->company_id;
            $this->create_at = time();
            return true;
        }
        return false;
    }
}
