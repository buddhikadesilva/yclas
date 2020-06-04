<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<h3><?=_e('My Favorites')?></h3>
			</div>

			<?foreach($favorites as $favorite):?>
			<div class="my_ad_item" id="fi<?=$favorite->id_favorite?>">
				<div class="my_ad_body clearfix">
					<div class="ad_pcoll">
						<div class="pad_10">
						<?if($favorite->ad->get_first_image() !== NULL):?>
							<img src="<?=$favorite->ad->get_first_image()?>" alt="<?=HTML::chars($favorite->ad->title)?>" />
						<?else:?>
							<img data-src="holder.js/<?=core::config('image.width_thumb')?>x<?=core::config('image.height_thumb')?>?<?=str_replace('+', ' ', http_build_query(array('text' => $favorite->ad->category->translate_name(), 'size' => 14, 'auto' => 'yes')))?>" alt="<?=HTML::chars($favorite->ad->title)?>">
						<?endif?>
						</div>
					</div>
					<div class="ad_dcoll">
						<div class="pad_10">
							<div class="my_ad_title clearfix">
								<a class="at" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$favorite->ad->category->seoname,'seotitle'=>$favorite->ad->seotitle))?>"><?=$favorite->ad->title?></a>
							</div>

							<p><b><?=_e('Date')?> : </b><?= Date::format($favorite->ad->published, core::config('general.date_format'))?></p>
							<p><b><?=_e('Location')?> : </b><?=$favorite->ad->location->translate_name()?></p>
							<p><b><?=_e('Favorited') ?> :</b> <?= Date::format($favorite->created, core::config('general.date_format'))?></p>
							<p class="text-right">
								<a
								href="<?=Route::url('oc-panel', array('controller'=>'profile', 'action'=>'favorites','id'=>$favorite->id_ad))?>"
								class="btn btn-danger index-delete index-delete-inline"
								data-title="<?=__('Are you sure you want to delete?')?>"
								data-id="fi<?=$favorite->id_favorite?>"
								data-btnOkLabel="<?=__('Yes, definitely!')?>"
								data-btnCancelLabel="<?=__('No way!')?>">
								<i class="fa fa-trash-o"></i>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			<?endforeach?>
		</div>
	</div>
</div>