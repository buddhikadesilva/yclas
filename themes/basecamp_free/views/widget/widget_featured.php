<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->featured_title!=''):?>
	<div class="panel-heading">
		<h3 class="panel-title"><?=$widget->featured_title?></h3>
	</div>
<?endif?>

<div class="panel-body">
	<?foreach($widget->ads as $ad):?>
		<div class="featured-sidebar-box">
			<div class="feat-spacer">
			<div class="<?=(get_class($widget)=='Widget_Featured')?'featured-custom-box':''?>" >
				<?if($ad->get_first_image() !== NULL):?>
					<div class="picture">
						<a class="" title="<?=HTML::chars($ad->title);?>" alt="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
							<div class="ad-container">
								<img src="<?=$ad->get_first_image()?>" width="100%">
								<?if ($ad->price>0):?>
									<span class="ad_price"> <?=i18n::money_format( $ad->price, $ad->currency())?></span>
								<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
									<span class="ad_price"><?=_e('Free');?></span>
								<?else:?>
									<span class="ad_price"><?=_e('Check Listing');?></span>
								<?endif?>
							</div>
						</a>
					</div>
				<?else:?>
					<div class="picture">
						<a class="" title="<?=HTML::chars($ad->title);?>" alt="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
							<div class="ad-container">
								<img data-src="holder.js/<?=core::config('image.width_thumb')?>x<?=core::config('image.height_thumb')?>?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->translate_name(), 'size' => 14, 'auto' => 'yes')))?>"  width="100%">
								<?if ($ad->price>0):?>
									<span class="ad_price"> <?=i18n::money_format( $ad->price, $ad->currency())?></span>
								<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
									<span class="ad_price"><?=_e('Free');?></span>
								<?else:?>
									<span class="ad_price"><?=_e('Check Listing');?></span>
								<?endif?>
							</div>
						</a>
					</div>
				<?endif?>
				<div class="clearfix"></div>
			</div>
			</div>
		</div>
	<?endforeach?>
</div>