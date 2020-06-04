<?php defined('SYSPATH') or die('No direct script access.');?>

<?=Alert::show()?>

<ul class="list-inline pull-right">
    <li>
        <form class="form-inline" id="advert_search" method="GET" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="search" id="search" class="form-control" placeholder="<?=__('Search')?>">
            </div>
        </form>
    </li>
    <li>
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <?=Core::get('order',__('Sort'))?> <?=HTML::chars(Core::get('sort'))?>  <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                <?foreach($fields as $field):?>
                    <li><a class="ajax-load" href="<?=Route::url('oc-panel', array('controller'=> 'ad', 'action'=>'index')) ?>?order=<?=$field?>&sort=desc&status=<?=HTML::chars(Core::get('status'))?>"><?=$field?> - Desc</a></li>
                    <li><a class="ajax-load" href="<?=Route::url('oc-panel', array('controller'=> 'ad', 'action'=>'index')) ?>?order=<?=$field?>&sort=asc&status=<?=HTML::chars(Core::get('status'))?>"><?=$field?> - Asc</a></li>
                <?endforeach?>
            </ul>
        </div>
    </li>
</ul>

<h1 class="page-header page-title">
    <?=__('Advertisements')?>
        <a target="_blank" href="https://docs.yclas.com/how-to-manage-advertisements/">
            <i class="fa fa-question-circle"></i>
        </a>
</h1>

<hr>

