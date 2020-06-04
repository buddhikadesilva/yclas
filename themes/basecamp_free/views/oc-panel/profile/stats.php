<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="col-xs-12">
			<div class="page-header">
				<h3>
				<?=_e('Statistics')?>
				<?if ($advert->loaded()):?>
					: <?=$advert->title?>
				<?endif?>
				</h3>
			</div>

			<div class="clearfix">
				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-heading text-center">
							<b><i class="fa fa-bar-chart"></i> <?=_e('Contacts')?></b>
						</div>
						<div class="panel-body">
							<ul class="list-group">
								<li class="list-group-item"><?=_e('Today')?> <span class="badge"><?=$contacts_today?></span></li>
								<li class="list-group-item"><?=_e('Yesterday')?> <span class="badge"><?=$contacts_yesterday?></span></li>
								<li class="list-group-item"><?=_e('Last 30 days')?> <span class="badge"><?=$contacts_month?></span></li>
							</ul>
						</div>
						<div class="panel-footer text-right">
							<b><?=_e('Total')?> : <span class="badge badge-success"><?=$contacts_total?></span></b>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="panel panel-default">
						<div class="panel-heading text-center">
							<b><i class="fa fa-bar-chart"></i> <?=_e('Visits')?></b>
						</div>
						<div class="panel-body">
							<ul class="list-group">
								<li class="list-group-item"><?=_e('Today')?> <span class="badge"><?=$visits_today?></span></li>
								<li class="list-group-item"><?=_e('Yesterday')?> <span class="badge"><?=$visits_yesterday?></span></li>
								<li class="list-group-item"><?=_e('Last 30 days')?> <span class="badge"><?=$visits_month?></span></li>
							</ul>
						</div>
						<div class="panel-footer text-right">
							<b><?=_e('Total')?> : <span class="badge badge-success"><?=$visits_total?></span></b>
						</div>
					</div>
				</div>
			</div>
	
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"> <i class="fa fa-bar-chart"></i> <?=_e('Charts')?></h3>
					</div>
					<div class="panel-body">
						<form id="edit-profile" class="form-inline text-center" method="post" action="">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=_e('From')?></div>
										<input type="text" class="form-control" id="from_date" name="from_date" value="<?=$from_date?>" data-date="<?=$from_date?>" data-date-format="yyyy-mm-dd">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
								</div>
							</div>
							<span>-</span>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=_e('To')?></div>
										<input type="text" class="form-control" id="to_date" name="to_date" value="<?=$to_date?>" data-date="<?=$to_date?>" data-date-format="yyyy-mm-dd">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
								</div>
							</div>
							<button type="submit" class="btn btn-base-dark"><?=_e('Filter')?></button>
							<div>
								<br>
								<strong class="text-center"><?=_e('Views and Contacts statistic')?></strong>
								<?=Chart::line($stats_daily, array('height'  => 400,'width'   => 400,
								'options' => array('responsive' => true, 'maintainAspectRatio' => false, 'scaleShowVerticalLines' => false, 'multiTooltipTemplate' => '<%= datasetLabel %> - <%= value %>')))?>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>