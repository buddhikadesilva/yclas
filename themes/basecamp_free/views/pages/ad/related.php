<?php defined('SYSPATH') or die('No direct script access.');?>

<?if(core::count($ads)):?>
	<h3><?=_e('Related ads')?></h3>
		<div class="ad_listings">
			<ul class="ad_list list">
				<?foreach($ads as $ad ):?>
					<?if($ad->featured >= Date::unix2mysql(time())):?>
						<li class="ad_listitem clearfix featured_ad">
							<span class="feat_marker"><i class="glyphicon glyphicon-bookmark"></i></span>
					<?else:?>
						<li class="ad_listitem clearfix">
					<?endif?>
					<div class="ad_inner">
						<div class="ad_photo">
							<div class="ad_photo_inner">
							<a title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
							<?if($ad->get_first_image() !== NULL):?>
								<?=HTML::picture($ad->get_first_image(), ['w' => 180, 'h' => 180], ['992px' => ['w' => '180', 'h' => '180'], '320px' => ['w' => '180', 'h' => '180']], ['class' => 'img-responsive'], ['alt' => HTML::chars($ad->title)])?>
							<?else:?>
								<img data-src="holder.js/180x180?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->translate_name(), 'size' => 14, 'auto' => 'yes')))?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>">
							<?endif?>
								<span class="gallery_only fm"><i class="glyphicon glyphicon-bookmark"></i></span>
							<?if ($ad->price!=0):?>
								<span class="gallery_only ad_gprice"><?=i18n::money_format( $ad->price, $ad->currency())?></span>
							<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
								<span class="gallery_only ad_gprice"><?=_e('Free');?></span>
							<?else:?>
								<span class="gallery_only ad_gprice">Check Listing</span>
							<?endif?>
							</a>
							</div>
						</div>

						<div class="ad_details">
							<div class="ad_details_inner">
								<h2>
									<a title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
										<?=$ad->title?>
									</a>
								</h2>
								<p class="ad_meta clearfix">
									<?if ($ad->published!=0){?>
										<span><i class="glyphicon glyphicon-calendar"></i> <?=Date::format($ad->published, core::config('general.date_format'))?></span>
									<? }?>
								</p>
								<?if(core::config('advertisement.description')!=FALSE):?>
									<div class="ad_desc"><?=Text::limit_chars(Text::removebbcode($ad->description), 255, NULL, TRUE);?></div>
								<?endif?>
								<div class="ad_buttons">
									<?if ($ad->price!=0):?>
									<span class="ad_price">
										<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
											<?=_e('Price');?>: <b><?=i18n::money_format( $ad->price, $ad->currency())?></b>
										</a>
									</span>
									<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
										<span class="ad_price">
											<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
												<?=_e('Price');?>: <b><?=_e('Free');?></b>
											</a>
										</span>
									<?else:?>
										<span class="ad_price na">
											<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">Check Listing</a>
										</span>
									<?endif?>
								</div>
							</div>
						</div>
					</div>
					</li>
				<?endforeach?>
			</ul>
		</div>
<?else:?>
	<div class="no_results text-center">
		<span class="nr_badge"><i class="glyphicon glyphicon-th-list"></i></span>
		<p class="nr_info"><?=_e('Sorry, no related ads available..')?></p>
	</div>
<?endif?>