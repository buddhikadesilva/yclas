<?php defined('SYSPATH') or die('No direct script access.');?>

<?if($widget->ad != FALSE):?>
    <div class="panel-body">
        <div>		
            <?if(core::config('payment.pay_to_go_on_top') > 0 AND core::config('payment.to_top') != FALSE):?>	
                <a class="btn btn-danger center-block" type="button" href="<?=Route::url('default', array('action'=>'to_top','controller'=>'ad','id'=>$widget->ad->id_ad))?>">
                    <?=_e('Go Top!')?> <?=i18n::money_format(core::config('payment.pay_to_go_on_top'),core::config('payment.paypal_currency'))?>
                </a>
            <?endif?>
        
            <?if(core::config('payment.to_featured') != FALSE AND $widget->ad->featured < Date::unix2mysql()):?>
                <a class="btn btn-danger center-block" type="button" href="<?=Route::url('default', array('action'=>'to_featured','controller'=>'ad','id'=>$widget->ad->id_ad))?>">
                    <?=_e('Go Featured!')?> <?=i18n::money_format(Model_Order::get_featured_price(),core::config('payment.paypal_currency'))?>
                </a>
            <?endif?>
        
            <div class="clearfix"></div><br>
        
            <a class="btn btn-primary" href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$widget->ad->id_ad))?>">
                <i class="glyphicon glyphicon-edit"></i> <?=_e("Edit");?>
            </a> 
            <a class="btn btn-primary" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$widget->ad->id_ad))?>" 
                onclick="return confirm('<?=__('Deactivate?')?>');">
                <i class="glyphicon glyphicon-off"></i><?=_e("Deactivate");?>
            </a> 
        
            <?if(Auth::instance()->logged_in() AND Auth::instance()->get_user()->is_admin()):?>
                <a class="btn btn-primary" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$widget->ad->id_ad))?>" 
                    onclick="return confirm('<?=__('Spam?')?>');">
                    <i class="glyphicon glyphicon-fire"></i><?=_e("Spam");?>
                </a> 
                <a class="btn btn-primary" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$widget->ad->id_ad))?>" 
                    onclick="return confirm('<?=__('Delete?')?>');">
                    <i class="glyphicon glyphicon-remove"></i><?=_e("Delete");?>
                </a>
            <?endif?>
        </div>

        <hr>
        
        <ul>
            <?foreach($widget->user_ads as $ads):?>
                <li>
                    <a title="<?=HTML::chars($ads->title);?>" alt="<?=HTML::chars($ads->title);?>" href="<?=Route::url('ad', array('category'=>$ads->category->seoname,'seotitle'=>$ads->seotitle))?>">
                        <?=$ads->title;?>
                    </a>
                </li>
            <?endforeach?>
        </ul>
    </div>
<?endif?>