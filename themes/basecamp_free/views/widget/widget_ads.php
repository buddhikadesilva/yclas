<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->ads_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->ads_title?></h3>
    </div>
<?endif?>

<div class="panel-body">
    <ul class="list-unstyled">
        <?foreach($widget->ads as $ad):?>
            <li>
                <i class="fa fa-angle-double-right"></i> <a href="<?=Route::url('ad',array('seotitle'=>$ad->seotitle,'category'=>$ad->category->seoname))?>" title="<?=HTML::chars($ad->title)?>">
                    <?=$ad->title?>
                </a>
            </li>
        <?endforeach?>
    </ul>
</div>