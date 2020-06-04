<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="<?=(Theme::get('sidebar_position')!='none')?'col-xs-9':'col-xs-12'?> <?=(Theme::get('sidebar_position')=='left')?'pull-right':'pull-left'?>">
				<div class="page-header">
					<h3><?=_e('Register')?></h3>
				</div>
				<div class="auth-page reg">
					<?=View::factory('pages/auth/register-form')?>
					<?if (Core::config('general.sms_auth') == TRUE ):?>
					    <div class="page-header">
					        <h2 class="h3"><?=_e('Phone Register')?></h2>
					    </div>
					    <?=View::factory('pages/auth/phoneregister-form')?>
					<?endif?>
				</div>
			</div>

			<?=View::fragment('sidebar_front','sidebar')?>
	    </div>
	</div>
</div>
