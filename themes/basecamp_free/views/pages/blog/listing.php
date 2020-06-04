<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="<?=(Theme::get('sidebar_position')!='none')?'col-xs-9':'col-xs-12'?> <?=(Theme::get('sidebar_position')=='left')?'pull-right':'pull-left'?>">
				<div class="page-header">
					<h3>
						<button class="btn btn-base-dark btn-sm pull-right" type="button" data-toggle="collapse" data-target="#collapseBlogSearch" aria-expanded="false" aria-controls="collapseBlogSearch">
							<span class="glyphicon glyphicon-search"></span>
						</button>
						<?=_e('Blog')?>
					</h3>
				</div>

				<div class="collapse" id="collapseBlogSearch">
					<div class="well">
						<form action="<?=Route::URL('blog')?>" method="get">
							<div class="input-group">
								<input type="text" class="form-control" placeholder="<?=__('Search')?>..." value="<?=HTML::chars(core::get('search'))?>" name="search">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><?=_e('Search')?></button>
								</span>
							</div>
						</form>
					</div>
				</div>
				
				<!-- Blog Posts -->
				<?if(core::count($posts)):?>
					<?foreach($posts as $post ):?>
						<div class="blog-item panel panel-default">
							<div class="panel-heading">
								<h4><a title="<?=HTML::chars($post->title)?>" href="<?=Route::url('blog', array('seotitle'=>$post->seotitle))?>"> <?=$post->title; ?></a></h4>
							</div>
							<p class="post-info">
								<span class="glyphicon glyphicon-calendar"></span> <?=Date::format($post->created, core::config('general.date_format'))?>
							</p>
							<div class="panel-body">
								<div><?=Text::truncate_html($post->description, 255, NULL)?></div>
							</div>
							<div class="panel-footer text-right">
							<?if ($user !== NULL AND $user!=FALSE AND $user->id_role == Model_Role::ROLE_ADMIN):?>
								<a class="btn btn-warning" href="<?=Route::url('oc-panel', array('controller'=>'blog','action'=>'update','id'=>$post->id_post))?>"><i class="fa fa-edit"></i></a>
								<a class="btn btn-danger" href="<?=Route::url('oc-panel', array('controller'=>'blog','action'=>'delete','id'=>$post->id_post))?>" 
								onclick="return confirm('<?=__('Delete?')?>');"><i class="fa fa-trash"></i></a>
							<?endif?>
							<a class="btn btn-base-dark" title="<?=HTML::chars($post->title)?>" href="<?=Route::url('blog', array('seotitle'=>$post->seotitle))?>"><?=_e('Read more')?></a>
							</div>
						</div>
					<?endforeach?>
					<div class="text-center">
						<?=$pagination?>
					</div>
				<?else:?>
					<!-- No Blogs Found -->
					<div class="no_results text-center">
						<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
						<p class="nr_info"><?=_e('We do not have any blog posts')?></p>
					</div>
				<?endif?>
			</div>

			<?=View::fragment('sidebar_front','sidebar')?>
	    </div>
	</div>
</div>