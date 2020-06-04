<?php defined('SYSPATH') or die('No direct script access.');?>

<ul class="list-inline pull-right">
    <li>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#hide-categories">
            <?=__('Hide Categories')?>
        </button>
    </li>
    <li>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#quick-creator" id="quick-creator-btn">
            <?=__('Quick creator')?>
        </button>
    </li>
    <li>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import-tool">
            <i class="fa fa-upload"></i>&nbsp; <?=__('Import')?>
        </button>
    </li>
    <li>
        <a class="btn btn-primary ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'category','action'=>'create'))?>" title="<?=__('New Category')?>">
            <i class="fa fa-plus-circle"></i>&nbsp; <?=__('New Category')?>
        </a>
    </li>
</ul>

<h1 class="page-header page-title">
    <?=__('Categories')?>
    <a target="_blank" href="https://docs.yclas.com/how-to-add-categories/">
        <i class="fa fa-question-circle"></i>
    </a>
</h1>

<hr>

<p>
    <?=__('Change the order of your categories. Keep in mind that more than 2 levels nested probably won´t be displayed in the theme (it is not recommended).')?>
</p>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?=__('Home')?></div>
            </div>
            <?=FORM::open(Route::url('oc-panel',array('controller'=>'category','action'=>'delete')), array('class'=>'form-inline', 'enctype'=>'multipart/form-data'))?>
                <div class="panel-body table-responsive">
                    <ol class='plholder' id="ol_1" data-id="1">
                        <?function lili($item, $key,$cats){?>
                            <li data-id="<?=$key?>" id="li_<?=$key?>">
                                <div class="drag-item">
                                    <span class="drag-icon">
                                        <i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i>
                                    </span>
                                    <div class="drag-name">
                                        <?=$cats[$key]['name']?>
                                    </div>
                                    <a class="drag-action ajax-load" title="<?=__('Edit')?>"
                                        href="<?=Route::url('oc-panel',array('controller'=>'category','action'=>'update','id'=>$key))?>">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    <a 
                                        href="<?=Route::url('oc-panel', array('controller'=> 'category', 'action'=>'delete','id'=>$key))?>" 
                                        class="drag-action index-delete" 
                                        title="<?=__('Are you sure you want to delete?')?>" 
                                        data-id="li_<?=$key?>" 
                                        data-text="<?=__('We will move the siblings categories and ads to the parent of this category.')?>"
                                        data-btnOkLabel="<?=__('Yes, definitely!')?>" 
                                        data-btnCancelLabel="<?=__('No way!')?>">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </a>
                                    <span class="drag-action">
                                        <div class="checkbox check-success">
                                            <input name="categories[]" value="<?=$key?>" type="checkbox" id="checkbox_<?=$key?>">
                                            <label for="checkbox_<?=$key?>"></label>
                                        </div>
                                    </span>
                                </div>
                    
                                <ol data-id="<?=$key?>" id="ol_<?=$key?>">
                                    <? if (is_array($item)) array_walk($item, 'lili', $cats);?>
                                </ol><!--ol_<?=$key?>-->
                    
                            </li><!--li_<?=$key?>-->
                        <?}
                        if(is_array($order))
                            array_walk($order, 'lili',$cats);?>
                    </ol><!--ol_1-->
                    <span id='ajax_result' data-url='<?=Route::url('oc-panel',array('controller'=>'category','action'=>'saveorder'))?>'></span>
                </div>
                <?if(core::count($cats) > 1) :?>
                    <div class="panel-footer">
                        <div class="text-right">
                            <button type="button" data-toggle="modal" data-target="#delete-all" class="btn btn-danger">
                                <?=__('Delete all categories')?>
                            </button>

                            <button name="delete" type="submit" class="btn btn-danger">
                                <i class="glyphicon glyphicon-trash"></i>&nbsp; <?=__('Delete selected categories')?>
                            </button>
                        </div>
                    </div>
                <?endif?>
            <?=FORM::close()?>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-all" tabindex="-1" role="dialog" aria-labelledby="deleteCategories" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= FORM::open(Route::url('oc-panel',array('controller'=>'category','action'=>'delete_all'), array('class'=>'form-horizontal', 'role'=>'form')))?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                    <h4 id="deleteCategories" class="modal-title"><?=__('Are you sure you want to delete all the categories?')?></h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <p><?=__('We will move all the ads to home category.')?> <?=__('This is permanent! No backups, no restores, no magic undo button. We warned you, ok?')?></p>
                    </div>
                </div>
                <div class="modal-body text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Cancel')?></button>
                    <button type="submit" class="btn btn-danger" name="confirmation" value="1"><?=__('Delete')?></button>
                </div>
            <?= FORM::close()?>
        </div>
    </div>
