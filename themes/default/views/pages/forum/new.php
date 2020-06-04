<div class="page-header">
    <h1><?=__("New Forum Topic")?></h1>
</div>

<div class="well">
	<?php if ($errors): ?>
    <div class="alert alert-warning">
	    <?=__('Some errors were encountered, please check the details you entered.')?>
	    <ul class="errors">
		    <?php foreach ($errors as $message): ?>
		        <li><?php echo $message ?></li>
		    <?php endforeach ?>
	    </ul>
    </div>
    <?php endif ?>       
	<?=FORM::open(Route::url('forum-new'), array('class'=>'form-horizontal', 'enctype'=>'multipart/form-data'))?>
	<fieldset>

        <div class="form-group control-group">
            <?= FORM::label('id_forum', __('Forum'), array('class'=>'col-md-2 control-label', 'for'=>'id_forum' ))?>
            <div class="col-md-6 controls">
                <select name="id_forum" id="id_forum" class="form-control input-xlarge" REQUIRED>
                    <option><?=__('Select a forum')?></option>
                    <?foreach ($forums as $f):?>
                        <option value="<?=$f['id_forum']?>" <?=(core::request('id_forum')==$f['id_forum'])?'selected':''?>>
                            <?=$f['name']?></option>
                    <?endforeach?>
                </select>
            </div>
        </div>

		<div class="form-group control-group">
			<?= FORM::label('title', __('Title'), array('class'=>'col-md-2 control-label', 'for'=>'title'))?>
			<div class="col-md-6 controls">
				<?= FORM::input('title', core::post('title'), array('placeholder' => __('Title'), 'class' => 'form-control input-xlarge', 'id' => 'title', 'required'))?>
			</div>
		</div>
		<div class="form-group control-group">
			<?= FORM::label('description', __('Description'), array('class'=>'col-md-2 control-label', 'for'=>'description'))?>
			<div class="col-md-6 controls">
				<?= FORM::textarea('description', core::post('description'), array('placeholder' => __('Description'), 'class' => 'form-control input-xxlarge', 'name'=>'description', 'id'=>'description', 'required'))?>	
			</div>
		</div>
		
		<?if (core::config('advertisement.captcha') != FALSE):?>
		<div class="form-group control-group">
		    <div class="col-md-6 col-md-offset-2 controls">
		        <?if (Core::config('general.recaptcha_active')):?>
                    <?=View::factory('recaptcha', ['id' => 'recaptcha1'])?>
                <?else:?>
		            <?=__('Captcha')?>*:<br />
		            <?=captcha::image_tag('new-forum')?><br />
		            <?= FORM::input('captcha', "", array('class' => 'form-control input-xlarge', 'id' => 'captcha', 'required'))?>
		        <?endif?>
		    </div>
		</div>
		<?endif?>
		<div class="clearfix"></div><br>
		<div class="form-group control-group">
			<div class="col-md-6 col-md-offset-2 controls">
				<?= FORM::button(NULL, __('Publish new topic'), array('type'=>'submit', 'class'=>'btn btn-success', 'action'=>Route::url('forum-new')))?>
			</div>
		</div>
	</fieldset>
	<?= FORM::close()?>

</div><!--end span10-->