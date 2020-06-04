<?php defined('SYSPATH') or die('No direct script access.');?>	
	<div class="page-header">
		<h1><?=_e('2 Step SMS Authentication')?></h1>
	</div>

    <form class="well form-horizontal auth"  method="post" action="<?=$form_action?>">         
      <?=Form::errors()?>

      <div class="form-group">
        <label class="col-sm-8 control-label"><?=_e('We have sent an SMS code to your phone number')?>
        <?=substr($phone,0,3)?>*****<?=substr($phone,-4)?></label>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label"><?=_e('Verification Code')?></label>
        <div class="col-md-5 col-sm-5">
          <input class="form-control" type="text" name="code" placeholder="<?=__('Code')?>">
        </div>
      </div>
      

      <div class="page-header"></div>
      <div class="col-sm-offset-3">
        <button type="submit" class="btn btn-primary"><?=_e('Send')?></button>
      </div>
      <?=Form::redirect()?>
      <?=Form::CSRF('sms')?>
    </form>         
