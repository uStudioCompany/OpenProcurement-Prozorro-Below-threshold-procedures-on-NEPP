<?php
use app\modules\pages\models\PagesTree;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var $models \app\modules\pages\models\PagesTree;
 */
$folderIcon = '<span class="glyphicon glyphicon-folder-open"></span>';
$folderCloseIcon = '<span class="glyphicon glyphicon-folder-close"></span>';
$fileIcon = '<span class="glyphicon glyphicon-file"></span>';
$editIcon = '<span class="glyphicon glyphicon-pencil"></span>';
$searchIcon = '<span class="glyphicon glyphicon-search"></span>';
$trashIcon = '<span class="glyphicon glyphicon-trash"></span>';
$addRoot = '<span class="glyphicon glyphicon-asterisk"></span>';
$this->title = Yii::t('app', 'Explorer');
$cookiesExpandedFolder = \yii\helpers\Json::decode($_COOKIE['pages_tree']);

/*if (Yii::$app->session->hasFlash('success')) { */ ?><!--
    <div class="alert alert-success fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?/*= Yii::$app->session->getFlash('success'); */ ?>
    </div>
--><?php /*}*/
if (Yii::$app->session->hasFlash('warning')) { ?>
    <div class="alert alert-warning fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <?= Yii::$app->session->getFlash('warning'); ?>
    </div>
<?php } ?>
<div class="panel-body">
    <?= Html::a(Yii::t('app', 'Search page') . ' ' . $searchIcon, ['manager/index'], ['class' => 'btn btn-success']); ?>
    <? if (!PagesTree::checkExistRoot($models)) : ?>
        <?= Html::a(Yii::t('app', 'Add root') . ' ' . $addRoot, ['add-root'], ['class' => 'btn btn-warning', 'data-confirm' => Yii::t('app', 'delete.root')]); ?>
    <? else : ?>
        <div class="pull-right">
            <?= Html::a(Yii::t('app', 'Create folder') . ' ' . $folderIcon, ['create-folder'], ['class' => 'btn btn-success']); ?>
            <?= Html::a(Yii::t('app', 'Create page') . ' ' . $fileIcon, ['manager/create'], ['class' => 'btn btn-success']); ?>
        </div>
    <? endif; ?>
</div>
<? Pjax::begin(['id' => 'ptree']); ?>
<table class="tree table borderless">
    <?php
    foreach ($models as $key => $model) {
        $parent = $key ? 'treegrid-parent-' . ($model->findRoot($models)['key'] + 1) : '';
        if (in_array($model->id, $cookiesExpandedFolder)) {
            $parent .= ' ' . 'expanded';
        }
        $index = $key + 1;
        if ($model->icon == PagesTree::FOLDER) {
            $folder = true;
            $viewURL = '#';
            $updateURL = Html::a($editIcon, ['update-folder', 'id' => $model->id]);
            $viewIcon = $folderCloseIcon;
            $createFolder = Html::a($folderIcon, ['create-folder', 'pt_id' => $model->id]);
            $createPage = Html::a($fileIcon, ['/pages/manager/create', 'pt_id' => $model->id], ['target' => '_blank']);
            $delete = Html::a($trashIcon, ['delete-folder', 'id' => $model->id], ['target' => '_blank', 'data-confirm' => Yii::t('app', 'delete.node')]);
        } else {
            $folder = false;
            $viewURL = Url::base() . '/pages/' . @$model->pages->alias;
            $viewIcon = $fileIcon;
            $updateURL = Html::a($editIcon, Url::base() . '/pages/manager/update/' . $model->pages->id, ['target' => '_blank']);
            $createFolder = '';
            $createPage = '';
            $delete = Html::a($trashIcon, ['/pages/manager/delete', 'id' => $model->pages->id], ['data-method' => 'post', 'data-confirm' => Yii::t('app', 'delete.page')]);
        }
        $title = $viewIcon . ' ' . Html::encode($model->name);
        ?>
        <tr class="treegrid-<?= $index . ' ' . $parent ?>" id="<?= ($folder ? 'folder' : 'file') . '-' . $model->id ?>">
            <td>&emsp;<?= $folder || !@$model->pages->published ? $title : Html::a($title, $viewURL, ['target' => '_blank']) ?></td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown"
                            id="h20">
                        <span class="caret" id="forcaret"></span>
                    </button>
                    <ul class="dropdown-menu list-inline" role="menu">
                        <li data-toggle="tooltip" title="<?= Yii::t('app', 'Update') ?>"
                            class="green-tooltip"><?= $updateURL ?></li>
                        <? if ($createFolder != '') : ?>
                            <li data-toggle="tooltip" title="<?= Yii::t('app', 'Create folder') ?>"
                                class="green-tooltip"><?= $createFolder ?></li>
                        <? endif; ?>

                        <? if ($createPage != '') : ?>
                            <li data-toggle="tooltip" data-placement="bottom"
                                title="<?= Yii::t('app', 'Create page') ?>"
                                class="green-tooltip"><?= $createPage ?></li>
                        <? endif; ?>
                        <li data-toggle="tooltip" data-placement="bottom" title="<?= Yii::t('app', 'Delete') ?>"
                            class="green-tooltip"><?= $delete ?></li>
                    </ul>
                </div>
            </td>
        </tr>
        <?
    }
    ?>
</table>
<? $this->registerJS('
$(\'.tree\').treegrid({
        enableMove: true,
        onMoveOver: function (item, helper, target, position) {
            return CheckMoveOver(target, item);
        },
        onMoveStop: function (item, helper) {
            MoveNode(item);
        },
        onMoveStart: function (item, helper) {
            if (item.hasClass(\'treegrid-1\')) {
                return false;
            }
            return true;
        }
    });
    $(\'[data-toggle="tooltip"]\').tooltip();
    $(\'body\').on(\'click\', \'.tree\', function () {
        getExpandedFolder();
    });
    ') ?>
<? Pjax::end(); ?>
<?php
$this->registerJSFile(Url::to('@web/js/jquery.treegrid.js'), ['position' => yii\web\View::POS_END, 'depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile(Url::to('@web/css/jquery.treegrid.css'));
