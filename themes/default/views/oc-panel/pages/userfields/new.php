<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title">
    <?=__('New Custom Field')?>
</h1>

<hr>
        
<div class="panel panel-default">
    <div class="panel-body">
        <form method="post" action="<?=Route::url('oc-panel',array('controller'=>'userfields','action'=>'new'))?>">         
            <?=Form::errors($errors)?>
          
            <div class="form-group">
                <label class="control-label"><?=__('Name')?></label>                
                <input class="form-control" type="text" name="name" maxlength="64" placeholder="<?=__('Name')?>" value="<?=HTML::chars(Core::post('name'))?>" required>
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Label')?></label>                
                <input class="form-control" type="text" name="label" placeholder="<?=__('Label')?>" value="<?=HTML::chars(Core::post('label'))?>" required>
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Tooltip')?></label>                
                <input class="form-control" type="text" name="tooltip" placeholder="<?=__('Tooltip')?>" value="<?=HTML::chars(Core::post('tooltip'))?>">
            </div>

            <div class="form-group">
                <label class="control-label" for="date"><?=__('Type')?></label>      
                <select name="type" class="form-control" id="cf_type_fileds" required>
                    <option value="string"><?=__('Text 256 Chars')?></option>
                    <option value="textarea"><?=__('Text Long')?></option>
                    <option value="integer"><?=__('Number')?></option>  
                    <option value="decimal"><?=__('Number Decimal')?></option>
                    <option value="date"><?=__('Date')?></option>
                    <option value="select"><?=__('Select')?></option>
                    <option value="radio"><?=__('Radio')?></option>
                    <option value="email"><?=__('Email')?></option>
                    <option value="country"><?=__('Country')?></option>
                    <option value="checkbox"><?=__('Checkbox')?></option>
                </select>
            </div>

            <div class="form-group">
                <label class="control-label"><?=__('Values')?></label>
                <input class="form-control" id="cf_values_input" type="text" name="values" placeholder="<?=__('Comma separated for select')?>">
            </div>

            <hr>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="required" type="checkbox" id="checkbox_required">
                    <label for="checkbox_required"><?=__('Required')?></label>
                </div>
                <div class="help-block"><?=__('Required field to register.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="searchable" type="checkbox" id="checkbox_searchable">
                    <label for="checkbox_searchable"><?=__('Searchable')?></label>
                </div>
                <div class="help-block"><?=__('Search in ads will include this field as well.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="show_profile" type="checkbox" id="checkbox_show_profile">
                    <label for="checkbox_show_profile"><?=__('Show Profile')?></label>
                </div>
                <div class="help-block"><?=__('Can be seen in the user profile.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="show_register" type="checkbox" id="checkbox_show_register">
                    <label for="checkbox_show_register"><?=__('Show Register')?></label>
                </div>
                <div class="help-block"><?=__('Appears when user registers.')?></div>
            </div>

            <div class="form-group">
                <div class="checkbox check-success">
                    <input name="admin_privilege" type="checkbox" id="checkbox_admin_privilege">
                    <label for="checkbox_admin_privilege"><?=__('Admin Privileged')?></label>
                </div>
                <div class="help-block"><?=__('Can be seen and edited only by admin.')?></div>
            </div>
            
            <hr>
            <div class="form-actions">
                <a href="<?=Route::url('oc-panel',array('controller'=>'userfields','action'=>'index'))?>" class="btn btn-default ajax-load" title="<?=__('Cancel')?>">
                    <?=__('Cancel')?>
                </a>
                <button type="submit" class="btn btn-primary"><?=__('Create')?></button>
            </div>
        </form>
    </div>
</div>

