<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->locations_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->locations_title?></h3>
    </div>
<?endif?>

<div id="widget_locations" class="panel-body">
    <?if($widget->loc_breadcrumb !== NULL):?>
        <h5>
            <p>
                <?if($widget->loc_breadcrumb['id_parent'] != 0):?>
                    <a href="<?=Route::url('list',array('location'=>$widget->loc_breadcrumb['parent_seoname'],'category'=>$widget->cat_seoname))?>" title="<?=HTML::chars($widget->loc_breadcrumb['parent_translate_name'])?>"><?=$widget->loc_breadcrumb['parent_translate_name']?></a> -
                        <?=$widget->loc_breadcrumb['translate_name']?>
                <?else:?>
                    <a href="<?=Route::url('list',array('location'=>$widget->loc_breadcrumb['parent_seoname'],'category'=>$widget->cat_seoname))?>" title="<?=HTML::chars($widget->loc_breadcrumb['parent_translate_name'])?>"><?=_e('Home')?></a> -
                    <?if($widget->loc_breadcrumb['id'] != 1):?>
                        <?=$widget->loc_breadcrumb['translate_name']?>
                    <?endif?>
                <?endif?>
            </p>
        </h5>
    <?endif?>
    <ul>
        <?foreach($widget->loc_items as $loc):?>
            <li>
                <a href="<?=Route::url('list',array('location'=>$loc->seoname,'category'=>$widget->cat_seoname))?>" title="<?=HTML::chars($loc->translate_name())?>">
                <?=$loc->translate_name()?></a>
            </li>
        <?endforeach?>
    </ul>
</div>