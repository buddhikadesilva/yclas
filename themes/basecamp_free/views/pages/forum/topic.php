<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=$topic->title?></h3>
				</div>

				<div class="topic-view">
					<div class="col-xs-12">
						<div class="pad_10">
							<?=Text::bb2html($topic->description,TRUE)?>
						</div>
					</div>
					<div class="action-btns text-right">
						<?if (Auth::instance()->logged_in()):?>
							<a class="btn btn-success" href="#reply_form"><?=_e('Reply')?></a>
						<?else:?>
							<a class="btn btn-success" data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>#login-modal">
							<?=_e('Reply')?>
							</a>
						<?endif?>
						<?if(Auth::instance()->logged_in()):?>
							<?if(Auth::instance()->get_user()->id_role==Model_Role::ROLE_ADMIN):?>
								<a class="btn btn-warning" href="<?=Route::url('oc-panel', array('controller'=> 'topic', 'action'=>'update','id'=>$topic->id_post)) ?>">
									<?=_e('Edit')?>
								</a>
							<?endif?>
						<?endif?>
					</div>	
					<div class="col-xs-12 topic-author">
						<img class="auth-pic" src="<?=$topic->user->get_profile_image()?>" alt="<?=HTML::chars($topic->user->name)?>">
						<ul>
						<?if (in_array('profile', Route::all())) :?>
							<li><b><a href="<?=Route::url('profile', array('seoname'=>$topic->user->seoname)) ?>"><?=$topic->user->name?></a></b></li>
						<?else :?>
							<li><b><?=$topic->user->name?></b></li>
						<?endif?>
							<li><?=Date::fuzzy_span(Date::mysql2unix($topic->created))?></li>
							<li><?=$topic->created?></li>
						</ul>
					</div>
				</div>	
		
				<div class="clearfix"></div>

				<?if ($replies->count()>0):?>
				<div class="page-header text-center">
					<h1><?=_e('Comments')?></h1>
				</div>
				<?foreach ($replies as $reply):?>
					<div class="col-xs-12 forum-comment">
						<div class="comment-user-block">
							<div class="m-5">
								<img class="comment-auth" src="<?=$reply->user->get_profile_image()?>" alt="<?=HTML::chars($reply->user->name)?>">
							</div>
						</div>
						<div class="comment-content">
							<p class="comment-info">
							<?
								try {
							?>
							<a href="<?=Route::url('profile', array('seoname'=>$reply->user->seoname)) ?>"><?=$reply->user->name?></a>
							<?    
								} catch (Exception $e) {
									echo $reply->user->name;
							}?>
							<?=Date::fuzzy_span(Date::mysql2unix($reply->created))?>
							</p>
							<div class="comment-full">
								<?=Text::bb2html($reply->description,TRUE)?>
							</div>
							<div class="text-right">
								<?if(Auth::instance()->logged_in()):?>
									<?if(Auth::instance()->get_user()->id_role==Model_Role::ROLE_ADMIN):?>
										<a class="btn btn-warning" href="<?=Route::url('oc-panel', array('controller'=> 'topic', 'action'=>'update','id'=>$reply->id_post)) ?>">
										<?=_e('Edit')?>
										</a>
									<?endif?>
								<?endif?>
								<?if(Auth::instance()->logged_in()):?>
								<a  class="btn btn-success" href="#reply_form"><?=_e('Reply')?></a>
								<?endif?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				<?endforeach?>

				<div class="text-center">
					<?=$pagination?>
				</div>

				<?endif?>

				<?if($topic->status==Model_POST::STATUS_ACTIVE AND Auth::instance()->logged_in()):?>
				<form id="reply_form" method="post" action="<?=Route::url('forum-topic',array('seotitle'=>$topic->seotitle,'forum'=>$forum->seoname))?>"> 
					<?php if ($errors): ?>
						<div class="alert alert-danger text-center" role="alert">
							<p class="message"><?=_e('Some errors were encountered, please check the details you entered.')?></p>
							<ul class="errors">
								<?php foreach ($errors as $message): ?>
									<li><?php echo $message ?></li>
								<?php endforeach ?>
							</ul>
						</div>
					<?php endif?>       
					<div class="form-group">
						<div class="col-xs-12">
							<textarea name="description" rows="10" class="form-control input-xxlarge" required><?=core::post('description',_e('Reply here'))?></textarea>
						</div>
					</div>
					<?if (core::config('advertisement.captcha') != FALSE OR core::config('general.captcha') != FALSE):?>
						<div class="form-group">
							<div class="col-xs-12"><br>
								<?if (Core::config('general.recaptcha_active')):?>
									<div class="pad_10">
                                        <?=View::factory('recaptcha', ['id' => 'recaptcha1'])?>
									</div>
								<?else:?>
									<div class="form-captcha wide-view">
										<span class="cap_note text-center"><?= FORM::label('captcha', _e('Captcha'), array('for'=>'captcha'))?></span>
										<span class="cap_img"><?=captcha::image_tag('new-reply-topic')?></span>
										<span class="cap_ans"><?= FORM::input('captcha', "", array('class' => 'form-control', 'id' => 'captcha', 'required'))?></span>
									</div>
								<?endif?>
							<br></div>
						</div>
					<?endif?>
					<div class="form-group text-center">
						<button type="submit" class="btn btn-success"><?=_e('Reply')?></button>
					</div>
				</form> 

				<?else:?>

				<div class="login-required">
					<a class="btn btn-success btn-lg" data-toggle="modal" data-dismiss="modal" 
						href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>#login-modal">
						<?=_e('Login to reply')?>
					</a>
				</div>

				<?endif?>
			</div>
		</div>
	</div>
</div>	