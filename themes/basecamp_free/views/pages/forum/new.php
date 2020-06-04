<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=_e("New Forum Topic")?></h3>
				</div>

				<?php if ($errors): ?>
					<div class="alert alert-warning">
						<?=_e('Some errors were encountered, please check the details you entered.')?>
						<ul class="errors">
							<?php foreach ($errors as $message): ?>
								<li><?php echo $message ?></li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>
		
				<?=FORM::open(Route::url('forum-new'), array('class'=>'clean-table', 'enctype'=>'multipart/form-data'))?>
				<fieldset>
					<dl>
						<dt><?= FORM::label('title', _e('Title'), array('class'=>'control-label', 'for'=>'title'))?></dt>
						<dd><?= FORM::input('title', core::post('title'), array('placeholder' => __('Title'), 'class' => 'form-control', 'id' => 'title', 'required'))?></dd>
					</dl>
					<dl>
						<dt><?= FORM::label('id_forum', _e('Forum'), array('class'=>'control-label', 'for'=>'id_forum' ))?></dt>
						<dd>
							<select name="id_forum" id="id_forum" class="form-control" REQUIRED>
								<option><?=_e('Select a forum')?></option>
								<?foreach ($forums as $f):?>
									<option value="<?=$f['id_forum']?>" <?=(core::request('id_forum')==$f['id_forum'])?'selected':''?>>
										<?=$f['name']?></option>
								<?endforeach?>
							</select>
						</dd>
					</dl>
					<dl>
						<dt><?= FORM::label('description', _e('Description'), array('class'=>'control-label', 'for'=>'description'))?></dt>
						<dd><?= FORM::textarea('description', core::post('description'), array('placeholder' => __('Description'), 'class' => 'form-control', 'name'=>'description', 'id'=>'description', 'required'))?></dd>
					</dl>
		
					<?if (core::config('advertisement.captcha') != FALSE OR core::config('general.captcha') != FALSE):?>
						<dl>
							<?if (Core::config('general.recaptcha_active')):?>
                                <?=View::factory('recaptcha', ['id' => 'recaptcha1'])?>
							<?else:?>
								<div class="form-captcha wide-view">
									<span class="cap_note text-center"><?= FORM::label('captcha', _e('Captcha'), array('for'=>'captcha'))?></span>
									<span class="cap_img"><?=captcha::image_tag('new-forum')?></span>
									<span class="cap_ans"><?= FORM::input('captcha', "", array('class' => 'form-control', 'id' => 'captcha', 'required'))?></span>
								</div>
							<?endif?>
						</dl>
					<?endif?>
			
					<dl>
						<dt><label>&nbsp;</label></dt>
						<dd class="text-center"><?= FORM::button(NULL, _e('Publish new topic'), array('type'=>'submit', 'class'=>'btn btn-success', 'action'=>Route::url('forum-new')))?></dd>
					</dl>
				</fieldset>
				<?= FORM::close()?>
			</div>
		</div>
	</div>
</div>