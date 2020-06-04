<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<h3><?=_e('Sales')?></h3>
			</div>

			<?foreach($orders as $order):?>
				<div class="my_ad_item">
					<div class="my_ad_body clearfix">
						<div class="ad_pcoll">
							<div class="pad_10">
							<?if($order->ad->get_first_image() !== NULL):?>
								<img src="<?=$order->ad->get_first_image()?>" alt="<?=HTML::chars($order->ad->title)?>" />
							<?else:?>
								<img data-src="holder.js/<?=core::config('image.width_thumb')?>x<?=core::config('image.height_thumb')?>?<?=str_replace('+', ' ', http_build_query(array('text' => $order->ad->category->translate_name(), 'size' => 14, 'auto' => 'yes')))?>" alt="<?=HTML::chars($order->ad->title)?>">
							<?endif?>
							</div>
						</div>
						<div class="ad_dcoll">
							<div class="pad_10">
								<div class="my_ad_title clearfix">
									<a class="at" href="<?=Route::url('ad', array('controller'=>'ad','category'=>$order->ad->category->seoname,'seotitle'=>$order->ad->seotitle))?>"><?=$order->ad->title?> (#<?=$order->pk()?>)</a>
									<?if (isset($order->ad->cf_file_download)):?>
				                        <a class="btn btn-sm btn-success" href="<?=$order->ad->cf_file_download?>">
				                            <?=_e('Download')?>
				                        </a>
				                    <?endif?>

                                    <?if ($order->paymethod == 'escrow'):?>
                                        <? $transaction = json_decode($order->txn_id) ?>

                                        <?if (isset($transaction->status) AND ! $transaction->status->shipped):?>
                                            <a class="btn btn-default" href="<?= Route::url('oc-panel', ['controller'=>'escrow', 'action'=>'ship', 'id' => $order->id_order]) ?>">
                                                <i class="glyphicon glyphicon-check"></i> <?=_e('Mark as shipped')?>
                                            </a>
                                        <?endif?>
                                    <?endif?>
                                </div>
								<p><b><?=_e('User')?> : </b><a href="<?=Route::url('profile', array('seoname'=> $order->user->seoname)) ?>" ><?=$order->user->name?></a></p>
								<p><b><?=_e('Date')?> : </b><?=$order->pay_date?></p>
								<p><b><?=_e('Price')?> : </b><?=i18n::format_currency($order->amount, $order->currency)?></p>
							</div>
						</div>
					</div>
				</div>
			<?endforeach?>

			<div class="text-center">
				<?=$pagination?>
			</div>
		</div>
	</div>
</div>