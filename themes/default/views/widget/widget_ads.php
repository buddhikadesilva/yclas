<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->ads_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->ads_title?></h3>
    </div>
<?endif?>

<div id="widget_ads" class="panel-body">
    <ul>
        <?foreach($widget->ads as $ad):?>
            <li>
                <a href="<?=Route::url('ad',array('seotitle'=>$ad->seotitle,'category'=>$ad->category->seoname))?>" title="<?=HTML::chars($ad->title)?>">
                    <?=$ad->title?>
                </a>
            </li>
        <?endforeach?>
    </ul>
</div>