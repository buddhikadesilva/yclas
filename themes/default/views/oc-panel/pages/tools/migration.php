<?php defined('SYSPATH') or die('No direct script access.');?>

<ul class="nav nav-tabs nav-tabs-simple">
    <li <?=(Request::current()->action()=='optimize') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'optimize'))?>"
            title="<?=HTML::chars(__('Optimize'))?>"
            class="ajax-load">
            <?=__('Optimize')?>
        </a>
    </li>
    <li <?=(Request::current()->action()=='sitemap') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'sitemap'))?>"
            title="<?=HTML::chars(__('Sitemap'))?>"
            class="ajax-load">
            <?=__('Sitemap')?>
        </a>
    </li>
    <li <?=(Request::current()->action()=='migration') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'migration'))?>"
            title="<?=HTML::chars(__('Migration'))?>"
            class="ajax-load">
            <?=__('Migration')?>
        </a>
    </li>
    <li <?=(Request::current()->action()=='cache') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'cache'))?>"
            title="<?=HTML::chars(__('Cache'))?>"
            class="ajax-load">
            <?=__('Cache')?>
        </a>
    </li>
    <li <?=(Request::current()->action()=='logs') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'logs'))?>"
            title="<?=HTML::chars(__('Logs'))?>"
            class="ajax-load">
            <?=__('Logs')?>
        </a>
    </li>
    <li <?=(Request::current()->action()=='phpinfo') ? 'class="active"' : NULL?>>
        <a href="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'phpinfo'))?>"
            title="<?=HTML::chars(__('PHP Info'))?>"
            class="ajax-load">
            <?=__('PHP Info')?>
        </a>
    </li>
    <?if(Core::config('general.algolia_search') == 1):?>
        <li <?=(Request::current()->action()=='index') ? 'class="active"' : NULL?>>
            <a href="<?=Route::url('oc-panel',array('controller'=>'algolia','action'=>'index'))?>"
                title="Algolia"
                class="ajax-load">
                Algolia Search
            </a>
        </li>
    <?endif?>
</ul>

<div class="panel panel-default">
    <div class="panel-body">
        <h1 class="page-header page-title">
            <?=__('Migration')?> from OC 1.7.x/1.8.x to 2.x
            <a target="_blank" href="https://docs.yclas.com/how-to-upgrade-1-7-x1-8-x-to-2-x/">
                <i class="fa fa-question-circle"></i>
            </a>
        </h1>
        <hr>
        <p><?=__("Your PHP time limit is")?> <?=ini_get('max_execution_time')?> <?=__("seconds")?>
                <form method="post" action="<?=Route::url('oc-panel',array('controller'=>'tools','action'=>'migration'))?>">
                    <?=Form::errors()?>
                    <div class="form-group">
                        <label class="control-label"><?=__("Host name")?>:</label>
                        <input type="text" name="hostname" value="<?=$db_config['connection']['hostname']?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?=__("User name")?>:</label>
                        <input type="text" name="username"  value="<?=$db_config['connection']['username']?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?=__("Password")?>:</label>
                        <input type="text" name="password" value="" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?=__("Database name")?>:</label>
                        <input type="text" name="database" value="<?=$db_config['connection']['database']?>"  class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?=__("Database charset")?>:</label>
                                <input type="text" name="charset" value="<?=$db_config['charset']?>"  class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?=__("Table prefix")?>:</label>
                                <input type="text" name="table_prefix" value="oc_" class="form-control" />
                    </div>
                    <hr>
                    <a href="<?=Route::url('oc-panel')?>" class="btn btn-default"><?=__('Cancel')?></a>
                     <button type="submit" class="btn btn-primary"><?=__('Migrate')?></button>
                </form>
    </div>
</div>
