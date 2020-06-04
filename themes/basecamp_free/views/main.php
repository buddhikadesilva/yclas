<?php defined('SYSPATH') or die('No direct script access.');?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="<?=i18n::html_lang()?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?=i18n::html_lang()?>"> <!--<![endif]-->
<head>
<?=View::factory('header_metas',array('title'             => $title,
                                      'meta_keywords'     => $meta_keywords,
                                      'meta_description'  => $meta_description,
                                      'meta_copyright'    => $meta_copyright,
                                      'amphtml'           => $amphtml,))?>
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script type="text/javascript" src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->
    <?=Theme::styles($styles)?>
    <?=Theme::scripts($scripts)?>
    <?=core::config('general.html_head')?>
    <?=View::factory('analytics')?>
    <style type="text/css">
        <?if (Theme::get('upper_banner_image')) :?>
            .index-head {background-image:url('<?=Theme::get('upper_banner_image')?>');}
        <?endif?>
    </style>
</head>
<body data-spy="scroll" data-target=".subnav" data-offset="100">
<?=View::factory('alert_terms')?>
<?=$header?>
<div id="content" class="container-fluid">
	<div class="row">
		<div class="container">
			<?=Alert::show()?>
		</div>
			<?=$content?>
	</div>
</div>
<?=$footer?>
<?=Theme::scripts($scripts,'footer')?>
<?=Theme::scripts($scripts,'async_defer', 'default', ['async' => '', 'defer' => ''])?>
<?=core::config('general.html_footer')?>
<?=(Kohana::$environment === Kohana::DEVELOPMENT)? View::factory('profiler'):''?>
</body>
</html>
