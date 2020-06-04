<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->categories_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->categories_title?></h3>
    </div>
<?endif?>

<div id="widget_categories" class="panel-body">
    <?if($widget->cat_breadcrumb !== NULL):?>
        <h5>
            <p>
                <?if($widget->cat_breadcrumb['id_parent'] != 0):?>
                    <a href="<?=Route::url('list',array('category'=>$widget->cat_breadcrumb['parent_seoname'],'location'=>$widget->loc_seoname))?>" title="<?=HTML::chars($widget->cat_breadcrumb['parent_translate_name'])?>"><?=$widget->cat_breadcrumb['parent_translate_name']?></a> -
                    <?=$widget->cat_breadcrumb['translate_name']?>
                <?else:?>
                    <a href="<?=Route::url('list',array('category'=>$widget->cat_breadcrumb['parent_seoname'],'location'=>$widget->loc_seoname))?>" title="<?=HTML::chars($widget->cat_breadcrumb['parent_translate_name'])?>"><?=_e('Home')?></a> -
                    <?=$widget->cat_breadcrumb['translate_name']?>
                <?endif?>
            </p>
        </h5>
    <?endif?>
    <ul>
        <?foreach($widget->cat_items as $cat):?>
            <li>
                <a href="<?=Route::url('list',array('category'=>$cat->seoname,'location'=>$widget->loc_seoname))?>" title="<?=HTML::chars($cat->translate_name())?>">
                <?=$cat->translate_name()?></a>
            </li>
        <?endforeach?>
    </ul>
</div>