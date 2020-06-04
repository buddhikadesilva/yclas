<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="<?=(Theme::get('sidebar_position')!='none')?'col-xs-9':'col-xs-12'?> <?=(Theme::get('sidebar_position')=='left')?'pull-right':'pull-left'?>">
				<?if ($category!==NULL):?>
					<div class="page-header">
						<h3><?=$category->translate_name()?></h3>
					</div>
				<?elseif ($location!==NULL):?>
					<div class="page-header">
						<h3><?=$location->translate_name()?></h3>
					</div>
				<?endif?>

				<!-- CAT or LOC DESCRIPTION -->
				<?if ($category!==NULL && $category->translate_description() !==NULL):?>
					<div class="cat_loc_desc">
						<p><?=$category->translate_description()?></p>
					</div>
				<?elseif ($location!==NULL && $location->translate_description() !==NULL):?>
					<div class="cat_loc_desc">
						<p><?=$location->translate_description()?></p>
					</div>
				<?endif?>
				<!-- // CAT or LOC DESCRIPTION -->

				<!-- ADS AVAILABLE SO LETS SHOW THEM -->
				<?if(core::count($ads)):?>
					<!-- FILTER OPTIONS -->
					<div class="listing_filter">
						<div class="loc_opts btn-group">
						<?if(core::config('general.auto_locate')):?>
							<button
							class="btn btn-sm btn-base-dark <?=core::request('userpos') == 1 ? 'active' : NULL?>"
								id="myLocationBtn"
								type="button"
								data-toggle="modal"
								data-target="#myLocation"
								data-marker-title="<?=__('My Location')?>"
								data-marker-error="<?=__('Cannot determine address at this location.')?>"
								data-href="?<?=http_build_query(['userpos' => 1] + Request::current()->query())?>">
								<i class="glyphicon glyphicon-map-marker"></i> <?=sprintf(__('%s from you'), i18n::format_measurement(Core::config('advertisement.auto_locate_distance', 1)))?>
							</button>
						<?endif?>
						<?if (core::config('advertisement.map')==1):?>
							<a href="<?=Route::url('map')?>?category=<?=Model_Category::current()->loaded()?Model_Category::current()->seoname:NULL?>&location=<?=Model_Location::current()->loaded()?Model_Location::current()->seoname:NULL?>"
								class="btn btn-sm btn-base-dark">
								<span class="glyphicon glyphicon-globe"></span> <?=_e('Map')?>
							</a>
						<?endif?>
						</div>

						<div class="sort_opts btn-group ">
							<a class="btn btn-sm btn-base-dark <?=(core::cookie('list/grid')==0)?'active':''?>" id="grid" href="#"><span class="glyphicon glyphicon-th-large"></span></a>
							<a class="btn btn-sm btn-base-dark <?=(core::cookie('list/grid')==1)?'active':''?>" id="list" href="#"><span class="glyphicon glyphicon-th-list"></span></a>
						<button type="button" id="sort" data-sort="<?=HTML::chars(core::request('sort'))?>" class="btn btn-sm btn-base-dark dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-sort-by-attributes-alt"></span> <?=_e('Sort')?> <span class="caret"></span>
						</button>
							<ul class="dropdown-menu" role="menu" id="sort-list">
								<?if((New Model_Field())->get('eventdate')):?>
					                <li><a href="?<?=http_build_query(['sort' => 'event-date'] + Request::current()->query())?>"><?=_e('Event date')?></a></li>
					            <?endif?>
								<li><a href="?<?=http_build_query(['sort' => 'title-asc'] + Request::current()->query())?>"><?=_e('Name (A-Z)')?></a></li>
								<li><a href="?<?=http_build_query(['sort' => 'title-desc'] + Request::current()->query())?>"><?=_e('Name (Z-A)')?></a></li>
								<?if(core::config('advertisement.price')!=FALSE):?>
								<li><a href="?<?=http_build_query(['sort' => 'price-asc'] + Request::current()->query())?>"><?=_e('Price (Low)')?></a></li>
								<li><a href="?<?=http_build_query(['sort' => 'price-desc'] + Request::current()->query())?>"><?=_e('Price (High)')?></a></li>
								<?endif?>
								<li><a href="?<?=http_build_query(['sort' => 'featured'] + Request::current()->query())?>"><?=_e('Featured')?></a></li>
								<li><a href="?<?=http_build_query(['sort' => 'favorited'] + Request::current()->query())?>"><?=_e('Favorited')?></a></li>
								<?if(core::config('general.auto_locate')):?>
								<li><a href="?<?=http_build_query(['sort' => 'distance'] + Request::current()->query())?>" id="sort-distance"><?=_e('Distance')?></a></li>
								<?endif?>
								<li><a href="?<?=http_build_query(['sort' => 'published-desc'] + Request::current()->query())?>"><?=_e('Newest')?></a></li>
								<li><a href="?<?=http_build_query(['sort' => 'published-asc'] + Request::current()->query())?>"><?=_e('Oldest')?></a></li>
							</ul>
						</div>

						<div class="clearfix"></div>
					</div>
					<div class="text-right">
						<div class="btn-group">
							<button class="btn btn-base-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?=_e('Show').' '.HTML::chars(core::request('items_per_page')).' '._e('items per page')?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu" id="show-list">
								<li><a href="?<?=http_build_query(['items_per_page' => '5'] + Request::current()->query())?>">  5 <?=_e('per page')?></a></li>
								<li><a href="?<?=http_build_query(['items_per_page' => '10'] + Request::current()->query())?>"> 10 <?=_e('per page')?></a></li>
								<li><a href="?<?=http_build_query(['items_per_page' => '20'] + Request::current()->query())?>"> 20 <?=_e('per page')?></a></li>
								<li><a href="?<?=http_build_query(['items_per_page' => '50'] + Request::current()->query())?>"> 50 <?=_e('per page')?></a></li>
								<li><a href="?<?=http_build_query(['items_per_page' => '100'] + Request::current()->query())?>">100 <?=_e('per page')?></a></li>
							</ul>
						</div>
					</div>
				<!-- // FILTER OPTIONS -->

				<!-- AD LIST -->
				<div class="ad_listings">
					<ul class="ad_list list clearfix">
						<?$ci=0; foreach($ads as $ad ):?>
						<?if($ci%3==0 OR $ci==0):?><div class="row"><?endif?>
						<?if($ad->featured >= Date::unix2mysql(time())):?>
						<li class="<?=(core::cookie('list/grid')==0)?'col-lg-4 col-md-4 col-sm-4 col-xs-10 ad_griditem':'ad_listitem'?> clearfix featured_ad">
							<span class="feat_marker"><i class="glyphicon glyphicon-bookmark"></i></span>
						<?else:?>
						<li class="<?=(core::cookie('list/grid')==0)?'col-lg-4 col-md-4 col-sm-4 col-xs-10 ad_griditem':'ad_listitem'?> clearfix">
						<?endif?>
							<div class="ad_inner">
								<div class="ad_photo">
									<div class="ad_photo_inner">
										<a title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
											<?if($ad->get_first_image() !== NULL):?>
												<?=HTML::picture($ad->get_first_image(), ['w' => 180, 'h' => 180], ['320px' => ['w' => '180', 'h' => '180']], ['class' => 'img-responsive'], ['alt' => HTML::chars($ad->title)])?>
											<?else:?>
												<img data-src="holder.js/180x180?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->translate_name(), 'size' => 14, 'auto' => 'yes')))?>" class="img-responsive" alt="<?=HTML::chars($ad->title)?>">
											<?endif?>
											<span class="gallery_only fm"><i class="glyphicon glyphicon-bookmark"></i></span>
											<?if ($ad->price!=0):?>
												<span class="gallery_only ad_gprice"><span class="price-curry"><?=i18n::money_format( $ad->price, $ad->currency())?></span></span>
											<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
												<span class="gallery_only ad_gprice"><?=_e('Free');?></span>
											<?else:?>
												<span class="gallery_only ad_gprice"><?=_e('Check Listing');?></span>
											<?endif?>
										</a>
									</div>
								</div>

								<div class="ad_details">
									<div class="ad_details_inner">
										<h2>
											<a title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
											<?=$ad->title?>
											</a>
										</h2>
										<p class="ad_meta clearfix">
											<?if ($ad->published!=0){?>
												<span><i class="glyphicon glyphicon-calendar"></i> <?=Date::format($ad->published, core::config('general.date_format'))?></span>
											<? }?>
											<?if ($ad->id_location AND core::request('sort') == 'distance' AND Model_User::get_userlatlng()) :?>
												<span> - <i class="glyphicon glyphicon-map-marker"></i> <?=i18n::format_measurement($ad->distance)?> away</span>
											<?endif?>
										</p>

										<?if(core::config('advertisement.description')!=FALSE):?>
											<p class="ad_desc"><?=(core::cookie('list/grid')==1)?Text::limit_chars(Text::removebbcode($ad->description), 255, NULL, TRUE) : Text::limit_chars(Text::removebbcode($ad->description), 30, NULL, TRUE)?></p>
										<?endif?>

									</div>
								</div>
								<div class="ad_buttons">
									<?if ($user !== NULL AND ($user->id_role == Model_Role::ROLE_ADMIN OR $user->id_role == Model_Role::ROLE_MODERATOR )):?>
										<span class="ad_options">
											<a class="btn btn-warning" data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('controller'=>'myads','action'=>'index'))?>#adcontrol<?=$ad->id_ad?>-modal"><i class="glyphicon glyphicon-cog"></i></a>
										</span>

										<div id="adcontrol<?=$ad->id_ad?>-modal" class="modal fade">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<a class="close" data-dismiss="modal" >Cancel</a>
														<br />
														<ul class="ad_controls_list">
															<li><a class="btn btn-success" href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"><i class="glyphicon glyphicon-edit"></i> <?=__("Edit");?></a></li>
															<li><a class="btn btn-warning" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$ad->id_ad))?>" onclick="return confirm('<?=__('Deactivate?')?>');"><i class="glyphicon glyphicon-off"></i> <?=_e("Deactivate");?></a></li>
															<li><a class="btn btn-danger" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$ad->id_ad))?>" onclick="return confirm('<?=__('Spam?')?>');"><i class="glyphicon glyphicon-fire"></i> <?=_e("Spam");?></a></li>
															<li><a class="btn btn-danger" href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$ad->id_ad))?>" onclick="return confirm('<?=__('Delete?')?>');"><i class="glyphicon glyphicon-remove"></i> <?=_e("Delete");?></a></li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									<?elseif($user !== NULL && $user->id_user == $ad->id_user):?>
										<span class="ad_options">
											<a class="btn btn-warning" data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('controller'=>'myads','action'=>'index'))?>#adcontrol<?=$ad->id_ad?>-modal"><i class="glyphicon glyphicon-cog"></i></a>
										</span>

										<div id="adcontrol<?=$ad->id_ad?>-modal" class="modal fade">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<a class="close" data-dismiss="modal" >Cancel</a>
														<br />
														<ul class="ad_controls_list">
															<li><a class="btn btn-success" href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"><i class="glyphicon glyphicon-edit"></i> <?=__("Edit");?></a></li>
															<li><a class="btn btn-warning"  href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'deactivate','id'=>$ad->id_ad))?>" onclick="return confirm('<?=__('Deactivate?')?>');"><i class="glyphicon glyphicon-off"></i> <?=_e("Deactivate");?></a></li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									<?endif?>

									<?if ($ad->price!=0):?>
										<span class="ad_price">
											<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
											<?=_e('Price');?>: <b><span class="price-curry"><?=i18n::money_format( $ad->price, $ad->currency())?></span></b>
											</a>
										</span>
									<?elseif (($ad->price==0 OR $ad->price == NULL) AND core::config('advertisement.free')==1):?>
										<span class="ad_price">
										<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
											<b><?=_e('Free');?></b>
										</a>
										</span>
									<?else:?>
										<span class="ad_price na">
											<a class="add-transition" title="<?=HTML::chars($ad->title)?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">Check Listing</a>
										</span>
									<?endif?>
								</div>
							</div>
						</li>
						<?$ci++;?>
						<?if($ci%3==0):?></div><div class="clearfix">&nbsp;</div><?endif?>
						<?endforeach?>
					</ul>
				</div>
				<!-- // AD LIST -->

				<div class="text-center">
					<?=$pagination?>
				</div>

				<?else:?>

					<!-- NO ADS -->
					<div class="no_results text-center">
						<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
						<p class="nr_info"><?=_e('We do not have any advertisements in this category')?></p>
						<?if (Core::config('advertisement.only_admin_post')!=1):?>
							<a class="btn btn-base-dark" title="<?=__('New Advertisement')?>"
								href="<?=Route::url('post_new')?>?category=<?=($category!==NULL)?$category->seoname:''?>&location=<?=($location!==NULL)?$location->seoname:''?>">
								<i class="glyphicon glyphicon-pencil"></i> <?=_e('Publish new advertisement')?>
							</a>
						<?endif?>
					</div>
					<!-- // NO ADS -->
				<?endif?>
			</div>

            <?=View::fragment('sidebar_front','sidebar')?>

        </div>

	</div>
