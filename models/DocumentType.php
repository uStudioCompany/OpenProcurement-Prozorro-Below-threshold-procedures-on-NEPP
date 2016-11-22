<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_types".
 *
 * @property string $id
 * @property integer $tender_flag
 * @property integer $bid_flag
 * @property integer $award_flag
 * @property integer $contract_flag
 * @property integer $cancellation_flag
 * @property integer $recommended_flag
 * @property string $title
 * @property string $description
 * @property string $title_en
 * @property string $description_en
 * @property string $title_ru
 * @property string $description_ru
 * @property integer $enabled
 */
class DocumentType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document_type';
    }

    public static $_document_subj_types = ['tender', 'award', 'contract'];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description'], 'required'],
            [['tender_flag', 'bid_flag', 'award_flag', 'contract_flag', 'cancellation_flag', 'recommended_flag', 'enabled'], 'integer'],
            [['id', 'type'], 'string', 'max' => 20],
            [['title', 'title_en', 'title_ru'], 'string', 'max' => 255],
            [['description', 'description_en', 'description_ru'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Code'),
            'type' => Yii::t('app', 'Type'),
            'tender_flag' => Yii::t('app', 'Tender'),
            'bid_flag' => Yii::t('app', 'Bid'),
            'award_flag' => Yii::t('app', 'Award'),
            'contract_flag' => Yii::t('app', 'Contract'),
            'cancellation_flag' => Yii::t('app', 'Cancellation'),
            'recommended_flag' => Yii::t('app', 'Recommended'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'title_en' => Yii::t('app', 'Title En'),
            'description_en' => Yii::t('app', 'Description En'),
            'title_ru' => Yii::t('app', 'Title Ru'),
            'description_ru' => Yii::t('app', 'Description Ru'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }

    public static function getTypes($subj_type = 'tender')
    {
        if (in_array($subj_type, self::$_document_subj_types)) {
            return self::find()->where(['type' => $subj_type])->all();
        }
    }

    public static function getType($id = null, $type = null, $lang = null)
    {

        if (!$lang) {
            $lang = Yii::$app->language;
        }

        if (isset(Yii::$app->params['column_lang_pref'][$lang])) {
            $lang = Yii::$app->params['column_lang_pref'][$lang];
        } else {
            return null;
        }

        if ($id) {
            $res = self::find()->select('`title' . $lang . '` as \'title\'')->where(['id' => $id])->one();
            return $res->title;
        } else if ($type) {
//            $res = self::find()->select('`id`, `title' . $lang . '` as \'title\'')->where(['type' => $type, $type.'_flag'=>1])->all();
            $res = self::find()->select('`id`, `title' . $lang . '` as \'title\'')->where([$type.'_flag'=>1])->orderBy('id DESC')->all();
            $out = [];
            foreach ($res AS $row) {
                $out[$row->id] = $row->title;
            }
            return $out;
        }
        return null;
    }

    public function helperLoadCodes($url, $type)
    {
        $out = [];
        $tmp = Yii::$app->opAPI->getStandards($url . $type . '_');
        if (isset($tmp['uk']) && isset($tmp['ru']) && isset($tmp['en'])) {
            foreach ($tmp['uk'] AS $code => $val) {
                $out[$code] = [
                    ':id' => $code,
                    ':type' => $type,
                    ':tender_flag' => 0, ':award_flag' => 0, ':contract_flag' => 0,
                    ':' . $type . '_flag' => 1,
                    ':title' => $val['Title_uk'],
                    ':description' => $val['Description_uk'],
                    ':title_ru' => $tmp['ru'][$code]['Title_ru'],
                    ':description_ru' => $tmp['ru'][$code]['Description_ru'],
                    ':title_en' => $tmp['en'][$code]['Title_en'],
                    ':description_en' => $tmp['en'][$code]['Description_en']];
            }
        } //echo '['. count($out) .']'."\n";
        return $out;
    }

    public function helperLoadCodesAll($url)
    {
        $out = [];
        $types = Yii::$app->params['subDocType'];
        foreach ($types AS $type) {
            $out += $this->helperLoadCodes($url, $type);
        } //echo '-['. count($out) .']-'."\n";
        return $out;
    }

    public function helperSaveCodes($params)
    {
        $sql = "REPLACE INTO `document_type` (`id`, `type`, `tender_flag`, `award_flag`, `contract_flag`, `title`, `description`, `title_en`, `description_en`, `title_ru`, `description_ru`)
                                      VALUES (:id,  :type,  :tender_flag,  :award_flag,  :contract_flag,  :title,  :description,  :title_en,  :description_en,  :title_ru,  :description_ru);";

        $query = $this->getDb()->createCommand($sql);

        $i = 0;
        foreach ($params as $code => $param) {
            $i += $query->bindValues($param)->execute();
        }
        return $i;
    }

    public static function getBidDocumentType(){
        
    }
}
