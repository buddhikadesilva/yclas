<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="<?=(Theme::get('sidebar_position')!='none')?'col-xs-9':'col-xs-12'?> <?=(Theme::get('sidebar_position')=='left')?'pull-right':'pull-left'?>">
				<div class="page-header">
					<h3>
						<?if(core::count($users)):?>
							<div class="btn-group pull-right">
								<button type="button" id="sort" data-sort="<?=HTML::chars(core::request('sort'))?>" class="btn btn-base-dark btn-sm dropdown-toggle" data-toggle="dropdown">
									<span class="glyphicon glyphicon-list-alt"></span> <?=_e('Sort')?> <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu" id="sort-list">
								 <?if (Core::config('advertisement.reviews')==1):?>
									<li><a href="?<?=http_build_query(['sort' => 'rating'] + Request::current()->query())?>"><?=_e('Rating')?></a></li>
								<?endif?>
									<li><a href="?<?=http_build_query(['sort' => 'name-asc'] + Request::current()->query())?>"><?=_e('Name (A-Z)')?></a></li>
									<li><a href="?<?=http_build_query(['sort' => 'name-desc'] + Request::current()->query())?>"><?=_e('Name (Z-A)')?></a></li>
									<li><a href="?<?=http_build_query(['sort' => 'created-desc'] + Request::current()->query())?>"><?=_e('Newest')?></a></li>
									<li><a href="?<?=http_build_query(['sort' => 'created-asc'] + Request::current()->query())?>"><?=_e('Oldest')?></a></li>
									<li><a href="?<?=http_build_query(['sort' => 'ads-desc'] + Request::current()->query())?>"><?=_e('More Ads')?></a></li>
									<li><a href="?<?=http_build_query(['sort' => 'ads-asc'] + Request::current()->query())?>"><?=_e('Less Ads')?></a></li>
								</ul>
							</div>
						 <?endif?>			
						<?=_e('Users')?>
					</h3>
				</div>

					<?=Form::errors()?>
					<?= FORM::open(Route::url('profiles'), array('class'=>'', 'method'=>'GET', 'action'=>''))?>
					<div class="input-group">
						<input type="text" class="form-control" id="search" name="search" placeholder="<?=__('Search')?>" type="search" value="<?=HTML::chars(core::request('search'))?>" />
							<div class="input-group-btn">
								<?= FORM::button('submit', _e('Search'), array('type'=>'submit', 'class'=>'btn btn-default', 'action'=>Route::url('profiles')))?> 
							</div>
					</div>
					<?= FORM::close()?>

					<?if(core::count($users)):?>

					<div class="clearfix"><br></div>
					<div id="users">
						<?$i = 1; foreach($users as $user ):?>
							<div class="user-block">
								<div class="ub-inner">
									<div class="thumbnail">
										<div class="thumb-img">
											<span class="badge badge-success"><?=$user->ads_count?> <?=_e('Ads')?></span>
											<a title="<?=HTML::chars($user->name)?>" href="<?=Route::url('profile',  array('seoname'=>$user->seoname))?>">
												<?=HTML::picture($user->get_profile_image(), ['w' => 200, 'h' => 200], ['1200px' => ['w' => '200', 'h' => '200'], '992px' => ['w' => '210', 'h' => '210'], '768px' => ['w' => '283', 'h' => '283'], '480px' => ['w' => '324', 'h' => '324'], '320px' => ['w' => '274', 'h' => '274']], ['class' => 'img-responsive'], ['alt' => __('Profile Picture')])?>
											</a>
											<p class="u-name">
												<?=$user->name?>
											</p>
											
										</div>	
									</div>
								</div>
							</div>
							<?if ($i%4 == 0) :?>
								<div class="clearfix"></div>
							<?endif?>
						<?$i++; endforeach?>
					</div>

					<div class="text-center pad_10">
						<?=$pagination?>
					</div>

					<?elseif (core::count($users) == 0):?>
						<div class="no_results text-center">
							<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
							<p class="nr_info"><?=_e('We do not have any users matching your search')?></p>
						</div>
					<?endif?>

			</div>
		
			<?=View::fragment('sidebar_front','sidebar')?>
	    </div>
	</div>
</div>	