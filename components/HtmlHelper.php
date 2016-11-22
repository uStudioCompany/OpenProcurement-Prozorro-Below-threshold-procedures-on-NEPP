<?php
namespace app\components;

use Yii;

class HtmlHelper
{
    public static function printErr($msg) {
        return '
            <div class="bs-example">
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>'.
                    $msg .'
                </div>
            </div>';
    }

    //public static function printFeatu($msg) {

}