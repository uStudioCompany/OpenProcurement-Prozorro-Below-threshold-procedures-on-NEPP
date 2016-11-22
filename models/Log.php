<?php

namespace app\models;

use yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property string $type
 * @property integer $user_id
 * @property integer $company_id
 * @property string $url
 * @property string $json
 * @property string $responce
 * @property string $method
 * @property string $level
 * @property string $category
 * @property string $log_time
 * @property string $prefix
 * @property string $message
 *
 * @property Companies $company
 * @property User $user
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'company_id'], 'integer'],
            [['json', 'responce','method','url','message'], 'string'],
            [['level', 'category', 'log_time', 'prefix'], 'string', 'max' => 255]
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
            'company_id' => Yii::t('app', 'Company ID'),
            'url' => Yii::t('app', 'URL'),
            'json' => Yii::t('app', 'Json'),
            'responce' => Yii::t('app', 'Responce'),
            'method' => Yii::t('app', 'Method'),
            'level' => Yii::t('app', 'Level'),
            'category' => Yii::t('app', 'Category'),
            'log_time' => Yii::t('app', 'Log Time'),
            'prefix' => Yii::t('app', 'Prefix'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Companies::className(), ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**Filling data without save
     *
     * @param null $method
     * @param null $url
     * @param null $json
     * @param null $type
     * @param null $response
     */
    public function logging($method = null, $url = null, $json = null, $type = null, $response = null)
    {
        $this->user_id = isset(Yii::$app->user) ? Yii::$app->user->identity->id : '';
        $this->company_id = isset(Yii::$app->user) ? Yii::$app->user->identity->company_id : '';
        $this->method = $method ? $method : null;
        $this->url = $url ? $url : null;
        $this->json = $json ? $json : null;
        $this->type = $type ? $type : (self::isConsole() ? 'cron' : 'web');
        $this->responce = $response ? $response : null;
        $this->log_time = time();
    }

    /**Check if log save in params
     *
     * @return bool
     */
    public function isSave()
    {
        switch ($this->type) {
            case 'document' :
                $save = Yii::$app->params['logging']['document.log'];
                break;
            case 'web' :
                $save = Yii::$app->params['logging']['web.log'];
                break;
            case 'cron' :
                $save = Yii::$app->params['logging']['cron.log'];
                break;
            default :
                $save = true;
        }
        if ($save) {
            return $this->save(false);
        }
        return false;
    }

    /**Check is console
     *
     * @return bool
     */
    public static function isConsole()
    {
        return Yii::$app instanceof yii\console\Application;
    }
}
