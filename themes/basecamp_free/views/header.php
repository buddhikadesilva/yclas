<?php defined('SYSPATH') or die('No direct script access.');?>

<!-- HEADER -->
<div class="site_header not_fixed">
<!-- MAIN NAV -->
	<div class="container navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand logo <?=(Theme::get('logo_url')!=''?'logo_img':'')?>" href="<?=Route::url('default')?>" title="<?=HTML::chars(core::config('general.site_name'))?>" alt="<?=HTML::chars(core::config('general.site_name'))?>">
                <h1><?=core::config('general.site_name')?></h1>
            </a>

            <?
			$cats = Model_Category::get_category_count();
            $loc_seoname = NULL;

            if (Model_Location::current()->loaded())
                $loc_seoname = Model_Location::current()->seoname;
			?>

			<div class="collapse navbar-collapse navbar-right" id="mobile-menu-panel">
				<ul class="nav navbar-nav">
					<?if (class_exists('Menu') AND core::count( $menus = Menu::get() )>0 ):?>
	                    <?foreach ($menus as $menu => $data):?>
	                        <li class="<?=(Request::current()->uri()==$data['url'])?'active':''?>" >
	                        <a href="<?=$data['url']?>" target="<?=$data['target']?>">
	                            <?if($data['icon']!=''):?><i class="<?=$data['icon']?>"></i> <?endif?>
	                            <?=$data['title']?></a>
	                        </li>
	                    <?endforeach?>
	                <?else:?>
						<?=Theme::nav_link(_e('Listing'),'ad', '' ,'listing', 'list')?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=_e('Categories')?> <b class="caret"></b></a>
	                        <ul class="dropdown-menu">
	                            <?foreach($cats as $c ):?>
	                                <?if($c['id_category_parent'] == 1 && $c['id_category'] != 1):?>
	                                    <li class="dropdown-submenu">
	                                        <a tabindex="-1" title="<?=HTML::chars($c['seoname'])?>" href="<?=Route::url('list', array('category'=>$c['seoname'],'location'=>$loc_seoname))?>">
	                                            <?=$c['translate_name']?>
	                                        </a>
	                                        <?if($c['id_category_parent'] == 1):?>
	                                            <?$i = 0; foreach($cats as $chi):?>
	                                                <?if($chi['id_category_parent'] == $c['id_category']):?>
	                                                    <?$i++; if($i == 1):?>
	                                                        <ul class="dropdown-menu">
	                                                    <?endif?>
	                                                    <li>
	                                                        <a title="<?=HTML::chars($chi['translate_name'])?>" href="<?=Route::url('list', array('category'=>$chi['seoname'],'location'=>$loc_seoname))?>">
	                                                            <?if (Theme::get('category_badge') != 1) : ?>
	                                                                <span class="pull-right badge badge-success"><?=number_format($chi['count'])?></span>
	                                                            <?endif?>
	                                                            <span class="<?=Theme::get('category_badge') != 1 ? 'badged-name' : NULL?>"><?=$chi['translate_name']?></span>
	                                                        </a>
	                                                    </li>
	                                                <?endif?>
	                                            <?endforeach?>
	                                            <?if($i > 0):?>
	                                                </ul>
	                                            <?endif?>
	                                        <?endif?>
	                                    </li>
	                                <?endif?>
	                            <?endforeach?>
	                        </ul>
						</li>
						<?if (core::config('general.blog')==1):?>
							<?=Theme::nav_link(_e('Blog'),'blog','','index','blog')?>
						<?endif?>
						<?if (core::config('general.faq')==1):?>
							<?=Theme::nav_link(_e('FAQ'),'faq','','index','faq')?>
						<?endif?>
						<?if (core::config('general.forums')==1):?>
							<?=Theme::nav_link(_e('Forum'),'forum','','index','forum-home')?>
						<?endif?>
						<?if (core::config('advertisement.map')==1):?>
							<?=Theme::nav_link(_e('Map'),'map', '', 'index', 'map')?>
						<?endif?>
						<?=Theme::nav_link(_e('Contact'),'contact', '', 'index', 'contact')?>
					<?endif?>
				</ul>
			</div>
		</div>
	</div>
