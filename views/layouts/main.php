<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use app\assets\BootboxAsset;
use app\assets\AppAsset;


AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="wrap">
        <?php
        $items = \app\modules\backend\models\Menu::getMenu();

//        if (@Yii::$app->user->identity->status == \app\models\User::STATUS_ADMIN) {
//            $items[] = [
//                'label' => 'BackEnd',
//                'items' => [
////                    ['label' => 'BackEnd', 'url' => ['/backend']],
////                    '<li class="divider"></li>',
////                    '<li class="dropdown-header">' . Yii::t('app', 'Dictionaries') . '</li>',
//                    ['label' => Yii::t('app', 'Companies'), 'url' => ['/backend/companies/index']],
//                    ['label' => Yii::t('app', 'Company types'), 'url' => ['/backend/company-type/index']],
//                    ['label' => Yii::t('app', 'Document types'), 'url' => ['/backend/document-type/index']],
//                    ['label' => Yii::t('app', 'Pages edit'), 'url' => ['/pages/manager']],
//                    ['label' => Yii::t('app', 'Menu edit'), 'url' => ['/backend/menu/']],
//                    ['label' => Yii::t('app', 'Translate edit'), 'url' => ['/backend/translate/']],
//                    ['label' => Yii::t('app', 'Country edit'), 'url' => ['/backend/countries/']],
//                    ['label' => Yii::t('app', 'CountrySheme edit'), 'url' => ['/backend/country-sheme/']],
//                    ['label' => Yii::t('app', 'Contracts edit'), 'url' => ['/backend/contracts-templates/']],
//                    ['label' => Yii::t('app', 'Payments'), 'url' => ['/backend/payment/']],
//                    ['label' => Yii::t('app', 'Users'), 'url' => ['/backend/users/']],
////                    '<li class="divider"></li>',
////                    '<li class="dropdown-header">Other</li>',
////                    ['label' => 'Other', 'url' => ['/backend']],
//                ]
//            ];
//        }

        if (@Yii::$app->user->identity->status == \app\models\User::STATUS_ADMIN) { // админка
            $items[] = ['label' => Yii::t('app', 'BackEnd'), 'url' => ['/backend/companies']];
        }

        if (Yii::$app->user->isGuest) {
//            $items[] = ['label' => 'Вход', 'url' => ['/site/login']];
            $items[] = ['label' => Yii::t('app','Register'), 'url' => ['/register/']];
        } else {
            $items[] = [
                'label' => Yii::t('app', 'Logout').' (' . Yii::$app->user->identity->username . ')',
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post']
            ];
        }

        $items[] = \lajax\languagepicker\widgets\LanguagePicker::widget([
            'skin' => \lajax\languagepicker\widgets\LanguagePicker::SKIN_BUTTON,
            'size' => \lajax\languagepicker\widgets\LanguagePicker::SIZE_SMALL]);

        NavBar::begin([
            'brandLabel' => '<div class="m_logo"><img class="img-responsive center-block" width="150" src="'. Yii::$app->homeUrl.'img/prozorro-logo.png'.'" alt="PROZORRO LOGO"></div>',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $items,
        ]);
        NavBar::end();

//                \app\models\Notifications::AddEventToCabinet();
        ?>

        <div class="container">
            <?php
            if(!Yii::$app->user->isGuest && Yii::$app->controller->module->id != 'backend'){
                echo $this->render('../cabinet/buttons_block');
            }
            ?>
            <?=''// Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]) ?>

            <?php
            \app\components\ApiHelper::cdbAvailableControl();
            \app\models\Companies::companyActiveControl();
            if (Yii::$app->session->hasFlash('cdbAvailable') || Yii::$app->session->hasFlash('errorCompanyActive')): ?>
                <?php
                $flashType = (Yii::$app->session->hasFlash('cdbAvailable'))? 'cdbAvailable' : 'errorCompanyActive';
                echo \yii\bootstrap\Alert::widget([
                    'options' => [
                        'class' => 'alert-danger'
                    ],
                    'body' => Yii::$app->session->getFlash($flashType)
                ]);
                ?>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; 2015-2016</p>

        </div>
    </footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>