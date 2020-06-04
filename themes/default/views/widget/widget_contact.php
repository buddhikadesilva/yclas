<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->text_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->text_title?></h3>
    </div>
<?endif?>

<div class="panel-body">
    <?=FORM::open(Route::url('default', array('controller'=>'contact', 'action'=>'user_contact', 'id'=>$widget->id_ad)), array('class'=>'form-horizontal', 'enctype'=>'multipart/form-data'))?>
        <fieldset>
            <?if(core::config('general.messaging') == TRUE AND !Auth::instance()->logged_in()):?>
                <div class="alert alert-warning">
                    <?=_e('Please, login before contact the advertiser!')?>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <?=FORM::label('message', _e('Message'), array('class'=>'control-label', 'for'=>'message'))?>
                        <?=FORM::textarea('message', "", array('class'=>'form-control', 'placeholder' => __('Message'), 'name'=>'message', 'id'=>'message', 'rows'=>2, 'required', 'disabled'))?>
                    </div>
                </div>
                <? if (core::config('advertisement.price') AND
                    core::config('advertisement.contact_price')) : ?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('price', _e('Price'), array('class'=>'control-label', 'for'=>'price'))?>
                            <?=FORM::input('price', Core::post('price'), array('placeholder' => html_entity_decode(i18n::money_format(1, $widget->currency )), 'class' => 'form-control', 'id' => 'price', 'type'=>'text', 'disabled'))?>
                        </div>
                    </div>
                <? endif ?>
                <div class="form-group">
                    <div class="col-xs-12">
                        <?=FORM::button('submit', _e('Send Message'), array('type'=>'submit', 'class'=>'btn btn-block btn-success disabled', 'action'=>Route::url('default', array('controller'=>'contact', 'action'=>'user_contact' , 'id'=>$widget->id_ad))))?>
                    </div>
                </div>
            <?else:?>
                <?if (!Auth::instance()->logged_in()):?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('name', _e('Name'), array('class'=>'control-label', 'for'=>'name'))?>
                            <?=FORM::input('name', '', array('placeholder' => __('Name'), 'class' => 'form-control', 'id' => 'name', 'required'))?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('email', _e('Email'), array('class'=>'control-label', 'for'=>'email'))?>
                            <?=FORM::input('email', '', array('placeholder' => __('Email'), 'class' => 'form-control', 'id' => 'email', 'type'=>'email','required'))?>
                        </div>
                    </div>
                <?endif?>

                <?if(core::config('general.messaging') != TRUE):?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('subject', _e('Subject'), array('class'=>'control-label', 'for'=>'subject'))?>
                            <?=FORM::input('subject', "", array('placeholder' => __('Subject'), 'class' => 'form-control', 'id' => 'subject'))?>
                        </div>
                    </div>
                <?endif?>
                <div class="form-group">
                    <div class="col-xs-12">
                        <?=FORM::label('message', _e('Message'), array('class'=>'control-label', 'for'=>'message'))?>
                        <?=FORM::textarea('message', "", array('class'=>'form-control', 'placeholder' => __('Message'), 'name'=>'message', 'id'=>'message', 'rows'=>2, 'required'))?>
                    </div>
                </div>
                <?if(core::config('general.messaging') AND
                    core::config('advertisement.price') AND
                    core::config('advertisement.contact_price')):?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('price', _e('Price'), array('class'=>'control-label', 'for'=>'price'))?>
                            <?=FORM::input('price', Core::post('price'), array('placeholder' => html_entity_decode(i18n::money_format(1, $widget->currency )), 'class' => 'form-control', 'id' => 'price', 'type'=>'text'))?>
                        </div>
                    </div>
                <?endif?>
                <!-- file to be sent-->
                <?if(core::config('advertisement.upload_file') AND core::config('general.messaging') != TRUE):?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?=FORM::label('file', _e('File'), array('class'=>'control-label', 'for'=>'file'))?>
                            <?=FORM::file('file', array('placeholder' => __('File'), 'class' => 'input-xlarge', 'id' => 'file'))?>
                        </div>
                    </div>
                <?endif?>

                <?if (core::config('advertisement.captcha') != FALSE):?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <?if (Core::config('general.recaptcha_active')):?>
                                <?=View::factory('recaptcha', ['id' => 'recaptcha2'])?>
                            <?else:?>
                                <?=_e('Captcha')?>*:<br />
                                <?=captcha::image_tag('contact')?><br />
                                <?=FORM::input('captcha', "", array('class' => 'form-control', 'id' => 'captcha', 'required'))?>
                            <?endif?>
                        </div>
                    </div>
                <?endif?>

                <div class="form-group">
                    <div class="col-xs-12">
                        <?=FORM::button(NULL, _e('Send Message'), array('type'=>'submit', 'class'=>'btn btn-block btn-success', 'action'=>Route::url('default', array('controller'=>'contact', 'action'=>'user_contact' , 'id'=>$widget->id_ad))))?>
                    </div>
                </div>
            <?endif?>
        </fieldset>
    <?=FORM::close()?>
</div>
