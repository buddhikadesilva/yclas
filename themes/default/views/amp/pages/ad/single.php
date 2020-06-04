<?if ($ad->status != Model_Ad::STATUS_PUBLISHED && $permission === FALSE && ($ad->id_user != $user)):?>
    <h1 class="amp-oc-title"><?= __('This advertisement doesn´t exist, or is not yet published!')?></h1>
<?else:?>
    <h1 class="amp-oc-title"><?=$ad->title?></h1>
    <ul class="amp-oc-meta">
        <?if ($ad->price>0):?>
            <li><?=i18n::money_format($ad->price, $ad->currency())?></li>
        <?elseif ($ad->price==0 AND core::config('advertisement.free')==1):?>
            <li><?=_e('Free')?></li>
        <?endif?>
        <li>
            <a href="<?=Route::url('profile', ['seoname' => $ad->user->seoname])?>"><?=$ad->user->name?></a>
        </li>
        <li>
            <a href="<?=Route::url('list',array('category'=>$ad->category->seoname))?>">
                <?=$ad->category->name?>
            </a>
        </li>
        <?if ($ad->id_location != 1):?>
            <li>
                <a href="<?=Route::url('list',array('location'=>$ad->location->seoname))?>">
                    <?=$ad->location->name?>
                </a>
            </li>
        <?endif?>
        <li>
            <?=Date::format($ad->published, core::config('general.date_format'))?>
        </li>
        <?if(core::config('advertisement.count_visits')==1):?>
            <li><?=$hits?> <?=_e('Hits')?></li>
        <?endif?>
    </ul>

    <?$images = $ad->get_images()?>
    <?if($images):?>
        <amp-carousel width="400"
            height="300"
            layout="responsive"
            type="slides">
            <?foreach ($images as $path => $value):?>
                <amp-img src="<?=Core::imagefly($value['image'],400,300)?>"
                    width="400"
                    height="300"
                    layout="responsive">
                </amp-img>
            <?endforeach?>
        </amp-carousel>
    <?endif?>

    <?if(core::config('advertisement.description')!=FALSE):?>
        <p><?=Text::removebbcode($ad->description)?></p>
    <?endif?>
    <?if (Valid::url($ad->website)):?>
        <p><a href="<?=$ad->website?>" rel="nofollow" target="_blank">><?=$ad->website?></a></p>
    <?endif?>
    <?if (core::config('advertisement.address')):?>
        <p><?=$ad->address?></p>
    <?endif?>

    <?if (core::count($cf_list) > 0) :?>
        <ul class="amp-oc-cf">
            <?foreach ($cf_list as $name => $value):?>
                <?if($value=='checkbox_1'):?>
                    <li><b><?=$name?></b>: &#10003;</li>
                <?elseif($value=='checkbox_0'):?>
                    <li><b><?=$name?></b>: &times;</li>
                <?else:?>
                    <li><b><?=$name?></b>: <?=$value?></li>
                <?endif?>
            <?endforeach?>
        </ul>
    <?endif?>

    <?if((core::config('payment.paypal_seller')==1 OR Core::config('payment.stripe_connect')==1 OR Core::config('payment.escrow_pay')==TRUE) AND $ad->price != NULL AND $ad->price > 0):?>
        <?if(core::config('payment.stock')==0 OR ($ad->stock > 0 AND core::config('payment.stock')==1)):?>
            <a href="<?=Route::url('default', array('action'=>'buy','controller'=>'ad','id'=>$ad->id_ad))?>"><?=_e('Buy Now')?></a>
        <?endif?>
    <?endif?>

    <?if (core::config('advertisement.gm_api_key') AND $ad->latitude AND $ad->latitude):?>
        <amp-img src="//maps.googleapis.com/maps/api/staticmap?language=<?=i18n::get_display_language(i18n::$locale)?>&zoom=<?=Core::config('advertisement.map_zoom')?>&scale=false&size=600x300&maptype=roadmap&format=png&visual_refresh=true&markers=<?=($ad->category->get_icon() AND Kohana::$environment !== Kohana::DEVELOPMENT) ? 'icon:'.Core::imagefly($ad->category->get_icon(),48,48).'%7C' : NULL?>size:large%7Ccolor:red%7Clabel:·%7C<?=$ad->latitude?>,<?=$ad->longitude?>"
            height="300"
            width="600"
            layout="responsive">
        </amp-img>
    <?endif?>

    <div class="amp-oc-link">
        <a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=_e('Send Message')?></a>
    </div>

    <hr>

    <p class="amp-oc-text-center ">
        <a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=_e('View full page')?></a>
    </p>
<?endif?>
