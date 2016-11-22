<?php


namespace app\modules\backend\assets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BackendAsset extends AppAsset
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/app.css',
        'css/site.css',
        'css/language_flags.css',
//        'css/theme.css'
    ];
    public $js = [
        'js/moment.js',
        'js/bootstrap-datetimepicker.js',
//        'js/project.js',
//        'js/ajaxupload.js',
//        'js/jquery.uploadfile.js',
//        'https://cdn.rawgit.com/openprocurement-crypto/common/v.0.0.11/js/index.js',
//        'js/sign.js',
//        'js/nav_block.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'sersid\fontawesome\Asset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\bootstrap\BootstrapThemeAsset',
        'sersid\fontawesome\Asset',
    ];
}
