<?php defined('SYSPATH') or die('No direct script access.');?>
<form class="auth"  method="post" action="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'forgot'))?>">         
	<div class="modal-body">         
		<?=Form::errors()?>
		<div class="form-group clearfix">
			<label class="col-xs-12 control-label"><?=_e('Email')?></label>
			<div class="col-xs-12">
				<input class="form-control" type="text" name="email" placeholder="<?=__('Email')?>">
			</div>
		</div>
	</div>
	<div class="modal-foot-controls clearfix">
		<ul class="list-inline">
            <li>
                <button type="submit" class="btn btn-base-dark"><?=_e('Send')?></button>
            </li>
            <li>
                <?=_e('Don’t Have an Account?')?>
                <a data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'register'))?>#register-modal">
                    <?=_e('Register')?>
                </a>
            </li>
        </ul>
	</div>
	<?=Form::CSRF('forgot')?>
</form>