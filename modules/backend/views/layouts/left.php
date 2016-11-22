<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
<!--        <div class="user-panel">-->
<!--            <div class="pull-left image">-->
<!--                <img src="--><?//= $directoryAsset ?><!--/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>-->
<!--            </div>-->
<!--            <div class="pull-left info">-->
<!--                <p>Alexander Pierce</p>-->
<!---->
<!--                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
<!--            </div>-->
<!--        </div>-->

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">-->
<!--            <div class="input-group">-->
<!--                <input type="text" name="q" class="form-control" placeholder="Search..."/>-->
<!--                <span class="input-group-btn">-->
<!--                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                </button>-->
<!--              </span>-->
<!--            </div>-->
<!--        </form>-->
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'encodeLabels' => false,
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => '<span class="fa fa-tachometer"></span> '. Yii::t('app','Cabinet'), 'url' => ['/']],
                    //['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    //['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii']],
                    //['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug']],
                    //['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],

                    [
                        'label' => 'Компания',
                        'icon' => 'fa fa-th', //-share
                        'url' => '#',
                        'active' => (
                            Yii::$app->controller->id == 'companies' ||
                            Yii::$app->controller->id == 'company-type'
                        ),
                        'items' => [
                            ['label' => Yii::t('app', 'Companies'), 'icon' => 'fa fa-angle-double-right', 'url' => ['/backend/companies/index'], 'active' => (Yii::$app->controller->id == 'companies')],
                            ['label' => Yii::t('app', 'Company types'), 'icon' => 'fa fa-angle-double-right', 'url' => ['/backend/company-type/index'], 'active' => (Yii::$app->controller->id == 'company-type')],

                        ],
                    ],
                    ['label' => Yii::t('app', 'Document types'), 'url' => ['/backend/document-type/index']],
                    ['label' => Yii::t('app', 'Pages edit'), 'url' => ['/pages/pages-tree/index']],
                    ['label' => Yii::t('app', 'Menu edit'), 'url' => ['/backend/menu/']],
                    ['label' => Yii::t('app', 'Translate edit'), 'url' => ['/backend/translate/']],
                    ['label' => Yii::t('app', 'Country edit'), 'url' => ['/backend/countries/']],
                    ['label' => Yii::t('app', 'CountrySheme edit'), 'url' => ['/backend/country-sheme/']],
                    ['label' => Yii::t('app', 'Contracts edit'), 'url' => ['/backend/contracts-templates/']],
                    ['label' => Yii::t('app', 'Payments'), 'url' => ['/backend/payment/']],
                    ['label' => Yii::t('app', 'Invoices'), 'url' => ['/backend/invoice/']],
                    ['label' => Yii::t('app', 'Users'), 'url' => ['/backend/users/']],
//                    ['label' => Yii::t('app', 'bids'), 'url' => ['/backend/bids/']],
                    ['label' => Yii::t('app', 'Regions'), 'url' => ['/backend/regions/']],


//                    [
//                        'label' => 'Библиотеки',
//                        'icon' => 'fa fa-th', //-share
//                        'url' => '#',
//                        'items' => [
//                            ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'],],
//                            ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'],],
//                            [
//                                'label' => 'Level One',
//                                'icon' => 'fa fa-circle-o',
//                                'url' => '#',
//                                'items' => [
//                                    ['label' => 'Level Two', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                    [
//                                        'label' => 'Level Two',
//                                        'icon' => 'fa fa-circle-o',
//                                        'url' => '#',
//                                        'items' => [
//                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                            ['label' => 'Level Three', 'icon' => 'fa fa-circle-o', 'url' => '#',],
//                                        ],
//                                    ],
//                                ],
//                            ],
//                        ],
//                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
