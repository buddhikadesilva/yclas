<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="col-md-3"> 
	<?foreach ( Widgets::render('sidebar') as $widget):?>
		<div class="panel panel-sidebar <?=get_class($widget->widget)?>">
			<?=$widget?>
		</div>
	<?endforeach?>
</div>