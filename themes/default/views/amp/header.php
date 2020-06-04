<nav class="amp-oc-title-bar">
    <div>
        <a href="<?=Route::url('default')?>">
        	<?if (Theme::get('logo_url')!=''):?>
        		<div class="fixed-height-container my1 amp-oc-text-center">
                	<amp-img src="<?=Theme::get('logo_url')?>" class="amp-oc-unknown-size" layout="fill"></amp-img>
                </div>
            <?else:?>
	            <h3><?=core::config('general.site_name')?></h3>
            <?endif?>
        </a>
    </div>
</nav>