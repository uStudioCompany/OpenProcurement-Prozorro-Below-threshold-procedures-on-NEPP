
<div class="form-group">
    <h2><?=Yii::t('app','Додатковi контактнi особи') ?></h2>
    <div class="col-md-3"></div>
    <div class="col-md-6 additional_persons">
        <ul class="list-group" itemcount="<?=count($additionalContacts);?>">

            <?php  foreach ($additionalContacts as $k=> $additionalContact)  : ?>

            <li class="list-group-item"><?=$additionalContact['name']; ?>
                <button class="close remove_person" type="button">
                    <span aria-hidden="true">×</span>
                </button>
                <input type="hidden" value="<?=$additionalContact['name']; ?>" name="Tender[procuringEntity][additionalContactPoints][<?=$k;?>][name]">
                <input type="hidden" value="<?=$additionalContact['name_en']; ?>" name="Tender[procuringEntity][additionalContactPoints][<?=$k;?>][name_en]">
                <input type="hidden" value="<?=$additionalContact['availableLanguage']; ?>" name="Tender[procuringEntity][additionalContactPoints][<?=$k;?>][availableLanguage]">
                <input type="hidden" value="<?=$additionalContact['telephone']; ?>" name="Tender[procuringEntity][additionalContactPoints][<?=$k;?>][telephone]">
                <input type="hidden" value="<?=$additionalContact['url']; ?>" name="Tender[procuringEntity][additionalContactPoints][<?=$k;?>][url]">
            </li>

            <?php endforeach; ?>



        </ul>
    </div>
</div>

<?php
$persons = \app\models\Persons::find()->where(['company_id' => Yii::$app->user->identity->company_id])->asArray()->all();
?>
<div class="dropdown dropup eu_procedure" person_count="<?=count($persons);?>">
    <button class="btn btn-primary dropdown-toggle" type="button"
            data-toggle="dropdown"><?= Yii::t('app', 'View Persons') ?>
        <span class="caret"></span></button>
    <ul class="dropdown-menu">
        <?php
        foreach ($persons as $k => $v) {
            echo '<li><a class="add_contact_person" cid="' . $v['id'] . '" href="javascript:void(0)">&nbsp;&nbsp;&nbsp;&nbsp;' . htmlspecialchars($v['userSurname'] . ' ' . $v['userName'] . ' ' . $v['userPatronymic']) . ' ( ' . $v['availableLanguage'] . ' )</a></li>';
        }
        ?>

    </ul>
</div>