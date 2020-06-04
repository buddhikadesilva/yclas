<?php defined('SYSPATH') or die('No direct script access.');?>

<ul class="list-inline pull-right">
    <li>
        <form class="form-inline" method="get" action="<?=URL::current();?>">
            <div class="form-group">
                <input type="text" class="form-control" name="name" placeholder="<?=__('Coupon name')?>" value="<?=HTML::chars(core::get('name'))?>">
            </div>
            <button type="submit" class="btn"><?=__('Search')?></button>
        </form>
    </li>
    <li>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import-tool">
            <i class="fa fa-upload"></i>&nbsp; <?=__('Import')?>
        </button>
    </li>
    <li>
        <a class="btn btn-primary" href="<?=Route::url($route, array('controller'=> Request::current()->controller(), 'action'=>'bulk')) ?>">
            <i class="glyphicon glyphicon-list-alt"></i>
            <?=__('Bulk')?>
        </a>  
    </li>
    <li>
        <a class="btn btn-primary" href="<?=Route::url($route, array('controller'=> Request::current()->controller(), 'action'=>'create')) ?>">
            <i class="glyphicon glyphicon-pencil"></i>
            <?=__('New')?>
        </a>
    </li>
</ul>

<h1 class="page-header page-title">
    <?=Text::ucfirst(__($name))?>
    <a target="_blank" href="https://docs.yclas.com/how-to-use-coupon-system/">
        <i class="fa fa-question-circle"></i>
    </a>
</h1>
<hr>

<?if (Theme::get('premium')!=1):?>
    <div class="alert alert-info fade in">
        <p>
            <strong><?=__('Heads Up!')?></strong> 
            <?=__('only available with PRO version!').'<br/>'.__('Upgrade your Yclas site to activate this feature.')?>
        </p>
        <p>
            <a class="btn btn-info" href="https://yclas.com/self-hosted.html">
                <?=__('Upgrade to PRO now')?>
            </a>
        </p>
    </div>
<?endif?>

<div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?=__('Name')?></th>
                    <th><?=__('Product')?></th>
                    <th><?=__('Discount')?></th>
                    <th><?=__('Number Coupons')?></th>
                    <th><?=__('Valid until')?></th>
                    <th class="hidden-sm hidden-xs"><?=__('Created')?></th>
                    <?if ($controller->allowed_crud_action('delete') OR $controller->allowed_crud_action('update')):?>
                        <th><?=__('Actions') ?></th>
                    <?endif?>
                </tr>
            </thead>
            <tbody>
                <?foreach($elements as $element):?>
                    <tr id="tr<?=$element->pk()?>">
                        <td><?=$element->name?></td>
                        <td>
                            <?if (isset($element->produt)):?>
                                <?=$element->product->title?>
                            <?elseif(method_exists('Model_Order','product_desc')):?>
                                <?=(($product_desc = Model_Order::product_desc($element->id_product)) == '') ? __('Any') : $product_desc?>
                            <?else:?>
                                <?=$element->id_product?>
                            <?endif?>
                        </td>
                        <td>
                            <?=($element->discount_amount==0)?round($element->discount_percentage,0).'%':i18n::money_format($element->discount_amount)?>
                        </td>
                        <td><?=$element->number_coupons?></td>
                        <td><?=Date::format($element->valid_date, core::config('general.date_format'))?></td>
                        <td class="hidden-sm hidden-xs"><?=Date::format($element->created, core::config('general.date_format'))?></td>

                        <?if ($controller->allowed_crud_action('delete') OR $controller->allowed_crud_action('update')):?>
                            <td class="nowrap">
                                <div class="btn-group">
                                    <?if ($controller->allowed_crud_action('update')):?>
                                        <a title="<?=__('Edit')?>" class="btn btn-primary" href="<?=Route::url($route, array('controller'=> Request::current()->controller(), 'action'=>'update','id'=>$element->pk()))?>">
                                           <i class="glyphicon glyphicon-edit"></i>
                                        </a>
                                    <?endif?>
                                    <?if ($controller->allowed_crud_action('delete')):?>
                                        <a 
                                            href="<?=Route::url($route, array('controller'=> Request::current()->controller(), 'action'=>'delete','id'=>$element->pk()))?>" 
                                            class="btn btn-danger index-delete" 
                                            data-title="<?=__('Are you sure you want to delete?')?>" 
                                            data-id="tr<?=$element->pk()?>" 
                                            data-btnOkLabel="<?=__('Yes, definitely!')?>" 
                                            data-btnCancelLabel="<?=__('No way!')?>">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    <?endif?>
                                </div>
                            </td>
                        <?endif?>
                    </tr>
                <?endforeach?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer text-right">
        <?if ($controller->allowed_crud_action('export')):?>
            <a class="btn btn-success" href="<?=Route::url($route, array('controller'=> Request::current()->controller(), 'action'=>'export')) ?>" title="<?=__('Export')?>">
                <i class="fa fa-download"></i>
                &nbsp;<?=__('Export all')?>
            </a>                
        <?endif?>
    </div>
</div>

<div class="text-center"><?=$pagination?></div>

<div class="modal fade" id="import-tool" tabindex="-1" role="dialog" aria-labelledby="importCoupons" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <?=FORM::open(Route::url('oc-panel',array('controller'=>'coupon','action'=>'import')), array('class'=>'', 'enctype'=>'multipart/form-data'))?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                    <h4 id="importCoupons" class="modal-title"><?=__('Upload CSV file')?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for=""><?=__('import coupons')?></label>
                        <input type="file" name="csv_file_coupons" id="csv_file_coupons" class="form-control"/>
                        <p class="help-block">
                            <?=__('Please use the correct CSV format')?>, <?=__('limited to 10.000 at a time')?>, <?=__('1 MB file')?>.
                            <br>
                            <?=__('Coupons')?>: <a href="https://cdn.rawgit.com/yclas/yclas/master/install/samples/import/coupons.csv"><?=__('download example')?>.</a>
                        </p>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Cancel')?></button>
                    <?=FORM::button('submit', __('Upload'), array('type'=>'submit','id'=>'csv_upload', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'coupon','action'=>'import'))))?>
                </div>
            <?=FORM::close()?>
        </div>
    </div>
</div>