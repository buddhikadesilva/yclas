<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->text_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->text_title?></h3>
    </div>
<?endif?>

<div class="panel-body">
    <?if (!is_null($widget->info)):?>
        <p><?=$widget->info->views?> <strong><?=_e('views')?></strong></p>
        <p><?=$widget->info->ads?> <strong><?=_e('ads')?></strong></p>
        <p><?=$widget->info->users?> <strong><?=_e('users')?></strong></p>
    <?endif?>
</div>
