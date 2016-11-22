<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regions".
 *
 * @property integer $id
 * @property string $name
 * @property string $en_name
 * @property string $ru_name
 * @property string $lang
 *
 * @property Companies[] $companies
 * @property Languages $lang0
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'en_name', 'ru_name'], 'string', 'max' => 50],
            [['lang'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('register', 'id'),
            'name' => Yii::t('register', 'Назва'),
            'en_name' => Yii::t('register', 'En Name'),
            'ru_name' => Yii::t('register', 'Ru Name'),
            'lang' => Yii::t('register', 'Мова'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Companies::className(), ['region' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang0()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang']);
    }
}
