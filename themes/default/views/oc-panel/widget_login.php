<?php defined('SYSPATH') or die('No direct script access.');?>

<ul class="nav navbar-top-links navbar-right">
    <li class="hidden-xs hidden-sm hidden-md">
        <a href="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'favorites'))?>">
            <i class="linecon li_heart"></i> 
        </a>
    </li>
    <li class="hidden-xs hidden-sm hidden-md">
    <?if (core::config('general.messaging') AND $messages = Model_Message::get_unread_threads(Auth::instance()->get_user())) :?>
            <?if (($messages_count = $messages->count_all()) > 0) :?>
                <a class="dropdown-toggle"
                    href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>"
                    data-toggle="dropdown"
                >
                    <i class="linecon li_mail"></i><?=$messages_count?>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-header"><?=sprintf(__('You have %s unread messages'), $messages_count)?></li>
                    <?foreach ($messages->find_all() as $message):?>
                        <li>
                            <a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'message','id'=>($message->id_message_parent != NULL) ? $message->id_message_parent : $message->id_message))?>">
                                <small><strong><?=isset($message->ad->title) ? $message->ad->title : __('Direct Message')?></strong></small>
                                <br>
                                <small><em><?=$message->from->name?></em></small>
                            </a>
                        </li>
                    <?endforeach?>
                </ul>
            <?else:?>
                <a
                    href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>"
                    title="<?=__('You have no unread messages')?>"
                    data-toggle="popover"
                    data-placement="bottom"
                >
                    <i class="linecon li_mail"></i> <?=$messages_count?>
                </a>
            <?endif?>
    <?elseif ($ads = Auth::instance()->get_user()->contacts() AND core::count($ads) > 0) :?>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="contact-notification" data-url="<?=Route::url('oc-panel', array('controller'=>'profile', 'action'=>'notifications'))?>">
                <i class="linecon li_mail"></i> <?=core::count($ads)?>
            </a>
            <ul id="contact-notification-dd" class="dropdown-menu">
                <li class="dropdown-header"><?=__('Please check your email')?></li>
                <li class="divider"></li>
                <li class="dropdown-header"><?=__('You have been contacted for these ads')?></li>
                <?foreach ($ads as $ad ):?>
                    <li class="dropdown-header"><strong><?=$ad["title"]?></strong></li>
                <?endforeach?>
            </ul>
    <?endif?>
    </li>
    <li class="dropdown">
        <a class="dropdown-toggle profile-dropdown" data-toggle="dropdown" href="#">
            <span><?=Auth::instance()->get_user()->name?></span>
            <img src="<?=Auth::instance()->get_user()->get_profile_image()?>" alt="" height="32" width="32" class="img-circle profile-img">
        </a>
        <ul class="dropdown-menu pull-right dropdown-profile">
            <li>
                <a href="<?=Route::url('profile',array('seoname'=>Auth::instance()->get_user()->seoname))?>">
                    <i class="fa fa-fw fa-user"></i> <?=__('Public profile')?>
                </a>
            </li>
            <li>
                <a href="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'edit'))?>">
                    <i class="fa fa-fw fa-edit"></i> <?=__('Edit profile')?>
                </a>
            </li>
            <li class="hidden-lg">
                <a href="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'favorites'))?>">
                    <i class="fa fa-fw fa-heart"></i> <?=__('My Favorites')?>
                </a>
            </li>
            <?if (core::config('general.messaging') == TRUE):?>
            <li class="hidden-lg">
                <a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>">
                    <i class="fa fa-fw fa-envelope-o"></i> <?=__('My Messages')?>
                </a>
            </li>
            <?endif?>
            <li>
                <a href="<?=Route::url('oc-panel',array('controller'=>'myads','action'=>'index'))?>">
                    <i class="fa fa-fw fa-list-alt"></i> <?=__('My Advertisements')?>
                </a>
            </li>  
            <li>
                <a href="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'orders'))?>">
                    <i class="fa fa-fw fa-shopping-cart"></i> <?=__('My Payments')?>
                </a>
            </li>
            <li>
                <a href="<?=Route::url('oc-panel',array('controller'=>'profile','action'=>'subscriptions'))?>">
                    <i class="fa fa-fw fa-calendar-check-o"></i> <?=__('My Subscriptions')?>
                </a>
            </li>
            <li class="divider hidden-lg"></li>                                        
            <li class="hidden-lg">
                <a href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'logout'))?>">
                    <i class="fa fa-fw fa-sign-out"></i> <?=__('Logout')?>
                </a>
            </li>
        </ul>
    </li>
    </li>
    <li class="hidden-xs hidden-sm hidden-md">
        <a href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'logout'))?>">
            <i class="fa fa-fw fa-sign-out"></i> <?=__('Logout')?>
        </a>
    </li>
</ul>
