<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title">
    <?=__('Bulk coupon generator')?>
</h1>

<hr>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body">
                <div class="btn-group btn-group-justified">
                    <a href="#" class="btn btn-default btn-fixed active"><?=__('Fixed Discount')?></a>
                    <a href="#" class="btn btn-default btn-percentage"><?=__('Percentage Discount')?></a>
                </div>
                <hr>
                <form action="<?=Route::url('oc-panel', array('controller'=> 'coupon', 'action'=>'bulk')) ?>" method="post" accept-charset="utf-8" class="form form-horizontal" enctype="multipart/form-data">   
                <input type="hidden" name="id_coupon" value="" />
                    <div class="form-group ">
                    <div class="col-sm-12">
                        <label for="id_product" class="control-label"><?=__('Id Product')?></label>        
                        <select name="id_product">
                            <option value="" selected="selected"><?=__('Any')?></option>
                            <?foreach ($products as $key => $value) :?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?endforeach?>
                        </select>                           
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-sm-12">
                        <label for="discount_amount" class="control-label"><?=__('Discount Amount')?></label>      
                        <input type="text" id="discount_amount" name="discount_amount" value="" placeholder="<?=html_entity_decode(i18n::money_format(1))?>" />                         
                    </div>
                </div>
                <div class="form-group hidden">
                    <div class="col-sm-12">
                        <label for="discount_percentage" class="control-label"><?=__('Discount Percentage')?></label>      
                        <input type="text" id="discount_percentage" name="discount_percentage" value="" />
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-sm-12">
                        <label for="valid_date" class="control-label"><?=__('Valid Date')?></label>        
                        <input type="text" name="valid_date" value="" placeholder="yyyy-mm-dd" data-toggle="datepicker" data-date="" data-date-format="yyyy-mm-dd" />                          
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-sm-12">
                        <label for="number_coupons" class="control-label"><?=__('Number of unique coupons to generate')?>, <?=__('limited to 10.000 at a time')?></label>       
                        <input type="text" id="number_coupons" name="number_coupons" value="" />                           
                    </div>
                </div>
                    
                <hr>
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn btn-primary"><?=__('Submit')?></button>
                </div>

                </form>
			</div>
		</div>
	</div>
</div>