<div class="panel panel-default">
    <ul class="nav nav-tabs nav-tabs-linetriangle">
        <?if(Core::get('status') == Model_Ad::STATUS_UNAVAILABLE):?>
            <?$current_url = Model_Ad::STATUS_UNAVAILABLE?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_UNCONFIRMED):?>
            <?$current_url = Model_Ad::STATUS_UNCONFIRMED?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_SPAM):?>
            <?$current_url = Model_Ad::STATUS_SPAM?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_SOLD):?>
            <?$current_url = Model_Ad::STATUS_SOLD?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_PUBLISHED AND Core::get('filter') == 'expired'):?>
            <?$current_url = 'expired'?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_PUBLISHED AND Core::get('filter') == 'active'):?>
            <?$current_url = 'active'?>
        <?elseif (Core::get('status') == Model_Ad::STATUS_PUBLISHED AND Core::get('filter') == 'featured'):?>
            <?$current_url = 'featured'?>
        <?else:?>
            <?$current_url = Model_Ad::STATUS_PUBLISHED?>
        <?endif?>
        <li class="<?=$current_url == Model_Ad::STATUS_PUBLISHED ? 'active' : NULL?>">
            <a class="ajax-load" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index'))?>"><?=__('All ads')?></a>
        </li>
        <li class="<?=$current_url == Model_Ad::STATUS_SPAM ? 'active' : NULL?>">
            <a class="ajax-load" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_SPAM?>"><?=__('Spam')?></a>
        </li>
        <li class="<?=$current_url == Model_Ad::STATUS_UNAVAILABLE ? 'active' : NULL?>">
            <a class="ajax-load" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_UNAVAILABLE?>"><?=__(' Unavailable')?></a>
        </li>
        <li class="<?=$current_url == Model_Ad::STATUS_UNCONFIRMED ? 'active' : NULL?>">
            <a class="ajax-load" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_UNCONFIRMED?>"><?=__(' Unconfirmed')?></a>
        </li>
        <li class="<?=$current_url == Model_Ad::STATUS_SOLD ? 'active' : NULL?>">
            <a class="ajax-load" href="<?=Route::url('oc-panel', array('directory'=>'panel', 'controller'=>'ad', 'action'=>'index')).'?status='.Model_Ad::STATUS_SOLD?>"><?=__('Sold')?></a>
        </li>
        <?if(core::config('advertisement.expire_date') > 0 OR (New Model_Field())->get('expiresat')):?>
            <li class="<?=$current_url == 'expired' ? 'active' : NULL?>">
                <a class="ajax-load" href="<?=Route::url('oc-panel', array('controller'=> 'ad', 'action'=>'index')) ?>?status=1&filter=expired" rel="tooltip" title="<?=__('Expired Ads')?>">
                    <?=__(' Expired Ads')?>
                </a>
            </li>
            <li class="<?=$current_url == 'active' ? 'active' : NULL?>">
                <a class="ajax-load" href="<?=Route::url('oc-panel', array('controller'=> 'ad', 'action'=>'index')) ?>?status=1&filter=active" rel="tooltip" title="<?=__('Not Expired Ads')?>">
                    <?=__(' Not Expired Ads')?>
                </a>
            </li>
        <?endif?>
        <?if(core::config('payment.to_featured') != FALSE):?>
            <li class="<?=$current_url == 'featured' ? 'active' : NULL?>">
                <a class="ajax-load" href="<?=Route::url('oc-panel', array('controller'=> 'ad', 'action'=>'index')) ?>?status=1&filter=featured" title="<?=__('Featured Ads')?>">
                    <?=__('Featured Ads')?>
                </a>
            </li>
        <?endif?>
    </ul>
    <div>
        <form method="GET" enctype="multipart/form-data">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="sorting_disabled">
                            <div class="checkbox check-success">
                                <input type="checkbox" id="select-all">
                                <label for="select-all"></label>
                            </div>
                        </th>
                        <th class="sorting_disabled">#</th>
                        <th class="sorting_disabled"><?=__('Name')?></th>
                        <th class="sorting_disabled hidden-sm hidden-xs"><?=__('Category')?></th>
                        <th class="sorting_disabled hidden-sm hidden-xs"><?=__('Location')?></th>
                        <?if(core::config('advertisement.count_visits')==1):?>
                           <th class="sorting_disabled hidden-xs"><?=__('Hits')?></th>
                        <?endif?>
                        <th class="sorting_disabled hidden-xs"><?=__('Status')?></th>
                        <th class="sorting_disabled hidden-sm hidden-xs"><?=__('Published')?></th>
                        <th class="sorting_disabled hidden-sm hidden-xs"><?=__('Created')?></th>
                        <!-- in case there are no ads we dont show buttons -->
                        <?if(isset($res)):?>
                            <th class="sorting_disabled">
                                <ul class="list-inline">
                                    <li>
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                                <i class="fa fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <?if(Core::get('status') != Model_Ad::STATUS_SPAM):?>
                                                    <li>
                                                        <button class="btn btn-block btn-link spam"
                                                            data-toggle="confirmation"
                                                            formaction="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam'))?>?current_url=<?=$current_url?>"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Spam?')?>">
                                                            <i class="fa fa-fire"></i> <?=__('Spam')?></span>
                                                        </button>
                                                    </li>
                                                <?endif?>
                                                <?if(Core::get('status') != Model_Ad::STATUS_UNAVAILABLE):?>
                                                    <li>
                                                        <button class="btn btn-block btn-link deactivate"
                                                            data-toggle="confirmation"
                                                            formaction="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate'))?>?current_url=<?=$current_url?>"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Deactivate?')?>">
                                                            <i class="fa fa-minus"></i> <?=__('Deactivate')?></span>
                                                        </button>
                                                    </li>
                                                <?endif?>
                                                <?if($current_url != Model_Ad::STATUS_PUBLISHED):?>
                                                    <li>
                                                        <button class="btn btn-block btn-link activate"
                                                            data-toggle="confirmation"
                                                            formaction="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'activate'))?>?current_url=<?=$current_url?>"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Activate?')?>">
                                                            <i class="fa fa-check"></i> <?=__('Activate')?></span>
                                                        </button>
                                                    </li>
                                                <?endif?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <button class="btn btn-block btn-link delete batch-delete"
                                                        formaction="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete'))?>?current_url=<?=$current_url?>"
                                                        data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                        data-btnCancelLabel="<?=__('No way!')?>"
                                                        title="<?=__('Delete?')?>" data-text="<?=__('Are you sure you want to delete?')?>">
                                                        <i class="fa fa-times"></i> <?=__('Delete')?></span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </th>
                        <?endif?>
                    </tr>
                </thead>
                <?if(isset($res)):?>
                    <tbody>
                        <?$i = 0; foreach($res as $ad):?>
                            <tr>
                                <td>
                                    <div class="checkbox check-success">
                                        <input name="id_ads[]" value="<?=$ad->id_ad?>" type="checkbox" id="ad_<?=$ad->id_ad?>">
                                        <label for="ad_<?=$ad->id_ad?>"></label>
                                    </div>
                                </td>

                                <td><?=$ad->id_ad?></td>

                                <td><a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?= wordwrap($ad->title, 15, "<br />\n"); ?></a>
                                </td>

                                <td class="hidden-sm hidden-xs"><?= wordwrap($ad->category->name, 15, "<br />\n"); ?></td>

                                <td class="hidden-sm hidden-xs">
                                    <?if($ad->location->loaded()):?>
                                        <?=wordwrap($ad->location->name, 15, "<br />\n");?>
                                    <?else:?>
                                        <?=__('n/a')?>
                                    <?endif?>
                                </td>

                                <?if(core::config('advertisement.count_visits')==1):?>
                                <td class="hidden-xs"><?=$ad->count_ad_hit();?></td>
                                <?endif?>

                                <td class="hidden-xs">
                                <?if($ad->status == Model_Ad::STATUS_NOPUBLISHED):?>
                                    <?=__('Not published')?>
                                <? elseif($ad->status == Model_Ad::STATUS_PUBLISHED):?>
                                    <?=__('Published')?>
                                <? elseif($ad->status == Model_Ad::STATUS_SPAM):?>
                                    <?=__('Spam')?>
                                <? elseif($ad->status == Model_Ad::STATUS_UNAVAILABLE):?>
                                    <?=__('Unavailable')?>
                                <?endif?>

                                <?if( ($order = $ad->get_order())!==FALSE ):?>
                                    <a class="label <?=($order->status==Model_Order::STATUS_PAID)?'label-success':'label-warning'?> "
                                        href="<?=Route::url('oc-panel', array('controller'=> 'order','action'=>'index'))?>?email=<?=$order->user->email?>">
                                    <?if ($order->status==Model_Order::STATUS_CREATED):?>
                                        <?=__('Not paid')?>
                                    <?elseif ($order->status==Model_Order::STATUS_PAID):?>
                                        <?=__('Paid')?>
                                    <?endif?>
                                        <?=i18n::format_currency($order->amount,$order->currency)?>
                                    </a>
                                <?endif?>
                                </td>

                                <td class="hidden-sm hidden-xs">
                                    <?if ($ad->status == Model_Ad::STATUS_PUBLISHED):?>
                                        <?=Date::format($ad->published, core::config('general.date_format'))?>
                                     <?endif ?>
                                </td>
                                <td class="hidden-sm hidden-xs">
                                    <?=Date::format($ad->created, core::config('general.date_format'))?>
                                </td>

                                <td class="nowrap">
                                    <div class="btn-group">
                                        <a class="btn btn-primary"
                                            href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'update','id'=>$ad->id_ad))?>"
                                            title="<?=__('Update')?>">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                        <div class="btn-group">
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                                <i class="fa fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <?if($ad->status != Model_Ad::STATUS_SPAM):?>
                                                    <li>
                                                        <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'spam','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>"
                                                            data-toggle="confirmation"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Spam?')?>">
                                                            <i class="fa fa-fire"></i> <?=__('Spam')?>
                                                        </a>
                                                    </li>
                                                <?endif?>
                                                <?if($ad->status != Model_Ad::STATUS_UNAVAILABLE):?>
                                                    <li>
                                                        <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'sold','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>"
                                                            data-toggle="confirmation"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Mark as Sold?')?>">
                                                            <i class="fa fa-money"></i> <?=__('Mark as Sold')?>
                                                        </a>
                                                    </li>
                                                <?endif?>
                                                <?if($ad->status != Model_Ad::STATUS_UNAVAILABLE):?>
                                                    <li>
                                                        <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'deactivate','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>"
                                                            data-toggle="confirmation"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Deactivate?')?>">
                                                            <i class="fa fa-minus"></i> <?=__('Deactivate')?>
                                                        </a>
                                                    </li>
                                                <?endif?>
                                                <?if( $ad->status != Model_Ad::STATUS_PUBLISHED ):?>
                                                    <li>
                                                        <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'activate','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>"
                                                            data-toggle="confirmation"
                                                            data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                            data-btnCancelLabel="<?=__('No way!')?>"
                                                            title="<?=__('Activate?')?>">
                                                            <i class="fa fa-check"></i> <?=__('Activate')?>
                                                        </a>
                                                    </li>
                                                <?endif?>
                                                <!-- sel_url_to_redirect is important because is quick selector. This works with dynamic check boxes, where we select href to build new url -->
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a class="sel_url_to_redirect"
                                                        href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'delete','id'=>$ad->id_ad))?>?current_url=<?=$current_url?>"
                                                        data-toggle="confirmation"
                                                        data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                        data-btnCancelLabel="<?=__('No way!')?>"
                                                        title="<?=__('Delete?')?>" data-id="tr1" data-text="<?=__('Are you sure you want to delete?')?>">
                                                        <i class="fa fa-times"></i> <?=__('Delete')?>
                                                    </a>
                                                </li>
                                                <?if($current_url == Model_Ad::STATUS_PUBLISHED):?>
                                                    <li role="separator" class="divider"></li>
                                                    <?if(core::config('payment.to_featured') != FALSE):?>
                                                        <?if($ad->featured==NULL OR Date::mysql2unix($ad->featured) < time()):?>
                                                            <li>
                                                                <a href="<?=Route::url('default', array('controller'=>'ad','action'=>'to_featured','id'=>$ad->id_ad))?>"
                                                                    data-toggle="confirmation"
                                                                    data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                                    data-btnCancelLabel="<?=__('No way!')?>"
                                                                    title="<?=__('Make featured?')?>" data-id="tr1" data-text="<?=__('Are you sure you want to make it featured?')?>">
                                                                    <i class="fa fa-bookmark-o"></i> <?=__('Featured')?>
                                                                </a>
                                                            </li>
                                                        <?elseif(Date::mysql2unix($ad->featured) > time()):?>
                                                            <li>
                                                                <a href="<?=Route::url('oc-panel', array('controller'=>'ad','action'=>'unfeature','id'=>$ad->id_ad))?>"
                                                                    data-toggle="confirmation"
                                                                    data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                                    data-btnCancelLabel="<?=__('No way!')?>"
                                                                    title="<?=__('Remove featured?')?>" data-id="tr1" data-text="<?=__('Are you sure you want to remove featured ad?')?>">
                                                                    <span class="fa-stack">
                                                                        <i class="fa fa-bookmark-o fa-stack-1x"></i>
                                                                        <i class="fa fa-ban fa-stack"></i>
                                                                    </span>
                                                                    <?=__('Remove Featured')?>
                                                                </a>
                                                            </li>
                                                        <?endif?>
                                                    <?endif?>
                                                    <?if(core::config('payment.pay_to_go_on_top') > 0 AND core::config('payment.to_top') != FALSE):?>
                                                        <li>
                                                            <a href="<?=Route::url('default', array('controller'=>'ad','action'=>'to_top','id'=>$ad->id_ad))?>"
                                                                data-toggle="confirmation"
                                                                data-btnOkLabel="<?=__('Yes, definitely!')?>"
                                                                data-btnCancelLabel="<?=__('No way!')?>"
                                                                title="<?=__('Refresh listing, go to top?')?>" data-id="tr1" data-text="<?=__('Are you sure you want to refresh listing and go to top?')?>">
                                                                <i class="fa fa-arrow-circle-up"></i> <?=__('Go to top')?>
                                                            </a>
                                                        </li>
                                                    <?endif?>
                                                    <li>
                                                        <a href="<?=Route::url('oc-panel', array('controller'=>'myads','action'=>'stats','id'=>$ad->id_ad))?>"
                                                            rel="tooltip" title="<?=__('Stats')?>">
                                                            <i class="fa fa-align-left"></i> <?=__('Stats')?>
                                                        </a>
                                                    </li>
                                                <?endif?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?endforeach?>
                    </tbody>
                <?endif?>
            </table>
        </form>
        <?if(isset($pagination)):?>
            <div class="panel-footer">
                <div class="text-center"><?=$pagination?></div>
            </div>
        <?endif?>
    </div>
</div>

<div class="modal modal-statc fade" id="processing-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-body">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?=_e('Processing...')?></h4>
                </div>
                <div class="modal-body">
                    <div class="progress progress-striped">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
