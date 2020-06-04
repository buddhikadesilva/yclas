<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<?if ($page->loaded()):?>
						<h3><?=$page->title?></h3>
						<p class="pad_5"><?=$page->description?></p>
					<?else:?>
						<h3><?=_e('Thanks for submitting your advertisement')?></h3>
					<?endif?>
				</div>

				<br><br>
				
				<div class="well text-center clearfix">
					<?if(core::config('payment.to_featured') != FALSE AND $ad->featured < Date::unix2mysql()):?>
						<p class="text-info"><?=_e('Your Advertisement can go to featured! For only ').i18n::format_currency(Model_Order::get_featured_price(),core::config('payment.paypal_currency'));?></p>
						<span class="extra_btn"><a class="btn btn-success" type="button" href="<?=Route::url('default', array('action'=>'to_featured','controller'=>'ad','id'=>$ad->id_ad))?>">
							<i class="glyphicon glyphicon-bookmark"></i> <?=_e('Go Featured!')?> 
						</a></span>
					<?endif?>
					<?if(core::config('general.moderation') == Model_Ad::POST_DIRECTLY) :?>
						<span class="extra_btn"><br><a class="btn btn-success" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=_e('Go to Your Ad')?></a><br><br></span>
					<?endif?>
				</div>
			</div>
		</div>
	</div>
</div>