<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<?if (core::get('status',-1)==Model_Message::STATUS_NOTREAD):?>	
					<h3><?=_e('Unread')?></h3>
				<?elseif (core::get('status',-1)==Model_Message::STATUS_ARCHIVED):?>
					<h3><?=_e('Archieved')?></h3>
				<?elseif (core::get('status',-1)==Model_Message::STATUS_SPAM):?>
					<h3><?=_e('Spam')?></h3>
				<?else:?>
					<h3><?=_e('Inbox')?></h3>
				<?endif?>
			</div>
	
			<?=Alert::show()?>
 
			<div class="inbox-top-menu text-right">
				<div class="btn-group">
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>" class="btn btn-base-dark <?=(!is_numeric(core::get('status')))?'active':''?>">
						<?=_e('All')?>
					</a>
					<a href="?status=<?=Model_Message::STATUS_NOTREAD?>" class="btn btn-base-dark <?=(core::get('status',-1)==Model_Message::STATUS_NOTREAD)?'active':''?>">
						<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> <?=_e('Unread')?>
					</a>
					<button type="button" class="btn btn-base-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="?status=<?=Model_Message::STATUS_ARCHIVED?>"><span class="glyphicon glyphicon-folder-close"></span> <?=_e('Archieved')?></a>
						</li>
						<li>
							<a href="?status=<?=Model_Message::STATUS_SPAM?>"><span class="glyphicon glyphicon-fire"></span> <?=_e('Spam')?></a>
						</li>
					</ul>
				</div>
			</div>
 
			<div class="col-sm-3 inbox-side-menu">
				<div class="list-group">
					<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>" class="list-group-item <?=(!is_numeric(core::get('status')))?'active':''?>"><?=_e('All')?></a>
					<a href="?status=<?=Model_Message::STATUS_NOTREAD?>" class="list-group-item <?=(core::get('status',-1)==Model_Message::STATUS_NOTREAD)?'active':''?>"><?=_e('Unread')?></a>
					<a href="?status=<?=Model_Message::STATUS_ARCHIVED?>" class="list-group-item <?=(core::get('status',-1)==Model_Message::STATUS_ARCHIVED)?'active':''?>"><?=_e('Archieved')?></a>
					<a href="?status=<?=Model_Message::STATUS_SPAM?>" class="list-group-item <?=(core::get('status',-1)==Model_Message::STATUS_SPAM)?'active':''?>"><?=_e('Spam')?></a>
				</div>
			</div>

			<div class="col-xs-12 col-sm-9 ">
				<?if (core::count($messages) > 0):?>
				<div class="list-group">
					<?foreach ($messages as $message):?>
					<div class="list-group-item clearfix" style="<?=($message->status_to == Model_Message::STATUS_NOTREAD AND $message->id_user_from != $user->id_user) ? 'font-weight: bold;' : NULL?>">
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="mess_pad">
									<?if ($message->status_to == Model_Message::STATUS_NOTREAD AND $message->id_user_from != $user->id_user) :?><i class="fa fa-envelope"></i><?else:?><i class="fa fa-envelope-o unread"></i><?endif?> 
										<a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'message','id'=>($message->id_message_parent != NULL) ? $message->id_message_parent : $message->id_message))?>">
											<?if(isset($message->ad->title)):?>
												<?=$message->ad->title?>
											<?else:?>
												<?=_e('Direct Message')?>
											<?endif?>
										</a>
								</div>
							</div>
							<div class="col-xs-12 col-sm-3">
								<div class="mess_pad">
									<a href="<?=Route::url('profile', array('seoname' => $message->from->seoname))?>"><?=$message->from->name?></a>
								</div>
							</div>
							<div class="col-xs-12 col-sm-3 text-right">
								<div class="mess_pad">
									<?=(empty($message->parent->read_date))?$message->parent->created:$message->created?>
								</div>
							</div>
						</div>
					</div>
					<?endforeach?>
				</div>
				<?else:?>
					<!-- No results? Lets tell the user -->
					<div class="no_results text-center">
						<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
						<p class="nr_info"><?=_e('You don’t have any messages yet.')?></p>
					</div>
				<?endif?>

				<?if(isset($pagination)):?>
					<div class="text-center">
						<?=$pagination?>
					</div>
				<?endif?>
			</div>

		</div>
	</div>
</div>