<?php defined('SYSPATH') or die('No direct script access.');?>

<?if(core::count($ads)):?>
    <h3><?=_e('Related ads')?></h3>
    <?foreach($ads as $ad ):?>
        <div class="media">
            <div class="media-left">
                <a title="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
                    <?if($ad->get_first_image() !== NULL):?>
                        <img class="media-object" src="<?=Core::imagefly($ad->get_first_image(),64,64)?>" alt="<?= HTML::chars($ad->title)?>">
                    <?else:?>
                        <?if(( $icon_src = $ad->category->get_icon() )!==FALSE ):?>
                            <img src="<?=Core::imagefly($icon_src,64,64)?>" alt="<?=HTML::chars($ad->title);?>">
                        <?else:?>
                            <img data-src="holder.js/64x64?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->translate_name(), 'size' => 8, 'auto' => 'yes')))?>" alt="<?=HTML::chars($ad->title)?>">
                        <?endif?>
                    <?endif?>
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <?if($ad->featured >= Date::unix2mysql(time())):?>
                        <span class="label label-danger pull-right"><?=_e('Featured')?></span>
                    <?endif?>
                    <a title="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"> <?=$ad->title; ?></a>
                </h4>
                <p><?=Text::limit_chars(Text::removebbcode($ad->description), 255, NULL, TRUE);?>
                    <?if($ad->id_location != 1):?>
                        <a href="<?=Route::url('list',array('location'=>$ad->location->seoname))?>" title="<?=HTML::chars($ad->location->translate_name())?>">
                            <span class="label label-default"><?=$ad->location->translate_name()?></span>
                        </a>
                    <?endif?>
                </p>
            </div>
        </div>
    <?endforeach?>
<?endif?>
