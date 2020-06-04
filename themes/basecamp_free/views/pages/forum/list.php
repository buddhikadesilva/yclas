<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=$forum->name?></h3>
					<div class="pad_5tb">
						<small><?=Text::bb2html($forum->description,TRUE)?></small>
					</div>
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
						<a class="btn btn-success" href="<?=Route::url('forum-new')?>?id_forum=<?=$forum->id_forum?>" title="<?=__('New Topic')?>">
					<?endif?>
						<i class="fa fa-plus"></i> <?=_e('New Topic')?></a>
				</div>
				
				<div class="clearfix"></div>

				<br>

				<div class="topic-row header">
					<div class="topic-title">
						<span><?=_e('Topic')?></span>
					</div>
					<div class="topic-date">
						<span><?=_e('Created')?></span>
					</div>
					<div class="topic-date">
						<span><?=_e('Last Message')?></span>
					</div>
					<div class="topic-replies">
						<span><?=_e('Replies')?></span>
					</div>
					<div class="clear"></div>
				</div>

				<?if(core::count($topics)):?>
					<?foreach($topics as $topic):?>
					<?
					//amount answers a topic got
					$replies = ($topic->count_replies>0)?$topic->count_replies:0;
					$page = '';
					//lets drive the user to the last page
					if ($replies>0)
						{
							$last_page = round($replies/Controller_Forum::$items_per_page,0);
							$page = ($last_page>0) ?'?page=' . $last_page : '';
						}
					?>
					
						<div class="topic-row">
							<div class="topic-title">
								<span><a title="<?=HTML::chars($topic->title)?>" href="<?=Route::url('forum-topic', array('forum'=>$forum->seoname,'seotitle'=>$topic->seotitle))?><?=$page?>"><?=$topic->title;?></a></span>
							</div>
							<div class="topic-date">
								<span><i class="fa fa-calendar"></i> <?=Date::format($topic->created)?></span>
							</div>
							<div class="topic-date">
								<span><?if ($replies>0):?><i class="fa fa-share"></i> <?=Date::format($topic->last_message)?><?else:?>- -<?endif?></span>
							</div>
							<div class="topic-replies">
								<span><i class="fa fa-comment-o"></i> <?=$replies?></span>
							</div>
						<div class="clear"></div>
						</div>
					<?endforeach?>
				<?elseif (core::count($topics) == 0):?>
					<div class="no_results text-center">
						<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
						<p class="nr_info"><?=_e('There are no topics in this section..')?></p>
					</div>
				<?endif?>

				<div class="text-center">
					<?=$pagination?>
				</div>

			</div>
		</div>
	</div>
</div>	