</div>

<?if(core::config('general.auto_locate')):?>
	<div class="modal fade" id="myLocation" tabindex="-1" role="dialog" aria-labelledby="myLocationLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-distance btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="label-icon"><?=i18n::format_measurement(Core::cookie('mydistance', Core::config('advertisement.auto_locate_distance', 2)))?></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-left" role="menu">
								<li>
									<a href="#" data-value="2"><?=i18n::format_measurement(2)?></a>
								</li>
								<li>
									<a href="#" data-value="5"><?=i18n::format_measurement(5)?></a>
								</li>
								<li>
									<a href="#" data-value="10"><?=i18n::format_measurement(10)?></a>
								</li>
								<li>
									<a href="#" data-value="20"><?=i18n::format_measurement(20)?></a>
								</li>
								<li>
									<a href="#" data-value="50"><?=i18n::format_measurement(50)?></a>
								</li>
								<li>
									<a href="#" data-value="250"><?=i18n::format_measurement(250)?></a>
								</li>
								<li>
									<a href="#" data-value="500"><?=i18n::format_measurement(500)?></a>
								</li>
							</ul>
						</div>
						<input type="hidden" name="distance" id="myDistance" value="<?=Core::cookie('mydistance', Core::config('advertisement.auto_locate_distance', 2))?>" disabled>
						<input type="hidden" name="latitude" id="myLatitude" value="" disabled>
						<input type="hidden" name="longitude" id="myLongitude" value="" disabled>
						<?=FORM::input('myAddress', Request::current()->post('address'), array('class'=>'form-control', 'id'=>'myAddress', 'placeholder'=>_e('Where do you want to search?')))?>
						<span class="input-group-btn">
							<button id="setMyLocation" class="btn btn-default" type="button"><?=_e('Ok')?></button>
						</span>
					</div>
					<br>
					<div id="mapCanvas"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?=_e('Close')?></button>
					<?if (core::request('userpos') == 1) :?>
						<a class="btn btn-danger" href="?<?=http_build_query(['userpos' => NULL] + Request::current()->query())?>"><?=_e('Remove')?></a>
					<?endif?>
				</div>
			</div>
		</div>
	</div>
<?endif?>
<?if (core::config('advertisement.map')==1):?>
    <?=View::factory('pages/ad/listing_map', compact('ads'))?>
<?endif?>
