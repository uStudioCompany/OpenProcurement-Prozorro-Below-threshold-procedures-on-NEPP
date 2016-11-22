<?php


?>
<div>
    <?php foreach ($items as $key => $item): ?>
        <?php $has_chield = ($item['children'] > 0); ?>
        
        <div id="<?= $item['id'] ?>" class="item <?= ($has_chield) ? 'has-chield' : ''?>" pid="<?= $item['pid'] ?>">
            <div class="item-name">
                <?php
                if ($has_chield) {
                    echo '<i class="fa fa-plus-square-o"></i>';
                }
                ?>
                <span class="item-id"><?= $item['id']?></span>
                <span class="item-descr"><?= $item['name'] ?></span>
            </div>
            <?php if ($item['id'] != '99999999-9') { ?>
                <div class="item-child" style="display: none;"></div>
            <?php } ?>
        </div>
    <?php endforeach; ?>
</div>