<!-- // MAIN NAV -->

<!-- TOOL BAR -->
<div class="head_tool_bar">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="navbar-btn text-right">
					<?=View::factory('widget_login')?>
					<?if ((Core::config('advertisement.only_admin_post')!=1) OR (Core::config('advertisement.only_admin_post')==1 AND Auth::instance()->logged_in() AND (Auth::instance()->get_user()->is_admin() OR Auth::instance()->get_user()->is_moderator()))):?>
						<a class="btn btn-base-light" href="<?=Route::url('post_new')?>">
							<i class="glyphicon glyphicon-plus glyphicon"></i>
						</a>
					<?endif?>
					<button class="btn btn-base-light" type="button" data-toggle="collapse" data-target="#toolSearch" aria-expanded="false" aria-controls="toolSearch">
						<i class="glyphicon glyphicon-search glyphicon"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- // TOOL BAR -->

<!-- HIDDEN SEARCH BAR -->
<div class="collapse clearfix" id="toolSearch">
	<div class="tool_bar_search">
		<div class="container">
			<div class="row">
				<?if(Core::config('general.algolia_search') == 1):?>
					<div class="tool_bar_search_input">
						<div class="input-group col-md-12">
							<?=View::factory('pages/algolia/autocomplete')?>
						</div>
					</div>
				<?else:?>
					<?= FORM::open(Route::url('search'), array('class'=>'', 'method'=>'GET', 'action'=>''))?>
					<div class="tool_bar_search_input">
						<div class="input-group col-md-12">
							<input type="text" class="search-query form-control" name="search" placeholder="<?=__('Search')?>"  />
							<span class="input-group-btn"><?= FORM::button('submit', _e('Search'), array('type'=>'submit', 'class'=>'btn btn-default', 'action'=>Route::url('search')))?></span>
						</div>
					</div>
					<?= FORM::close()?>
				<?endif?>
			</div>
		</div>
	</div>
</div>
<!-- // HIDDEN SEARCH BAR -->

<!-- Breadcrumbs -->
<?=(Theme::get('breadcrumb')==1)?Breadcrumbs::render('breadcrumbs'):''?>
<!-- // Breadcrumbs -->

</div>
<!-- // HEADER -->


<?if (!Auth::instance()->logged_in()):?>
	<!-- POP UP MODALS - LOGIN - REGISTER - FORGOT PASS -->
	<div id="login-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal" >&times;</a>
					<h3 class="modal-title"><?=_e('Login')?></h3>
				</div>
					<?=View::factory('pages/auth/login-form')?>
			</div>
		</div>
	</div>
	<div id="forgot-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal" >&times;</a>
					<h3 class="modal-title"><?=_e('Forgot password')?></h3>
				</div>
					<?=View::factory('pages/auth/forgot-form')?>
			</div>
		</div>
	</div>
	<div id="register-modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<a class="close" data-dismiss="modal" >&times;</a>
					<h3 class="modal-title"><?=_e('Register')?></h3>
				</div>
					<?=View::factory('pages/auth/register-form', ['recaptcha_placeholder' => 'recaptcha4', 'modal_form' => TRUE])?>
			</div>
		</div>
	</div>
	<!-- // POP UP MODALS - LOGIN - REGISTER - FORGOT PASS -->
<?elseif(Core::config('general.pusher_notifications')):?>
    <div id="pusher-subscribe" class="hidden" data-user="<?=Auth::instance()->get_user()->email?>" data-key="<?=Core::config('general.pusher_notifications_key')?>" data-cluster="<?=Core::config('general.pusher_notifications_cluster')?>"></div>
<?endif?>
