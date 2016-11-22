<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $name_en
 * @property string $name_ru
 * @property string $url
 * @property integer $order
 * @property integer $active
 */
class Menu extends \yii\db\ActiveRecord
{

    /**
     * Value of 'published' field where page is not published.
     */
    const PUBLISHED_NO = 0;
    /**
     * Value of 'published' field where page is published.
     */
    const PUBLISHED_YES = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid','order','published'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
            [['name_en', 'name_ru'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Pid'),
            'name' => Yii::t('app', 'Name'),
            'name_en' => Yii::t('app', 'Name En'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'url' => Yii::t('app', 'Url'),
            'order' => Yii::t('app', 'Order number'),
            'published' => Yii::t('app', 'Published'),
        ];
    }


    public static function getMenu()
    {
        $result = static::getMenuRecrusive(0);
        return $result;
    }

    private static function getMenuRecrusive($parent)
    {

        $items = Menu::find()
            ->where(['pid' => $parent,'published'=>1])
            ->orderBy('order')
            ->asArray()
            ->all();

        $result = [];

        $contentLangParam = 'name'.\Yii::$app->params['column_lang_pref'][\Yii::$app->language];

        foreach ($items as $item) {
            $result[] = [
                'label' => $item[$contentLangParam],
//                'url' => Yii::$app->urlManager->createAbsoluteUrl('pages/'.$item['url']),
                'url' => Yii::$app->urlManager->createAbsoluteUrl($item['url']),
                'items' => static::getMenuRecrusive($item['id']),
//                '<li class="divider"></li>',
            ];
        }
        return $result;
    }

    /**
     * List values of field 'published' with label.
     * @return array
     */
    static public function publishedDropDownList()
    {
        $formatter = Yii::$app->formatter;
        return [
            self::PUBLISHED_NO => $formatter->asBoolean(self::PUBLISHED_NO),
            self::PUBLISHED_YES => $formatter->asBoolean(self::PUBLISHED_YES),
        ];
    }


}
