<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pad_10tb">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header">
					<h3><?=$faq->title?></h3>
				</div>
				<div class="faq-full">
					<?=Text::bb2html($faq->description,TRUE,FALSE)?>
				</div>
			
				<?=$disqus?>
			</div>
		</div>
	</div>
</div>	