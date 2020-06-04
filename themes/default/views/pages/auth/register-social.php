<?php defined('SYSPATH') or die('No direct script access.');?>
<form class="well form-horizontal"  method="post" action="<?=$form_action?>">         
          <?=Form::errors()?>
          <div class="form-group">
            <label class="control-label"><?=_e('Name')?></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="name" value="<?=HTML::chars(Core::post('name'))?><?=HTML::chars(Core::get('name'))?>" placeholder="<?=__('Name')?>">
            </div>
          </div>
          
          <div class="form-group">
            <label class="control-label"><?=_e('Email')?></label>
            <div class="col-md-4">
              <input class="form-control" type="text" name="email" value="<?=HTML::chars(Core::post('email'))?>" placeholder="<?=__('Email')?>">
            </div>
          </div>
     
          <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?=_e('Register')?></button>
          </div>
          <?=Form::CSRF('register_social')?>
</form>      	
