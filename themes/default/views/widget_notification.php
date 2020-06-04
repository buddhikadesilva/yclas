<?php defined('SYSPATH') or die('No direct script access.');?>

<?if(Auth::instance()->logged_in()):?>
    <?if (core::config('general.messaging') AND $messages = Model_Message::get_unread_threads(Auth::instance()->get_user())) :?>
        <div class="btn-group" role="group">
            <?if (($messages_count = $messages->count_all()) > 0) :?>
                <a class="btn btn-success widget_notification"
                    href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>"
                    data-toggle="dropdown"
                >
                    <i class="fa fa-bell"></i> <span class="badge"><?=$messages_count?></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-header"><?=sprintf(__('You have %s unread messages'), $messages_count)?></li>
                    <?foreach ($messages->find_all() as $message):?>
                        <li>
                            <a href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'message','id'=>($message->id_message_parent != NULL) ? $message->id_message_parent : $message->id_message))?>">
                                <small><strong><?=isset($message->ad->title) ? $message->ad->title : _e('Direct Message')?></strong></small>
                                <br>
                                <small><em><?=$message->from->name?></em></small>
                            </a>
                        </li>
                    <?endforeach?>
                </ul>
            <?else:?>
                <a class="btn btn-success widget_notification"
                    href="<?=Route::url('oc-panel',array('controller'=>'messages','action'=>'index'))?>"
                    title="<?=__('You have no unread messages')?>"
                    data-toggle="popover"
                    data-placement="bottom"
                >
                    <i class="fa fa-bell-o"></i>
                </a>
            <?endif?>
        </div>
    <?elseif ($ads = Auth::instance()->get_user()->contacts() AND core::count($ads) > 0) :?>
        <div class="btn-group" role="group">
            <a class="btn dropdown-toggle btn-success widget_notification" data-toggle="dropdown" href="#" id="contact-notification" data-url="<?=Route::url('oc-panel', array('controller'=>'profile', 'action'=>'notifications'))?>">
                <i class="fa fa-bell"></i> <span class="badge"><?=core::count($ads)?></span>
            </a>
            <ul id="contact-notification-dd" class="dropdown-menu">
                <li class="dropdown-header"><?=_e('Please check your email')?></li>
                <li class="divider"></li>
                <li class="dropdown-header"><?=_e('You have been contacted for these ads')?></li>
                <?foreach ($ads as $ad ):?>
                    <li class="dropdown-header"><strong><?=$ad["title"]?></strong></li>
                <?endforeach?>
            </ul>
        </div>
    <?endif?>
<?endif?>