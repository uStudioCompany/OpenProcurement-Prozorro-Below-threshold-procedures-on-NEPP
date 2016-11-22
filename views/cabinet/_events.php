<?php
use yii\helpers\Html;

?>
<div class="tenders-index m_viewlist-wrap">

    <?php
    if ($user == 'buyer') {
        $tenderEvents = \yii\helpers\Json::decode($data['events'], true);
    }
    if ($user == 'seller') {
        $tenderEvents = $data;
    }
    foreach ($tenderEvents as $events) {
        if ($events['protokol'] != null) {
            ?>
            <div class="panel panel-default">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="panel-body">
                            <h3><?= Yii::t('app', 'Визнано переможця закупiвлi') . ': ' . Html::encode($events['tender']['title']) ?></h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="container">
                            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/protokol', 'id' => $events['tender']['tid']]) ?>"
                               class="btn btn-success"
                               onclick='readEvent("protokol", "<?= $events["tender"]["tid"] ?>", "<?= Yii::$app->user->identity->id ?>")'
                               role="button"><?= Yii::t('app', 'detail') ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='clearfix'></div>
            <?php
            continue;
        }
        if ($events['unsuccessful'] != null) {
            foreach ($events['unsuccessful'] as $unsuccessful) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Ваша ставка не допущена до аукціону') ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Тендер') . ': ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Лот') . ': ' . Html::encode($unsuccessful['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/euprequalification', 'id' => $events['tender']['tid']]) ?>"
                                   class="btn btn-success"
                                   onclick='readEvent("prequal_unsuc", "<?= $unsuccessful['bidId'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                   role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
                <?php
            }
        }
        if ($events['disqualification'] != null) {
            foreach ($events['disqualification'] as $status => $awardId) {
                if ($status == 'unsuccessful') {
                    $message = Yii::t('app', 'Ваша ставка дискваліфікована');
                } elseif ($status == 'pending') {
                    $message = Yii::t('app', 'Ваша ставка в періоді очікуваня');
                } elseif ($status == 'cancelled') {
                    $message = Yii::t('app', 'Ваша ставка відмінена');
                } elseif ($status == 'active') {
                    $message = Yii::t('app', 'Переможець');
                } else {
                    break;
                }
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= $message ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Тендер') . ': ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/award', 'id' => $events['tender']['tid']]) ?>"
                                   class="btn btn-success"
                                   onclick='readEvent("disqualification", "<?= $awardId ?>", "<?= Yii::$app->user->identity->id ?>")'
                                   role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
                <?php
            }
        }
        if ($events['activate'] != null) {
            foreach ($events['activate'] as $activate) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Ваша ставка допущена до аукціону') ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Тендер') . ': ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Лот') . ': ' . Html::encode($activate['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/euprequalification', 'id' => $events['tender']['tid']]) ?>"
                                   class="btn btn-success"
                                   onclick='readEvent("prequal_activate", "<?= $activate['bidId'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                   role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
                <?php
            }
        }
        if ($events['cancel'] != null) {
            foreach ($events['cancel'] as $cancel) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Ви робили ставку, але лот/тендер скасовано') ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Тендер') . ': ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/view', 'id' => $events['tender']['tid']]) ?>"
                                   class="btn btn-success"
                                   onclick='readEvent("cancel", "<?= $cancel['bidId'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                   role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
                <?php
            }
            continue;
        }
        if ($events['tender']['status'] == 'cancelled') {
            $tenderStatus = 0;
        } else {
            $tenderStatus = 1;
        }
        if (isset($events['questions'])) {
            foreach ($events['questions'] as $question) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Запитання до закупiвлi') . ' ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3>
                                    <?php
                                    if ($question['questionOf'] == 'lot') {
                                        $lotModel = \app\models\tenderModels\Lot::getLotById($events['tender'], $question['relatedItem']);
                                        if ($lotModel['status'] == 'cancelled') {
                                            $lotStatus = 0;
                                        } else {
                                            $lotStatus = 1;
                                        }
                                        echo Yii::t('app', 'Відноситься до лоту') . ': ' . $lotModel['title'];
                                    } else if ($question['questionOf'] == 'item') {
                                        $itemModel = \app\models\tenderModels\Item::getItemById($events['tender'], $question['relatedItem'], 'array');
                                        echo Yii::t('app', 'Відноситься до товару') . ': ' . $itemModel['description'];
                                    }
                                    ?>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Заголовок') . ': ' . Html::encode($question['title']) ?></h3>
                            </div>
                            <?php if ($user == 'seller') { ?>
                                <div class="panel-body">
                                    <h4><?= ($tenderStatus ? ((isset($lotStatus) && !$lotStatus) ? Yii::t('app', 'Лот скасовано') : Yii::t('app', 'Надано відповідь')) : Yii::t('app', 'Закупівлю скасовано')) ?></h4>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <h5><?= Yii::$app->formatter->asDatetime($question['date']) ?></h5>
                                <?php if ($user == 'seller') { ?>
                                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/questions', 'id' => $events['tender']['tid']]) ?>"
                                       class="btn btn-success"
                                       onclick='readEvent("question", "<?= $question['id'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                       role="button"><?= Yii::t('app', 'detail') ?></a>
                                <?php } else { ?>
                                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/questions', 'id' => $events['tender']['tid']]) ?>"
                                       class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
                <?php
            }
        }
        if (isset($events['complaints'])) {
            foreach ($events['complaints'] as $complaint) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', $complaint['type']) . ' ' . Yii::t('app', 'до закупiвлi') . ' ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3>
                                    <?php
                                    if (isset($complaint['relatedLot'])) {
                                        $lotModel = \app\models\tenderModels\Lot::getLotById($events['tender'], $complaint['relatedLot']);
                                        if ($lotModel['status'] == 'cancelled') {
                                            $lotStatus = 0;
                                        } else {
                                            $lotStatus = 1;
                                        }
                                        echo Yii::t('app', 'Відноситься до лоту') . ': ' . $lotModel['title'];
                                    }
                                    ?>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Заголовок') . ': ' . Html::encode($complaint['title']) ?></h3>
                            </div>
                            <?php if ($user == 'seller') { ?>
                                <div class="panel-body">
                                    <h4><?= ($tenderStatus ? ((isset($lotStatus) && !$lotStatus) ? Yii::t('app', 'Лот скасовано') : Yii::t('app', 'Надано відповідь')) : Yii::t('app', 'Закупівлю скасовано')) ?></h4>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <h5><?= Yii::$app->formatter->asDatetime($complaint['date']) ?></h5>
                                <?php if ($user == 'seller') { ?>
                                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/complaints', 'id' => $events['tender']['tid']]) ?>"
                                       class="btn btn-success"
                                       onclick='readEvent("complaint", "<?= $complaint['id'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                       role="button"><?= Yii::t('app', 'detail') ?></a>
                                <?php } else { ?>
                                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/complaints', 'id' => $events['tender']['tid']]) ?>"
                                       class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
            <?php }
        }
        if (isset($events['qualifications']['complaints'])) {
            foreach ($events['qualifications']['complaints'] as $complaint) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', $complaint['type']) . ' ' . Yii::t('app', 'до закупiвлi') . ' ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3>
                                    <?php
                                    if (isset($complaint['relatedLot'])) {
                                        $lotModel = \app\models\tenderModels\Lot::getLotById($events['tender'], $complaint['relatedLot']);
                                        echo Yii::t('app', 'Відноситься до лоту') . ': ' . $lotModel['title'];
                                    }
                                    ?>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Заголовок') . ': ' . Html::encode($complaint['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <h5><?= Yii::$app->formatter->asDatetime($complaint['date']) ?></h5>
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/prequalification-complaints', 'id' => $events['tender']['tid'], 'prequalification' => '']) ?>"
                                   class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
            <?php }
        }
        if (isset($events['awards']['complaints'])) {
            foreach ($events['awards']['complaints'] as $complaint) {
                ?>
                <div class="panel panel-default">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="panel-body">
                                <h3><?= Yii::t('app', $complaint['type']) . ' ' . Yii::t('app', 'до закупiвлi') . ' ' . Html::encode($events['tender']['title']) ?></h3>
                            </div>
                            <div class="panel-body">
                                <h3>
                                    <?php
                                    if (isset($complaint['relatedLot'])) {
                                        $lotModel = \app\models\tenderModels\Lot::getLotById($events['tender'], $complaint['relatedLot']);
                                        echo Yii::t('app', 'Відноситься до лоту') . ': ' . $lotModel['title'];
                                    }
                                    ?>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <h3><?= Yii::t('app', 'Заголовок') . ': ' . Html::encode($complaint['title']) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="container">
                                <h5><?= Yii::$app->formatter->asDatetime($complaint['date']) ?></h5>
                                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/qualification-complaints', 'id' => $events['tender']['tid'], 'qualification' => $complaint['award_id']]) ?>"
                                   class="btn btn-success" role="button"><?= Yii::t('app', 'detail') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='clearfix'></div>
            <?php }
        }
        if (($user == 'seller') && isset($events['awards'])) {
            foreach ($events['awards'] as $award) {
                foreach ($award['complaints'] as $complaint) {
                    ?>
                    <div class="panel panel-default">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="panel-body">
                                    <h3><?= Yii::t('app', $complaint['type']) . ' ' . Yii::t('app', 'на кваліфікацію переможця') ?></h3>
                                </div>
                                <div class="panel-body">
                                    <h3>
                                        <?= Yii::t('app', 'Закупівля') . Html::encode($events['tender']['title']) ?>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <h3><?= Yii::t('app', 'Заголовок') . ': ' . Html::encode($complaint['title']) ?></h3>
                                </div>
                                <div class="panel-body">
                                    <h4><?= ($tenderStatus ? Yii::t('app', 'Надано відповідь') : Yii::t('app', 'Закупівлю скасовано')) ?></h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="container">
                                    <h5><?= Yii::$app->formatter->asDatetime($complaint['date']) ?></h5>
                                    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/' . Yii::$app->session->get('businesType') . '/tender/qualification-complaints', 'id' => $events['tender']['tid'], 'qualification' => key($award)]) ?>"
                                       class="btn btn-success"
                                       onclick='readEvent("award", "<?= $complaint['id'] ?>", "<?= Yii::$app->user->identity->id ?>")'
                                       role="button"><?= Yii::t('app', 'detail') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='clearfix'></div>
                <?php }
            }
        }
    } ?>
</div>
