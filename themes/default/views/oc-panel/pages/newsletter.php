<?php defined('SYSPATH') or die('No direct script access.');?>

<?=View::factory('oc-panel/elasticemail')?>

<a class="btn btn-info pull-right ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'settings','action'=>'email'))?>?force=1" title="<?=__('Email Settings')?>">
        <?=__('Email Settings')?>
    </a>
<h1 class="page-header page-title">
    <?=__('Newsletter')?>
    <a target="_blank" href="https://docs.yclas.com/how-to-send-the-newsletter/">
        <i class="fa fa-question-circle"></i>
    </a>
</h1>
<hr>

<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?=Route::url('oc-panel',array('controller'=>'newsletter','action'=>'index'))?>"> 
            <div class="panel panel-default">
                <div class="panel-body">
                    <?=Form::errors()?> 
                        <div class="form-group">
                            <label class="control-label"><?=__('To')?>:</label>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_all" id="send_all">
                                <label for="send_all"><?=__('All active users.')?> <span class="badge badge-info"><?=$count_all_users?></span></label>
                            </div>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_featured" id="send_featured">
                                <label for="send_featured"><?=__('Users with featured ads.')?> <span class="badge badge-info"><?=$count_featured?></span></label>
                            </div>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_featured_expired" id="send_featured_expired">
                                <label for="send_featured_expired"><?=__('Users with featured ads expired.')?> <span class="badge badge-info"><?=$count_featured_expired?></span></label>
                            </div>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_unpub" id="send_unpub">
                                <label for="send_unpub"><?=__('Users without published ads.')?> <span class="badge badge-info"><?=$count_unpub?></span></label>
                            </div>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_logged" id="send_logged">
                                <label for="send_logged"><?=__('Users not logged last 3 months')?> <span class="badge badge-info"><?=$count_logged?></span></label>
                            </div>
                            <div class="checkbox check-success">
                                <input type="checkbox" name="send_spam" id="send_spam">
                                <label for="send_spam"><?=__('Users marked a spam')?> <span class="badge badge-info"><?=$count_spam?></span></label>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label class="control-label"><?=__('From')?>:</label>
                            <input type="text" name="from" value="<?=Auth::instance()->get_user()->name?>" class="form-control"  />
                        </div>
                
                        <div class="form-group">
                            <label class="control-label"><?=__('From Email')?>:</label>
                            <input type="text" name="from_email" value="<?=Auth::instance()->get_user()->email?>" class="form-control"  />
                        </div>
                
                        <div class="form-group">
                            <label class="control-label"><?=__('Subject')?>:</label>
                            <input  type="text" name="subject" value="" class="form-control"  />
                        </div>
                
                        <div class="form-group">
                            <label class="control-label"><?=__('Message')?>:</label>
                            <textarea  name="description"  id="formorm_description" class="form-control" data-editor="html" rows="15" ></textarea>
                        </div>
                    <hr>
                    <a href="<?=Route::url('oc-panel')?>" class="btn btn-default"><?=__('Cancel')?></a>
                    <button type="submit" class="btn btn-primary"><?=__('Send')?></button>
                </div>
            </div>
        </form>
    </div>
</div>