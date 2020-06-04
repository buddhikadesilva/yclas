<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=_e('Search')?>: <?=HTML::chars(core::get('search'))?></h3>
				</div>

				<form  action="<?=Route::URL('forum-home')?>" method="get">
					<div class="input-group">
						<input type="text" class="form-control" id="task-table-filter" data-action="filter" data-filters="#task-table" placeholder="<?=__('Search')?>" type="search" value="<?=HTML::chars(core::get('search'))?>" name="search" />
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

				<div class="clearfix"></div>

				<br>

				<div class="forum-topics search-topics">	
					<div class="topic-row header">
						<div class="topic-title">
							<span><?=_e('Topic')?></span>
						</div>
						<div class="topic-forum">
							<span><?=_e('Forum')?></span>
						</div>
						<div class="topic-date">
							<span><?=_e('Last Message')?></span>
						</div>
						<div class="clear"></div>
					</div>

					<?if ($topics->count()>0):?>
						<?foreach($topics as $topic):?>
							<?
								if (is_numeric($topic->id_post_parent))
								{
									$title      = $topic->parent->title;
									$seotitle   = $topic->parent->seotitle;
								}
								else
								{
									$title      = $topic->title;
									$seotitle   = $topic->seotitle;
								}
								?>
								
								<div class="topic-row">
									<div class="topic-title">
										<span><a title="<?=HTML::chars($title)?>" href="<?=Route::url('forum-topic', array('forum'=>$topic->forum->seoname,'seotitle'=>$seotitle))?>"><?=$topic->title;?></a></span>
									</div>
									<div class="topic-forum">
										<span><a title="<?=HTML::chars($topic->forum->name)?>" href="<?=Route::url('forum-list', array('forum'=>$topic->forum->seoname))?>"><?=$topic->forum->name?></a></span>
									</div>
									<div class="topic-date">
										<span><i class="fa fa-calendar"></i> <?=Date::format($topic->created)?></span>
									</div>
									<div class="clear"></div>
								</div>
						<?endforeach?>
					<?else:?>
						<div class="no_results text-center">
							<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
							<p class="nr_info"><?=_e('Nothing found, sorry!')?><br><?=_e('You can try a new search or publish a new topic ;)')?></p>
						</div>
					<?endif?>
				</div>
			</div>
		</div>
	</div>
</div>