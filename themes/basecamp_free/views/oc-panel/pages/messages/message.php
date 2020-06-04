<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<h3>
					<?if ($msg_thread->id_ad !== NULL):?>
						<?=$msg_thread->ad->title?>
					<?else:?>
						<?=sprintf(__('Direct message from %s to %s'), $msg_thread->from->name, $msg_thread->to->name);   ?>
					<?endif?>
				</h3>
			</div>	

			<?=Alert::show()?>

			<div class="col-sm-3 inbox-side-menu">
				<div class="list-group">
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>" class="list-group-item"><?=_e('All')?></a>
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>?status=<?=Model_Message::STATUS_NOTREAD?>" class="list-group-item"><?=_e('Unread')?></a>
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>?status=<?=Model_Message::STATUS_ARCHIVED?>" class="list-group-item"><?=_e('Archieved')?></a>
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>?status=<?=Model_Message::STATUS_SPAM?>" class="list-group-item"><?=_e('Spam')?></a>
				</div>
			</div>

<div class="col-xs-12 col-sm-9 ">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="media">
				<div class="media-left">
					<a href="#">
						<img src="<?=$msg_thread->from->get_profile_image()?>" class="media-object" height="50" width="50" title="<?=HTML::chars($msg_thread->from->name)?>">
					</a>
				</div>
				<div class="media-body">
					<h4 class="media-heading"><?=$msg_thread->from->name?></h4>
					<div class="pad_5 text-right">
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'status','id'=>$msg_thread->id_message))?>?status=<?=Model_Message::STATUS_ARCHIVED?>" title="<?=__('Archive')?>" class="btn btn-sm btn-primary">
						<i class="fa fa-inbox"></i>
					</a>
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'status','id'=>$msg_thread->id_message))?>?status=<?=Model_Message::STATUS_SPAM?>" title="<?=__('Spam')?>" class="btn btn-sm btn-warning"
						data-toggle="confirmation"
						data-text="<?=__('Are you sure you want to mark it as Spam?')?>" 
						data-btnOkLabel="<?=__('Yes, definitely!')?>" 
						data-btnCancelLabel="<?=__('No way!')?>">
						<i class="fa fa-flag"></i>
					</a>
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'status','id'=>$msg_thread->id_message))?>?status=<?=Model_Message::STATUS_DELETED?>" title="<?=__('Delete')?>" class="btn btn-sm btn-danger"
						data-toggle="confirmation"
						data-text="<?=__('Are you sure you want to delete?')?>" 
						data-btnOkLabel="<?=__('Yes, definitely!')?>" 
						data-btnCancelLabel="<?=__('No way!')?>">
						<i class="fa fa-trash"></i>
					</a>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-body">

		<?foreach ($messages as $message):?>
			<div class="convo_item">
            
				<div class="media">
					<div class="media-left">
						<a href="<?=Route::url('profile', array('seoname' => $message->from->seoname))?>">
							<img class="media-object" src="<?=$message->from->get_profile_image()?>" width="40" height="40" alt="<?=HTML::chars($message->from->name)?>">
						</a>
					</div>
					<div class="media-body">
						<div class="messContent">
							<?if ($message->id_user_from != $user->id_user):?>
								<strong><a href="<?=Route::url('profile', array('seoname' => $message->from->seoname))?>"><?=$message->from->name?></a></strong>
							<?else:?>
								<strong>You:</strong>
							<?endif?>
							<br>
							<?=Text::bb2html($message->message,TRUE)?>
						
							<?if ($message->price > 0):?>
								<p>
									<strong><?=_e('Price')?></strong>: <?=i18n::money_format($message->price)?>
								</p>
							<?endif?>
						</div>
						
					</div>
					<div class="text-right mess-date"><small><?=Date::fuzzy_span(Date::mysql2unix($message->created))?> - <?=$message->created?></small></div>
					</div>
			</div>
		<?endforeach?>
		
			<div>
				<form method="post" action="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'message','id'=>Request::current()->param('id')))?>"> 
				<?php if (isset($errors)): ?>
					<div class="alert alert-danger" role="alert">
						<p><?=_e('Some errors were encountered, please check the details you entered.')?></p>
						<ul>
							<?php foreach ($errors as $message): ?>
								<li><?php echo $message ?></li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>
				
				<div class="form-group control-group pad_10">
					<textarea name="message" rows="7" class="form-control input-xxlarge disable-bbcode" placeholder="Type reply here...." data-editor="html" required><?=core::post('message')?></textarea>
				</div>
				<div class="form-group">
					 <?=Form::token('reply_message')?>
				</div>
			</div>
		</div>
			<div class="panel-footer text-center">
				<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>" class="btn btn-default"><?=_e('Cancel')?></a>
				<button type="submit" class="btn btn-success"><?=_e('Reply')?></button>
			</div>
				</form>
	</div>
</div>

</div>
</div>
</div>
