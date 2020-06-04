<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title">
    <?=__('Edit Custom Field')?>
</h1>

<hr>

<div class="panel panel-default">
    <div class="panel-body">
        <form method="post" action="<?=Route::url('oc-panel',array('controller'=>'userfields','action'=>'update','id'=>$name))?>">         
            <?=Form::errors()?>  
          
            <div class="form-group">
                <label class="control-label"><?=__('Name')?></label>
                <input DISABLED class="form-control" type="text" name="name" value="<?=$name?>" placeholder="<?=__('Name')?>" required>
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Type')?></label>
                <input DISABLED class="form-control" type="text" id="cf_type_field_input" name="type" value="<?=$field_data['type']?>" placeholder="<?=__('Type')?>" required>
            </div>
            
            <div class="form-group">
                <label class="control-label"><?=__('Label')?></label>
                <input class="form-control" type="text" name="label" value="<?=$field_data['label']?>" placeholder="<?=__('Label')?>" required>
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Tooltip')?></label>
                <input class="form-control" type="text" name="tooltip" value="<?=(isset($field_data['tooltip']))?$field_data['tooltip']:""?>" placeholder="<?=__('Tooltip')?>">
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Values')?></label>
                <input class="form-control" type="text" id="cf_values_input" name="values" value="<?=(is_array($field_data['values']))? implode(",", $field_data['values']): $field_data['values']?>" placeholder="<?=__('Comma separated for select')?>">
            </div>

            <hr>
            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="required" type="checkbox" id="checkbox_required" <?=($field_data['required']==TRUE)?'checked':''?>>
                    <label for="checkbox_required"><?=__('Required')?></label>
                </div>
                <div class="help-block"><?=__('Required field to register.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="searchable" type="checkbox" id="checkbox_searchable" <?=(isset($field_data['searchable']) AND $field_data['searchable']==TRUE)?'checked':''?>>
                    <label for="checkbox_searchable"><?=__('Searchable')?></label>
                </div>
                <div class="help-block"><?=__('Search in ads will include this field as well.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="show_profile" type="checkbox" id="checkbox_show_profile" <?=(isset($field_data['show_profile']) AND $field_data['show_profile']==TRUE)?'checked':''?>>
                    <label for="checkbox_show_profile"><?=__('Show Profile')?></label>
                </div>
                <div class="help-block"><?=__('Can be seen in the user profile.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="show_register" type="checkbox" id="checkbox_show_register" <?=(isset($field_data['show_register']) AND $field_data['show_register']==TRUE)?'checked':''?>>
                    <label for="checkbox_show_register"><?=__('Show Register')?></label>
                </div>
                <div class="help-block"><?=__('Appears when user registers.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="admin_privilege" type="checkbox" id="checkbox_admin_privilege" <?=(isset($field_data['admin_privilege']) AND $field_data['admin_privilege']==TRUE)?'checked':''?>>
                    <label for="checkbox_admin_privilege"><?=__('Admin Privileged')?></label>
                </div>
                <div class="help-block"><?=__('Can be seen and edited only by admin.')?></div>
            </div>

            <hr>
            <div class="form-actions">
                <a href="<?=Route::url('oc-panel',array('controller'=>'userfields','action'=>'index'))?>" class="btn btn-default ajax-load" title="<?=__('Cancel')?>">
                    <?=__('Cancel')?>
                </a>
                <button type="submit" class="btn btn-primary"><?=__('Save')?></button>
            </div>
        </form>
    </div>
</div>
