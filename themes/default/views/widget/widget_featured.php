<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->featured_title!=''):?>
	<div class="panel-heading">
		<h3 class="panel-title"><?=$widget->featured_title?></h3>
	</div>
<?endif?>

<div class="panel-body">
	<?foreach($widget->ads as $ad):?>
		<div class="category_box_title custom_box "></div>
		<div class="well <?=(get_class($widget)=='Widget_Featured')?'featured-custom-box':''?>" >
			<div class="featured-sidebar-box">
				<?if($ad->get_first_image() !== NULL):?>
					<div class="picture pull-right col-xs-12 col-sm-3 <?=($widget->placeholder!='header')?'col-md-12':''?>">
						<a class="pull-right" title="<?=HTML::chars($ad->title);?>" alt="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
							<figure>
								<img src="<?=Core::imagefly($ad->get_first_image('image'),250,250)?>" alt="<?=HTML::chars($ad->title)?>" class="img-responsive" width="100%"/>
							</figure>
						</a>
					</div>
				<?else:?>
					<div class="picture pull-right col-xs-12 col-sm-3 <?=($widget->placeholder!='header')?'col-md-12':''?>">
						<a class="pull-right" title="<?=HTML::chars($ad->title);?>" alt="<?=HTML::chars($ad->title);?>" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>">
							<figure>
								<img data-src="holder.js/250x250?<?=str_replace('+', ' ', http_build_query(array('text' => $ad->category->name, 'size' => 14, 'auto' => 'yes')))?>" class="img-responsive" width="100%">
							</figure>
						</a>
					</div>
				<?endif?>
				<div class="featured-sidebar-box-header">
					<a href="<?=Route::url('ad',array('seotitle'=>$ad->seotitle,'category'=>$ad->category->seoname))?>" title="<?=HTML::chars($ad->title)?>">
						<?if($widget->placeholder!='header'):?>
							<span class="f-box-header col-xs-12 col-sm-9 col-md-12"><?=Text::limit_chars(Text::removebbcode($ad->title), 30, NULL, TRUE)?></span>
						<?else:?>
							<span class="f-box-header col-xs-12 col-sm-9"><?=Text::limit_chars(Text::removebbcode($ad->title), 45, NULL, TRUE)?></span>
						<?endif?>
			        </a>
			    </div>
				<div class="f-description">
					<?if($widget->placeholder!='header'):?>
						<p class="col-xs-12 col-sm-9 col-md-12"><?=Text::limit_chars(Text::removebbcode($ad->description), 30, NULL, TRUE)?></p>
					<?else:?>
						<p class="col-xs-12 col-sm-9"><?=Text::limit_chars(Text::removebbcode($ad->description), 150, NULL, TRUE)?></p>
					<?endif?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	<?endforeach?>
</div>