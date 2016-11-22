<?php
use yii\helpers\Html;
use app\models\Companies;

$companyBusinesType = Companies::getCompanyBusinesType();
$companyFinanceStatus = Companies::getStatus(Yii::$app->user->identity->company_id);
$cookies = Yii::$app->request->cookies;
$balance = Yii::$app->finance->refreshBalance( Yii::$app->user->identity->company_id, true );

if (\app\components\HTender::checkMode()) { ?>
    <div class="alert alert-danger" role="alert"><?= Yii::t('app', 'Тестовий режим') ?></div>
<?php } ?>
<div class="row margin_b_20">
    <div class="col-md-2">
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button"
                    data-toggle="dropdown"><?= Yii::t('app', 'Моя компанiя') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><?= Html::a('<span class="glyphicon glyphicon-user"></span> ' . Yii::t('app', 'View Persons'), ['/' . $companyBusinesType . '/persons/']) ?></li>
                <li><?= Html::a('<span class="glyphicon glyphicon-envelope"></span> ' . Yii::t('app', 'View Invite'), ['/' . $companyBusinesType . '/invite/']) ?></li>
                <!--                <li role="separator" class="divider"></li>-->
                <li><?= Html::a('<span class="glyphicon glyphicon-info-sign"></span> ' . Yii::t('app', 'View Company'), ['/' . $companyBusinesType . '/companies/view/']) ?></li>
            </ul>
        </div>
    </div>


    <div class="col-md-2">
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button"
                    data-toggle="dropdown"><?= Yii::t('app', 'Мої пропозиції') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><?= Html::a('<span class="glyphicon glyphicon-open"></span> ' . Yii::t('app', 'View Tenders'), ['/' . $companyBusinesType . '/tenders/index']); ?></li>
                <li><?= Html::a('<span class="glyphicon glyphicon-export"></span> ' . Yii::t('app', 'View Plans'), ['/' . $companyBusinesType . '/plan/']) ?></li>
                <li><?= Html::a('<span class="glyphicon glyphicon-list-alt"></span> ' . ($companyBusinesType == 'seller' ? Yii::t('app', 'View my tenders contracts') : Yii::t('app', 'View Contracts')), ['/' . $companyBusinesType . '/contracting/']) ?></li>
            </ul>
        </div>
    </div>

    <div class="col-md-2">
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button"
                    data-toggle="dropdown"><?= Yii::t('app', 'My finances') ?>
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <?php
                    if ($companyFinanceStatus != Companies::STATUS_BLOCKED) { // если заблокирован
                    echo '<li>' . Html::a('<span class="glyphicon glyphicon-list-alt"></span> ' . Yii::t('app', 'Generate Contract'), ['/' . $companyBusinesType . '/cabinet/contract/']) . '</li>';
                }
                ?>
                <?php if ($companyBusinesType == 'seller'): ?>
                    <?php if ($companyFinanceStatus == 1): ?>
                        <?= '<li>' . Html::a('<span class="glyphicon glyphicon-th-list"></span> ' . Yii::t('app', 'Invoices'), ['/' . $companyBusinesType . '/cash-flow/view-invoices']) . '</li>' ?>
                        <?= '<li>' . Html::a('<span class="glyphicon glyphicon-transfer"></span> ' . Yii::t('app', 'Payments'), ['/' . $companyBusinesType . '/cash-flow/']) . '</li>' ?>
                    <?php endif; ?>
                <?php endif; ?>

            </ul>
        </div>
    </div>
    <?php if ($companyFinanceStatus != Companies::STATUS_ACCEPTED && $companyBusinesType == 'seller') {?>
        <div class="col-md-4">

            <?php echo Html::a(Yii::t('app', 'Financial a uthorization of company'), ['/seller/cash-flow/create-invoice?isBackground=true'], ['class' => 'btn btn-success', 'target' => '_blank']);?>
        </div>
        <?php } ?>

    <div class="col-md-6">
        <?php if ($companyBusinesType == 'seller') {
            echo Yii::t('app', 'Баланс компанiї складає ') . $balance . ' гривень';
        }
        ?>
    </div>


</div>