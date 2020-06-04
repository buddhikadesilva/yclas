<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=_e("Forums")?></h3>
				</div>

				<form action="<?=Route::URL('forum-home')?>" method="get">
					<div class="input-group">
						<input type="text" class="form-control" id="task-table-filter" placeholder="<?=__('Search')?>" type="search" value="<?=HTML::chars(core::get('search'))?>" name="search" />
						<div class="input-group-btn">
							<button class="btn btn-default" type="submit" value="<?=__('Search')?>"><span class="glyphicon glyphicon-search"></span></button>
						</div>
					</div>
				</form>

				<br>
			
				<div class="text-right">
					<?if (!Auth::instance()->logged_in()):?>
						<a class="btn btn-success" data-toggle="modal" data-dismiss="modal" 
						href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'login'))?>#login-modal" title="<?=__('New Topic')?>">
					<?else:?>
						<a class="btn btn-success" href="<?=Route::url('forum-new')?>" title="<?=__('New Topic')?>">
					<?endif?>
						<i class="fa fa-plus"></i> <?=_e('New Topic')?></a>
				</div>

				<br>

				<table class="table table-striped table-hover" id="forum-table">
					<thead>
						<tr>
							<th></th>
							<th class="hidden-xs text-center"><?=_e('Last Message')?></th>
							<th class="hidden-xs text-center"><?=_e('Topics')?></th>
						</tr>
					</thead>
					<tbody>
					<?foreach($forums as $f):?>
						<?if($f['id_forum_parent'] == 0):?>
							<tr>
								<td><i class="fa fa-ellipsis-v"></i> <a title="<?=HTML::chars($f['name'])?>" href="<?=Route::url('forum-list', array('forum'=>$f['seoname']))?>"><?=$f['name'];?></a></td>
								<td width="180" class="hidden-xs text-center"><?=(isset($f['last_message'])?Date::format($f['last_message']):'')?></td>
								<td width="140" class="hidden-xs text-center"><?=number_format($f['count'])?></td>
							</tr>
							<?foreach($forums as $fhi):?>
								<?if($fhi['id_forum_parent'] == $f['id_forum']):?>
									<tr class="sibling-forum">
										<th>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-ellipsis-v"></i> <a title="<?=HTML::chars($fhi['name'])?>" href="<?=Route::url('forum-list', array('forum'=>$fhi['seoname']))?>"><?=$fhi['name'];?></a></th>
										<th width="140" class="hidden-xs text-center"><?=(isset($fhi['last_message'])?Date::format($fhi['last_message']):'')?></th>
										<th width="100" class="hidden-xs text-center"><?=number_format($fhi['count'])?></th>
									</tr>
								<?endif?>
							<?endforeach?>
						<?endif?>
					<?endforeach?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>