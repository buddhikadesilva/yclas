<?php defined('SYSPATH') or die('No direct script access.');?>
<?if(array_key_exists('currency', Model_Field::get_all()) AND (strtolower(Request::current()->controller())=='ad' AND strtolower(Request::current()->action())=='view')):?>
	<?if ($widget->currency_title!=''):?>
	    <div class="panel-heading">
	        <h3 class="panel-title"><?=$widget->currency_title?></h3>
	    </div>
	<?endif?>
	<div class="panel-body">
		<?if(isset(Model_Ad::current()->cf_currency) AND !empty(Model_Ad::current()->cf_currency)):?>
	    	<div class="form-group curry" data-locale="<?=Model_Ad::current()->currency();?>" data-currencies="<?=($widget->currencies);?>" data-default="<?=($widget->default);?>" data-apikey="<?=($widget->apikey);?>">
	    <?else:?>
	    	<div class="form-group curry" data-locale="<?=core::config('general.number_format');?>" data-currencies="<?=($widget->currencies);?>" data-default="<?=($widget->default);?>" data-apikey="<?=($widget->apikey);?>">
	    <?endif?>
	        <div class="my-future-ddm"></div>
	    </div>
	</div>
<?elseif((!array_key_exists('currency', Model_Field::get_all()))):?>
	<?if ($widget->currency_title!=''):?>
	    <div class="panel-heading">
	        <h3 class="panel-title"><?=$widget->currency_title?></h3>
	    </div>
	<?endif?>
	<div class="panel-body">
    	<div class="form-group curry" data-locale="<?=core::config('general.number_format');?>" data-currencies="<?=($widget->currencies);?>" data-default="<?=($widget->default);?>" data-apikey="<?=($widget->apikey);?>">
	        <div class="my-future-ddm"></div>
	    </div>
	</div>
<?endif?>