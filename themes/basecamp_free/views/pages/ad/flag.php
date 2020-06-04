<div class="text-right pad_10">
	<a class="btn btn-xs btn-danger" type="button" href="<?=Route::url('contact')?>?subject=<?=__('Report Ad')?> - <?=$ad->id_ad?> - <?=$ad->title?>&message=<?=__('Report Ad')?> - <?=$ad->id_ad?> - <?=$ad->title?> - <?=Route::url('ad', array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <?=_e('Report this ad')?>
	</a>
</div>