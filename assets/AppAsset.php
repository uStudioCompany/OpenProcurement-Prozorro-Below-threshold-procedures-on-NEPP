<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/language_flags.css',
//        'css/theme.css'


//        'css/css/app.css',
//        'css/css/search.css',
        'css/fix.css',
        'css/fix2.css',
        //'http://prozorroy.byustudio.in.ua/css/fix2.css',

    ];
    public $js = [
        'js/moment.js',
        'js/bootstrap-datetimepicker.js',
//        'js/project.js',
//        'js/ajaxupload.js',
        'js/jquery.uploadfile.js',
        'https://cdn2.prozorro.gov.ua/openprocurement-crypto/sign/js/index.js',
        'js/sign.js',
        'js/nav_block.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'sersid\fontawesome\Asset',
        'app\assets\BootboxAsset',
    ];
}
