<div class="content" style="page-break-before: always">
    <div class="h1 c b">Додаткова угода про змiни до договору № <span><?= $contractNum ?></span>
    </div>
    <br><br>
    <p>м. Київ</p>
    <p class="tr">«<span><?= Yii::$app->formatter->asDateTime($create_at, 'php:d') ?></span>»
        <span><?= Yii::$app->formatter->asDateTime($create_at, 'php:F') ?></span> <span
            var="user cdate year"><?= Yii::$app->formatter->asDateTime($create_at, 'php:Y') ?></span> року</p>
    <br>
    <p>Товариство з обмеженою відповідальністю «Держзакупівлі.Онлайн» (далі - Оператор) в особі директора Кольги
        Василя Володимировича, який діє на підставі Статуту товариства, з однієї сторони, та <span
            var="user procuringEntity_name"><?= $legalName ?></span>, в особі <span
            var="user direction_status"><?= $userPosition ?></span>
        <span var="user direction_name" class="u1"><?= $fio ?></span>, який діє на підставі
        <span var="user direction_doc" class="u1"><?= $userDirectionDoc ?></span> (далі - Користувач), з
        іншої сторони, іншої сторони, уклали цю Додаткову угоду про зміни Договору № <?= $contractNum ?> від
        «<?= Yii::$app->formatter->asDateTime($contractDate, 'php:d') ?>» <?= Yii::$app->formatter->asDateTime($contractDate, 'php:F') ?> <?= Yii::$app->formatter->asDateTime($contractDate, 'php:Y') ?> року про нижче наведене:</p>

    <p><?= $changes ?></p>
    <br>
    <p class="c b full">РЕКВІЗИТИ СТОРІН</p>
    <br>
    <table cellpadding="0" cellspacing="0" width="100%%">
        <tr>
            <td style="width:48%%;vertical-align: top;">
                <p><br>ТОВ "Держзакупівлі.Онлайн"<br>
                    ЄДРПОУ: 39008321<br><span class="b">Юридична адpeса:</span><br>
                    Київ, вул. Воздвиженська, 10 «Б», оф. 23, 04071<br><span class="b">Фактична адpeса (для надсилання договорів):</span><br>
                    Київ, вул. Воздвиженська, 10 «Б», оф. 23, 04071<br><br>
                    р/р: 26008052738189<br>
                    МФО: 300711<br>
                    ПЕЧЕРСЬКА Ф.ПАТ КБ"ПРИВАТБАНК", М.КИЇВ<br><br>
                    Платник єдиного податку III групи за ставкою 5%%<br><br>
                    Диpeктор: Кольга Василь Володимирович</p>
            </td>
            <td></td>
            <td style="width:48%%;vertical-align: top;">
                <p><br><span var="user procuringEntity_name"></span><br>
                    ЄДРПОУ: <span><?=$identifier?></span><br>
                    в особі <span><?= $userPosition ?></span> <span class="u1"><?= $fio ?></span>,<br>
                    який(яка) діє на підставі <span class="u1"><?= $userDirectionDoc ?></span><br><br><span
                        class="b">Юридична адpeса:</span><br>
<!--                    <span var="user procuringEntity_address_locality">--><?//= $countryName ?><!--</span>,-->
                    <span var="user procuringEntity_address_streetAddress"><?= $locality ?></span>,
                    <span var="user procuringEntity_address_streetAddress"><?= $streetAddress ?></span>,
                    <span var="user procuringEntity_address_postalCode"><?= $postalCode ?></span>
                    <br><br>
                    р/р: __________________<br>
                    МФО: __________________<br>
                    Банк: _________________<br><br></p>
            </td>
        </tr>
    </table>
</div>

