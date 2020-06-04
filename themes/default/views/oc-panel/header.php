<?php defined('SYSPATH') or die('No direct script access.');?>

<header class="navbar-fixed-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <nav class="navbar" role="navigation">
                    <ul class="nav navbar-left">
                        <li class="dropdown-li">
                            <a href="<?=Route::url('default')?>" target="_blank" class="navbar-brand bigger"><i class="linecon li_shop"></i><span class="hidden-xs"><?= core::config('general.site_name') ?></span></a>
                        </li>
                        <li class="hidden">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="linecon li_settings"></i><span class="hidden-xs hidden-sm hidden-md"><?=__('Quick settings')?></span><i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <?=Theme::admin_link(__('Cache'),'tools','cache','oc-panel','glyphicon glyphicon-cog')?>
                                <?=Theme::admin_link(__('Delete all'),'tools','cache?force=1','oc-panel','glyphicon glyphicon-remove-sign')?>
                                <?=Theme::admin_link(__('Delete expired'),'tools','cache?force=2','oc-panel','glyphicon glyphicon-remove-circle')?>                                     
                            </ul>
                        </li>
                    </ul>
                    <?=View::factory('oc-panel/widget_login')?>
                </nav>
                <a href="https://docs.yclas.com/" target="_blank" class="help-link hidden-xs"><?=__('Need help?')?></a>
            </div>
        </div>
    </div>
</header>