</div>

<div class="modal fade" id="quick-creator" tabindex="-1" role="dialog" aria-labelledby="quickCategories" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <?=FORM::open(Route::url('oc-panel',array('controller'=>'category','action'=>'multy_categories')), array('role'=>'form','enctype'=>'multipart/form-data'))?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                    <h4 id="quickCategories" class="modal-title"><?=__('Quick category creator.')?></h4>
                </div>
                <div class="modal-body">
                    <p><?=__('Add names for multiple categories, for each one push enter.')?></p>
                    <div class="form-group">
                        <?=FORM::label('multy_categories', __('Name'), array('class'=>'control-label', 'for'=>'multy_categories'))?>
                        <div>
                            <?=FORM::input('multy_categories', '', array('placeholder' => __('Hit enter to confirm'), 'class' => 'form-control', 'id' => 'multy_categories', 'type' => 'text','data-role'=>'tagsinput'))?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Cancel')?></button>
                    <?=FORM::button('submit', __('Send'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'category','action'=>'multy_categories'))))?>
                </div>
            <?=FORM::close()?>
        </div>
    </div>
</div>

<div class="modal fade" id="import-tool" tabindex="-1" role="dialog" aria-labelledby="importCategories" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <?=FORM::open(Route::url('oc-panel',array('controller'=>'tools','action'=>'import_tool')), array('enctype'=>'multipart/form-data'))?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                    <h4 id="importCategories" class="modal-title"><?=__('Upload CSV file')?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label" for="csv_file_categories">
                            <?=__('Import Categories')?>
                        </label>
                        <input type="file" name="csv_file_categories" id="csv_file_categories" class="form-control">
                        <span class="help-block">
                            <?=__('Please use the correct CSV format')?> <a href="https://docs.google.com/uc?id=0B60e9iwQucDwTm1NRGlqcEZwdGM&export=download"><?=__('download example')?></a>.
                        </span>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Cancel')?></button>
                    <?=FORM::button('submit', __('Upload'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'tools','action'=>'import_tool'))))?>
                </div>
            <?=FORM::close()?>
        </div>
    </div>
</div>

<div class="modal" id="hide-categories" tabindex="-1" role="dialog" aria-labelledby="hideCategories" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <?=FORM::open(Route::url('oc-panel',array('controller'=>'category','action'=>'hide_homepage_categories')), array('enctype'=>'multipart/form-data'))?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle"></i></button>
                    <h4 id="hideCategories" class="modal-title"><?=__('Hide Categories')?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <?$categories = array()?>
                        <?foreach ((new Model_Category)->where('id_category','!=','1')->order_by('order','asc')->find_all()->cached() as $category) {
                            $categories[$category->id_category] = $category->name;
                        }?>
                        <?=FORM::label('Hide categories from homepage', __('Hide categories from homepage'), array('class'=>'control-label', 'for'=>'Hide categories from homepage'))?>
                        <?=FORM::hidden('hide_homepage_categories[]', NULL)?>
                        <?=FORM::select('hide_homepage_categories[]', $categories, $hide_homepage_categories, array( 
                            'class' => 'form-control', 
                            'id' => 'hide_homepage_categories', 
                        ))?> 
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=__('Cancel')?></button>
                    <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'category','action'=>'hide_homepage_categories'))))?>
                </div>
            <?=FORM::close()?>
        </div>
    </div>
</div>
