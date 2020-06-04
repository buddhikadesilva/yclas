<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=_e('Frequently Asked Questions')?></h3>
				</div>
			
				<?if(core::count($faqs)):?>
				<ol class="faq-list">
					<?foreach($faqs as $faq ):?>
						<li>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4><a title="<?=HTML::chars($faq->title)?>" href="<?=Route::url('faq', array('seotitle'=>$faq->seotitle))?>"> <?=$faq->title?></a></h4>
								</div>
								<div class="panel-body">
									<?=Text::limit_chars(Text::removebbcode($faq->description),400, NULL, TRUE);?>
								</div>
								<div class="panel-footer text-right">
									<a class="btn btn-default" title="<?=HTML::chars($faq->title)?>" href="<?=Route::url('faq', array('seotitle'=>$faq->seotitle))?>"><?=_e('Read more')?>.</a>
								</div>
							</div>	
						</li>
					<?endforeach?>
				</ol>
				
				<?else:?>
		
				<div class="no_results text-center">
					<span class="nr_badge"><i class="glyphicon glyphicon-info-sign glyphicon"></i></span>
					<p class="nr_info"><?=_e('We do not have any FAQ-s')?></p>
				</div>

				<?endif?>
			</div>
		</div>
	</div>
</div>