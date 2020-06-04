<?php defined('SYSPATH') or die('No direct script access.');?>
<form class="auth" method="post" action="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>">         
	<div class="modal-body">
		<?=Form::errors()?>
		<div class="form-group clearfix">
			<label class="col-xs-12 control-label"><?=_e('Email')?></label>
			<div class="col-xs-12">
				<input class="form-control" type="text" name="email" tabindex="1" placeholder="<?=__('Email')?>">
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="col-xs-12 control-label">
				<?=_e('Password')?> (<small><a data-toggle="modal" data-dismiss="modal" tabindex="-1" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'forgot'))?>#forgot-modal"><?=_e('Forgot password?')?></a></small>)
			</label>
			<div class="col-xs-12">
				<input class="form-control" type="password" name="password" tabindex="2" placeholder="<?=__('Password')?>">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="remember" tabindex="2" checked="checked"><?=_e('Remember me')?>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-foot-controls clearfix">
        <ul class="list-inline">
            <li>
                <button type="submit" class="btn btn-base-dark log-btn">
                    <?=_e('Login')?>
                </button>
            </li>
            <li>
                <?=_e('Don’t Have an Account?')?>
                <a data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'register'))?>#register-modal">
                    <?=_e('Register')?>
                </a>
            </li>
        </ul>
	</div>
	<?=Form::redirect()?>
	<?=Form::CSRF('login')?>
</form>

<?=View::factory('pages/auth/phonelogin')?>