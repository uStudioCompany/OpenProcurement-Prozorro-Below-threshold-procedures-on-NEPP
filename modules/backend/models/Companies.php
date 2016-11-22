<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "company_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $name_en
 * @property string $name_ru
 * @property string $full_name
 * @property string $full_name_en
 * @property string $full_name_ru
 *
 * @property Companies[] $companies
 */
class Companies extends \app\models\Companies
{



    public function rules()
    {
        return parent::rules();
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attr_labels = parent::attributeLabels();
        $attr_labels['fio'] = Yii::t('app', 'Full name');
        $attr_labels['userPosition'] = Yii::t('app', 'Position');
        $attr_labels['userDirectionDoc'] = Yii::t('app', 'Direction document');

        return $attr_labels;
    }

    /**
     * @param $status
     * @throws \Exception
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->update(false);
    }


}
