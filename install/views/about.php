<?defined('SYSPATH') or exit('Install must be loaded from within index.php!');?>

<div class="page-header">
    <h1><?=__('Welcome')?> </h1>
    <p><?=__('Thanks for using Yclas.')?> 
        <?=__('Your installation version is')?> <span class="label label-info"><?=install::VERSION?></span> 
    </p>
    
    <div class="clearfix"></div>
    <p><?=__('You need help or you have some questions')?>
        <a class="btn btn-info btn-xs" target="_blank" href="https://docs.yclas.com/"><i class="glyphicon glyphicon-question-sign"></i> <?=__('FAQ')?></a>
        <a class="btn btn-info btn-xs" target="_blank" href="https://yclas.com/blog/"><i class="glyphicon glyphicon-pencil"></i> <?=__('Blog')?></a>
    </p>
</div>

<div class="col-md-4 col-sm-12 col-xs-12">
    <div class="panel panel-info">
    <div class="panel-heading"><h3>Yclas <?=__('Latest News')?></h3>
    </div>
        <div class="panel-body">
            <ul>
                <?foreach (core::rss('http://feeds.feedburner.com/yclas')  as $item):?>
                    <li><a target="_blank" href="<?=$item->link?>" title="<?=$item->title?>"><?=$item->title?></a></li>
                    <div class="divider"></div>
                <?endforeach?>
            </ul>
        </div>
    </div>
</div>