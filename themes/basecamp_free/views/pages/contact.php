<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="<?=(Theme::get('sidebar_position')!='none')?'col-xs-9':'col-xs-12'?> <?=(Theme::get('sidebar_position')=='left')?'pull-right':'pull-left'?>">

				<?if(core::config('general.contact_page') != ''):?>
					<?$content = Model_Content::get_by_title(core::config('general.contact_page'))?>
					<div class="page-header">
						<h1><?=$content->title?></h1>
					</div>
					<div class="text-description"><?=$content->description?></div>
					<br>
				<?else:?>
					<div class="page-header">
						<h1><?=_e('Contact Us')?></h1>
					</div>
				<?endif?>
				
				<?=Form::errors()?>

				<?= FORM::open(Route::url('contact'), array('class'=>'', 'enctype'=>'multipart/form-data'))?>
				<fieldset class="pad_10">
			
				<?if (!Auth::instance()->logged_in()):?>
				<!-- NOT LOGGED IN SO GET NAME AND EMAIL -->
					<dl class="form-group">
						<dt><?= FORM::label('name', _e('Name'), array('class'=>'control-label', 'for'=>'name'))?></dt>
						<dd><?= FORM::input('name', Core::request('name'), array('placeholder' => __('Name'), 'class' => 'form-control', 'id' => 'name', 'required'))?></dd>
					</dl>
					<dl class="form-group">
						<dt><?= FORM::label('email', _e('Email'), array('class'=>'control-label', 'for'=>'email'))?></dt>
						<dd><?= FORM::input('email', Core::request('email'), array('placeholder' => __('Email'), 'class' => 'form-control', 'id' => 'email', 'type'=>'email','required'))?></dd>
					</dl>
				<!-- // NOT LOGGED IN SO GET NAME AND EMAIL -->	
				<?endif?>

					<dl class="form-group">
						<dt><?= FORM::label('subject', _e('Subject'), array('class'=>'control-label', 'for'=>'subject'))?></dt>
						<dd><?= FORM::input('subject', Core::request('subject'), array('placeholder' => __('Subject'), 'class' => 'form-control', 'id' => 'subject'))?></dd>
					</dl>
					<dl class="form-group">
						<dt><?= FORM::label('message', _e('Message'), array('class'=>'control-label', 'for'=>'message'))?></dt>
						<dd><?= FORM::textarea('message', Core::request('message'), array('class'=>'form-control', 'placeholder' => __('Message'), 'name'=>'message', 'id'=>'message', 'rows'=>7, 'required'))?></dd>
					</dl>

					<?if (core::config('advertisement.captcha') != FALSE):?>
						<dl class="form-group">
							<div class="captcha_box">
								<?if (Core::config('general.recaptcha_active')):?>
                                    <?=View::factory('recaptcha', ['id' => 'recaptcha1'])?>
								<?else:?>
									<div class="form-captcha wide-view">
										<span class="cap_note text-center"><?= FORM::label('captcha', _e('Captcha'), array('for'=>'captcha'))?></span>
										<span class="cap_img"><?=captcha::image_tag('contact')?></span>
										<span class="cap_ans"><?= FORM::input('captcha', "", array('class' => 'form-control fc-small', 'id' => 'captcha', 'required'))?></span>
									</div>
								<?endif?>
							</div>
						</dl>
					<?endif?>
					
					<dl class="form-group">
						<dt></dt>
						<dd class="text-center"><?= FORM::button(NULL, _e('Contact Us'), array('type'=>'submit', 'class'=>'btn btn-success', 'action'=>Route::url('contact')))?></dd>
					</dl>
				</fieldset>
				<?= FORM::close()?>
			</div>
		
			<?=View::fragment('sidebar_front','sidebar')?>
	    </div>
	</div>
</div>