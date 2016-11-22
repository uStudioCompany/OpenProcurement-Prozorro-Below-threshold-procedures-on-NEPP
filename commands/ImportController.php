<?php

namespace app\commands;

use yii;
use yii\console\Controller;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImportController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        echo "import/unit\n";
        echo "import/cpv\n";
        echo "import/dkpp\n";
        echo "import/mapping\n";
    }

    public function actionUnit()
    {
        $url    =  Yii::$app->params['urlUnit'];
        $model  = new \app\models\Unit();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionCpv()
    {
        $url    =  Yii::$app->params['urlCpv'];
        $model  = new \app\models\Cpv();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionDkpp()
    {
        $url    =  Yii::$app->params['urlDkpp'];
        $model  = new \app\models\Dkpp();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionKekv()
    {
        $url    =  Yii::$app->params['urlKekv'];
        $model  = new \app\models\Kekv();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionDk015()//ДК015:97 - Класифікація видів науково-технічної діяльності
    {
        $url    =  Yii::$app->params['urlDk015'];
        $model  = new \app\models\Dk015();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionDk018()//ДК018:2000 - Державний класифікатор будівель та споруд
    {
        $url    =  Yii::$app->params['urlDk018'];
        $model  = new \app\models\Dk018();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionDk003()//ДК003:2010 - Класифікатор професій
    {
        $url    =  Yii::$app->params['urlDk003'];
        $model  = new \app\models\Dk003();
        $params = $model->helperLoadCodes($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionMapping()
    {
        $url    =  Yii::$app->params['urlMap'];
        $model  = new \app\models\Cpv();
        $params = $model->helperLoadCodesMapping($url);

        echo 'Updated:'. $model->helperSaveCodesMapping($params) .' rows';
    }

    public function actionDocType()
    {
        $url    =  Yii::$app->params['urlDocType'];
        $model  = new \app\models\DocumentType();
        $params = $model->helperLoadCodesAll($url);

        echo 'Updated:'. $model->helperSaveCodes($params) .' rows';
    }

    public function actionFull()
    {
        echo 'Unit: ';  $this->actionUnit();    echo "\n";
        echo 'CPV:  ';  $this->actionCpv();     echo "\n";
        echo 'DKPP: ';  $this->actionDkpp();    echo "\n";
        echo 'DK003:';  $this->actionDk003();   echo "\n";
        echo 'DK015:';  $this->actionDk015();   echo "\n";
        echo 'DK018:';  $this->actionDk018();   echo "\n";
        echo 'Kekv: ';  $this->actionKekv();    echo "\n";
        echo 'Map:  ';  $this->actionMapping(); echo "\n";
        echo 'DTyp: ';  $this->actionDocType(); echo "\n";
    }
}

/*$sql = '';
        //$tmp = Yii::$app->opAPI->getUnit();
        foreach ($tmp['uk'] as $key=>$item) {
            $sql .= "INSERT INTO `unit` (`id`, `code`, `name`, `symbol`) VALUES (NULL, '$key', '$item->name_uk', '$item->symbol_uk');\n";
        }
        foreach ($tmp['ru'] as $key=>$item) {
            $sql .= "UPDATE  `unit` SET  `name_ru` =  '$item->name_ru', `symbol_ru` =  '$item->symbol_ru' WHERE  `unit`.`code` ='$key';\n";
        }
        foreach ($tmp['en'] as $key=>$item) {
            $sql .= "UPDATE  `unit` SET  `name_en` =  '$item->name_en', `symbol_en` =  '$item->symbol_en' WHERE  `unit`.`code` ='$key';\n";
        }
        Yii::$app->db->createCommand($sql)->execute();
        */