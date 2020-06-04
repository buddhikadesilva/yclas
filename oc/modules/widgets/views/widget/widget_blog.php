<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->widget_title !=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->widget_title ?></h3>
    </div>
<?endif?>

<div class="panel-body">
    <ul>
        <?foreach($widget->posts as $post):?>
            <li>
                <a href="<?= Route::url('blog', array('seotitle' => $post->seotitle)) ?>" title="<?=HTML::chars($post->title)?>">
                    <?=$post->title?>
                </a>
            </li>
        <?endforeach?>
    </ul>
</div>
