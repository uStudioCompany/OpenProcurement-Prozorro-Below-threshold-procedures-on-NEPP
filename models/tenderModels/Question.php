<?php

namespace app\models\tenderModels;

use app\models\tenderModels\BaseModel;
use app\models\tenderModels\Organization;

class Question extends BaseModel
{
    public $id;
    public $author;         // class Organization            
    public $title; 
    public $description;
    public $date;
    public $answer;
    public $questionOf;
    public $relatedItem;

    public function __construct($scenario='default')
    {
        $this->author = new Organization($scenario);
        parent::__construct($scenario);
    }

    public function rules()
    {
        return [
            ['id', 'safe'],
            ['title', 'required'],
            ['description', 'required'],
            ['description', 'string', 'min'=>30],
            ['date', 'safe'],
            ['answer', 'safe'],
            ['questionOf', 'safe'],
            ['relatedItem', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('app', 'Назва запитання'),
            'description' => \Yii::t('app', 'Опис запитання'),
            'date' => \Yii::t('app', 'Дата публiкацiї'),
            'answer' => \Yii::t('app', 'Вiдповiдь на задане питання'),
            'questionOf' => \Yii::t('app', 'Питання до'),
        ];
    }

    public static function getSellerQuestionOf($tender)
    {

        $items = [];
        $items['tender'] = \Yii::t('app', 'Тендеру');
        //отдает 500 если задавать вопросы к товару в тендераъ этого типа
        //@todo возможно это ошибка цбд и нужно будет проверить потом еще
        if (!in_array($tender->procurementMethod . '_' . $tender->procurementMethodType, \Yii::$app->params['2stage.tender'])) {
            foreach ($tender->items as $k => $val) {
                if ($k === '__EMPTY_ITEM__') continue;
                $items['item_' . $val['id']] = \Yii::t('app', 'Товару') . '-' . $val['description'];
            }
        }
        if (count($tender->lots)) {
            foreach ($tender->lots as $k => $val) {
                if ($k === '__EMPTY_LOT__') continue;
                if ($val['id']) {
                    $items['lot_' . $val['id']] = \Yii::t('app', 'Лоту') . '-' . $val['title'];
                }
            }
        }
        return $items;


    }
}
