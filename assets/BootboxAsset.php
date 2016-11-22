<?php
namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class BootboxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/bootbox';
    public $js = [
        'bootbox.js',
    ];

    public static function overrideSystemConfirm()
    {
        Yii::$app->view->registerJs('
            yii.confirm = function(message, ok, cancel) {
                bootbox.confirm({
                title: "Повідомлення", 
                message: message,
                buttons: {
                    confirm: {
                        className: "btn-success"
                    },
                    cancel: {
                        className: "btn-danger"
                    }
                },
                callback: function(result) {
                    if (result) { !ok || ok(); } else { !cancel || cancel(); }
                }});
            }
            bootbox.addLocale("uk", {
                  OK      : "OK",
                  CANCEL  : "Відміна",
                  CONFIRM : "Застосувати"
                });
            bootbox.setLocale("uk");
        ');
    }
}