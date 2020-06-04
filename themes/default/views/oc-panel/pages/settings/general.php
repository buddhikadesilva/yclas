<?php defined('SYSPATH') or die('No direct script access.');?>

<?=Form::errors()?>

<ul class="list-inline pull-right">
    <li>
        <a class="btn btn-primary ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'config'))?>" title="<?=__('All configurations')?>">
            <?=__('All configurations')?>
        </a>
    </li>
</ul>

<h1 id="page-general-configuration" class="page-header page-title"><?=__('General Configuration')?></h1>

<hr>

<div class="row">
    <div class="col-md-12 col-lg-12">
        <?=FORM::open(Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general')), array('class'=>'config ajax-load', 'enctype'=>'multipart/form-data'))?>
            <div>
                <div>
                    <ul class="nav nav-tabs nav-tabs-simple nav-tabs-left" id="tab-settings">
                        <li class="active">
                            <a data-toggle="tab" href="#tabSettingsGeneral" aria-expanded="true"><?=__('General')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsRegional" aria-expanded="false"><?=__('Regional')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsAdditionalFeatures" aria-expanded="false"><?=__('Additional Features')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsComments" aria-expanded="false"><?=__('Comments')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsRecaptcha" aria-expanded="false"><?=__('reCAPTCHA')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsNotifications" aria-expanded="false"><?=__('Notifications')?></a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsAlgolia" aria-expanded="false">Algolia Search</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tabSettingsCarQuery" aria-expanded="false">CarQuery</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tabSettingsGeneral" class="tab-pane active fade">
                            <h4><?=__('General Settings')?></h4>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['maintenance']['id'], __("Maintenance Mode"), array('class'=>'control-label', 'for'=>$forms['maintenance']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-activate-maintenance-mode/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['maintenance']['key'], 1, (bool) $forms['maintenance']['value'], array('id' => $forms['maintenance']['id'].'1'))?>
                                    <?=Form::label($forms['maintenance']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['maintenance']['key'], 0, ! (bool) $forms['maintenance']['value'], array('id' => $forms['maintenance']['id'].'0'))?>
                                    <?=Form::label($forms['maintenance']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Enables the site to maintenance")?>
                                </span>
                            </div>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['site_name']['id'], __('Site name'), array('class'=>'control-label', 'for'=>$forms['site_name']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/change-site-name-site-description/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::input($forms['site_name']['key'], $forms['site_name']['value'], array(
                                    'placeholder' => 'Yclas',
                                    'class' => 'form-control',
                                    'id' => $forms['site_name']['id'],
                                    'data-rule-required'=>'true',
                                ))?>
                                <span class="help-block">
                                    <?=__("Here you can declare your display name. This is seen by everyone!")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['site_description']['id'], __('Site description'), array('class'=>'control-label', 'for'=>$forms['site_description']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/change-site-name-site-description/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::textarea($forms['site_description']['key'], $forms['site_description']['value'], array(
                                    'placeholder' => __('Description of your site in no more than 160 characters.'),
                                    'rows' => 3, 'cols' => 50,
                                    'class' => 'form-control',
                                    'id' => $forms['site_description']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__('Description used for the Meta Description Tag of the home page. Might be used by Google as search result snippet. (max. 160 chars)')?>
                                </span>
                            </div>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['disallowbots']['id'], __("Disallows (blocks) Bots and Crawlers on this website"), array('class'=>'control-label', 'for'=>$forms['disallowbots']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/allowdisallow-bots-crawlers/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['disallowbots']['key'], 1, (bool) $forms['disallowbots']['value'], array('id' => $forms['disallowbots']['id'].'1'))?>
                                    <?=Form::label($forms['disallowbots']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['disallowbots']['key'], 0, ! (bool) $forms['disallowbots']['value'], array('id' => $forms['disallowbots']['id'].'0'))?>
                                    <?=Form::label($forms['disallowbots']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Disallows Bots and Crawlers on the website")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['private_site']['id'], __("Private Site"), array('class'=>'control-label', 'for'=>$forms['private_site']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/private-site/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['private_site']['key'], 1, (bool) $forms['private_site']['value'], array('id' => $forms['private_site']['id'].'1'))?>
                                    <?=Form::label($forms['private_site']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['private_site']['key'], 0, ! (bool) $forms['private_site']['value'], array('id' => $forms['private_site']['id'].'0'))?>
                                    <?=Form::label($forms['private_site']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Enables the site to private_site")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['google_authenticator']['id'], __("2 Step Authentication"), array('class'=>'control-label', 'for'=>$forms['google_authenticator']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/2-step-authentication/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['google_authenticator']['key'], 1, (bool) $forms['google_authenticator']['value'], array('id' => $forms['google_authenticator']['id'].'1'))?>
                                    <?=Form::label($forms['google_authenticator']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['google_authenticator']['key'], 0, ! (bool) $forms['google_authenticator']['value'], array('id' => $forms['google_authenticator']['id'].'0'))?>
                                    <?=Form::label($forms['google_authenticator']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("2 step Google Authenticator")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['sms_auth']['id'], __("2 Step SMS Authentication"), array('class'=>'control-label', 'for'=>$forms['sms_auth']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/2-step-sms-authentication/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['sms_auth']['key'], 1, (bool) $forms['sms_auth']['value'], array('id' => $forms['sms_auth']['id'].'1'))?>
                                    <?=Form::label($forms['sms_auth']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['sms_auth']['key'], 0, ! (bool) $forms['sms_auth']['value'], array('id' => $forms['sms_auth']['id'].'0'))?>
                                    <?=Form::label($forms['sms_auth']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("2 step SMS Authentication")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['sms_clickatell_api']['id'], __('Clickatell'), array('class'=>'control-label', 'for'=>$forms['sms_clickatell_api']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/2-step-sms-authentication/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::input($forms['sms_clickatell_api']['key'], $forms['sms_clickatell_api']['value'], array(
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'id' => $forms['sms_clickatell_api']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Clickatell SMS API Key, needed for SMS Authentication to work.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['sms_clickatell_two_way_phone']['id'], __('Clickatell Phone Number'), array('class'=>'control-label', 'for'=>$forms['sms_clickatell_two_way_phone']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/2-step-sms-authentication/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::input($forms['sms_clickatell_two_way_phone']['key'], $forms['sms_clickatell_two_way_phone']['value'], array(
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'id' => $forms['sms_clickatell_two_way_phone']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Clickatell phone number, required only for two-way messaging type integration.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['cookie_consent']['id'], __("Cookie consent"), array('class'=>'control-label', 'for'=>$forms['cookie_consent']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/cookie-consent/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['cookie_consent']['key'], 1, (bool) $forms['cookie_consent']['value'], array('id' => $forms['cookie_consent']['id'].'1'))?>
                                    <?=Form::label($forms['cookie_consent']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['cookie_consent']['key'], 0, ! (bool) $forms['cookie_consent']['value'], array('id' => $forms['cookie_consent']['id'].'0'))?>
                                    <?=Form::label($forms['cookie_consent']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Enables an alert to accept cookies")?>
                                </span>
                            </div>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['moderation']['id'], __('Moderation'), array('class'=>'control-label', 'for'=>$forms['moderation']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-ads-moderation-works/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::select($forms['moderation']['key'], array(0=>__("Post directly"),1=>__("Moderation on"),2=>__("Payment on"),3=>__("Email confirmation on"),4=>__("Email confirmation with Moderation"),5=>__("Payment with Moderation")), $forms['moderation']['value'], array(
                                    'placeholder' => $forms['moderation']['value'],
                                    'class' => 'form-control',
                                    'id' => $forms['moderation']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Moderation is how you control newly created advertisements. You can set it up to fulfill your needs. For example, Post directly will enable new ads to be posted directly, and get published as soon they submit.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['landing_page']['id'], __('Landing page'), array('class'=>'control-label', 'for'=>$forms['landing_page']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/home-or-listing/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::select($forms['landing_page']['key'],
                                        array('{"controller":"home","action":"index"}'=>'HOME','{"controller":"ad","action":"listing"}'=>'LISTING','{"controller":"user","action":"index"}'=>'USERS'), $forms['landing_page']['value'], array(
                                    'class' => 'form-control',
                                    'id' => $forms['landing_page']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("It changes landing page of website")?>
                                </span>
                            </div>

                            <?$pages = array(''=>__('Deactivated'))?>
                            <?foreach (Model_Content::get_pages() as $key => $value) {
                                $pages[$value->seotitle] = $value->title;
                            }?>
                            <div class="form-group">
                                <?= FORM::label($forms['alert_terms']['id'], __('Accept Terms Alert'), array('class'=>'control-label', 'for'=>$forms['alert_terms']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/activate-access-terms-alert/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::select($forms['alert_terms']['key'], $pages, $forms['alert_terms']['value'], array(
                                    'class' => 'form-control',
                                    'id' => $forms['alert_terms']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("If you choose to use alert terms, you can select the page you want to render. And to edit content, select Content->Page on your admin panel sidebar. Find the page and click Edit. In section Description add content that suits you.")?>
                                    <a href="ttps://www.shareasale.com/r.cfm?b=854385&u=1782794&m=65338">If you need to generate your terms of service or privacy policy click here.</a>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['private_site_page']['id'], __('Private Site landing page content'), array('class'=>'control-label', 'for'=>$forms['private_site_page']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/private-site/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::select($forms['private_site_page']['key'], $pages, $forms['private_site_page']['value'], array(
                                    'class' => 'tips form-control',
                                    'id' => $forms['private_site_page']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Adds content to private site landing page")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['contact_page']['id'], __('Contact page content'), array('class'=>'control-label', 'for'=>$forms['contact_page']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-add-text-contact-page/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::select($forms['contact_page']['key'], $pages, $forms['contact_page']['value'], array(
                                    'class' => 'form-control',
                                    'id' => $forms['contact_page']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Adds content to contact page")?>
                                </span>
                            </div>

                            <hr>

                            <div class="form-group">
                                <?=FORM::label($forms['email_domains']['key'], __('Allowed email domains'), array('class'=>'control-label', 'for'=>$forms['email_domains']['key']))?>
                                <a target="_blank" href="https://docs.yclas.com/allowed-email-domains/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['email_domains']['key'], $forms['email_domains']['value'], array(
                                    'placeholder' => __('For email domain push enter.'),
                                    'class' => 'form-control',
                                    'id' => $forms['email_domains']['key'],
                                    'data-role'=>'tagsinput',
                                ))?>
                                <span class="help-block">
                                    <?=__("You need to write your email domains to enable the service.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['disallowed_email_domains']['key'], __('Disallowed email domains'), array('class' => 'control-label', 'for' => $forms['disallowed_email_domains']['key'])) ?>
                                <a target="_blank" href="https://docs.yclas.com/allowed-email-domains/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::input($forms['disallowed_email_domains']['key'], $forms['disallowed_email_domains']['value'], array(
                                    'placeholder' => __('For email domain push enter.'),
                                    'class' => 'form-control',
                                    'id' => $forms['disallowed_email_domains']['key'],
                                    'data-role' => 'tagsinput',
                                )) ?>
                                <span class="help-block">
                                    <?= __("You need to write your email domains to enable the service.") ?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['api_key']['id'], __('API Key'), array('class'=>'control-label', 'for'=>$forms['api_key']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/api-documentation/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::input($forms['api_key']['key'], $forms['api_key']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['api_key']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Integrate anything using your site API Key.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['analytics']['id'], __('Analytics Tracking ID'), array('class'=>'control-label', 'for'=>$forms['analytics']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-add-tracking-codes/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['analytics']['key'], $forms['analytics']['value'], array(
                                    'placeholder' => 'UA-XXXXX-YY',
                                    'class' => 'form-control',
                                    'id' => $forms['analytics']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Once logged in your Google Analytics, you can find the Tracking ID in the Accounts List or in the Property Settings")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['akismet_key']['id'], __('Akismet Key'), array('class'=>'control-label', 'for'=>$forms['akismet_key']['id']))?>
                                <a target="_blank" href="https://akismet.com/">
                                    <i class="fa fa-external-link-square"></i>
                                </a>
                                <?=FORM::input($forms['akismet_key']['key'], $forms['akismet_key']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['akismet_key']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Providing akismet key will activate this feature. This feature deals with spam posts and emails.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['gcm_apikey']['id'], __('GCM API Key'), array('class'=>'control-label', 'for'=>$forms['gcm_apikey']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/native-apps/#push-notifications">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['gcm_apikey']['key'], $forms['gcm_apikey']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['gcm_apikey']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Push notifications for your native app. Using Google Cloud Messaging, insert your API Key here.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['html_head']['id'], __('HTML in HEAD element'), array('class'=>'control-label', 'for'=>$forms['html_head']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/html-in-head-element/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::textarea($forms['html_head']['key'], $forms['html_head']['value'], array(
                                    'placeholder' => '',
                                    'rows' => 3, 'cols' => 50,
                                    'class' => 'form-control',
                                    'id' => $forms['html_head']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("To include your custom HTML code (validation metadata, reference to JS/CSS files, etc.) in the HEAD element of the rendered page.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['html_footer']['id'], __('HTML in footer'), array('class'=>'control-label', 'for'=>$forms['html_footer']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/html-in-footer/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::textarea($forms['html_footer']['key'], $forms['html_footer']['value'], array(
                                    'placeholder' => '',
                                    'rows' => 3, 'cols' => 50,
                                    'class' => 'form-control',
                                    'id' => $forms['html_footer']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("To include your custom HTML code (reference to JS or CSS files, etc.) in the footer of the rendered page.")?>
                                </span>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsRegional" class="tab-pane fade">
                            <h4><?=__('Regional Settings')?></h4>

                            <hr>

                            <div class="form-group">
                                <?=FORM::label($forms['country']['id'], __('Country'), array('class'=>'control-label','for'=>$forms['country']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/2-step-sms-authentication/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=Form::select($forms['country']['key'], array_merge(['' => __('None')], EUVAT::countries()), $forms['country']['value'])?>
                                <span class="help-block">
                                    <?=__("Set an inital country for the phone field.")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['multilingual']['id'], __("Multilingual"), array('class'=>'control-label', 'for'=>$forms['multilingual']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-activate-multilingual-mode/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['multilingual']['key'], 1, (bool) $forms['multilingual']['value'], array('id' => $forms['multilingual']['id'].'1'))?>
                                    <?=Form::label($forms['multilingual']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['multilingual']['key'], 0, ! (bool) $forms['multilingual']['value'], array('id' => $forms['multilingual']['id'].'0'))?>
                                    <?=Form::label($forms['multilingual']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Enables the site to multilingual")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?=Form::label($forms['languages']['id'], __('Languages'), array('class'=>'control-label','for'=>$forms['languages']['id']))?>
                                <?= FORM::input($forms['languages']['key'], $forms['languages']['value'], array(
                                    'placeholder' => __('For each language push enter.'),
                                    'class' => 'form-control',
                                    'id' => $forms['languages']['key'],
                                    'data-role' => 'tagsinput',
                                )) ?>
                            </div>

                            <div class="form-group">
                                <?=FORM::label($forms['number_format']['id'], __('Money format'), array('class'=>'control-label','for'=>$forms['number_format']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-currency-format/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=Form::select($forms['number_format']['key'], i18n::currencies_defaults(), $forms['number_format']['value'])?>
                                <span class="help-block">
                                    <?=__("Number format is how you want to display numbers related to advertisements. More specific advertisement price. Every country have a specific way of dealing with decimal digits.")?>
                                </span>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['date_format']['id'], __('Date format'), array('class'=>'control-label', 'for'=>$forms['date_format']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/change-date-format/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['date_format']['key'], $forms['date_format']['value'], array(
                                    'placeholder' => "d/m/Y",
                                    'class' => 'form-control',
                                    'id' => $forms['date_format']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Each advertisement has a publish date. By selecting format, you can change how it is shown on your website.")?>
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?=__("Time Zone")?>:</label>
                                <a target="_blank" href="https://docs.yclas.com/how-to-change-time-zone/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?= FORM::select($forms['timezone']['key'], Date::get_timezones(), core::request('TIMEZONE',date_default_timezone_get()), array(
                                        'placeholder' => "Madrid [+1:00]",
                                        'class' => 'tips form-control',
                                        'id' => $forms['timezone']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?=__("Measurement Units")?>:</label>
                                <?=FORM::select($forms['measurement']['key'], array('metric' => __("Metric"), 'imperial' => __("Imperial")), $forms['measurement']['value'], array(
                                        'placeholder' => $forms['measurement']['value'],
                                        'class' => 'form-control',
                                        'id' => $forms['measurement']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("Measurement units used by the system.")?>
                                </span>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsAdditionalFeatures" class="tab-pane fade">
                            <h4><?=__("Enable Additional Features")?></h4>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['search_by_description']['id'], __("Include search by description"), array('class'=>'control-label', 'for'=>$forms['search_by_description']['id']))?>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['search_by_description']['key'], 1, (bool) $forms['search_by_description']['value'], array('id' => $forms['search_by_description']['id'].'1'))?>
                                    <?=Form::label($forms['search_by_description']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['search_by_description']['key'], 0, ! (bool) $forms['search_by_description']['value'], array('id' => $forms['search_by_description']['id'].'0'))?>
                                    <?=Form::label($forms['search_by_description']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Once set to TRUE, enables search to look for key words in description")?>
                                </span>
                            </div>

                            <div class="form-group">
                                <?= FORM::label($forms['search_multi_catloc']['id'], __("Multi select category and location search"), array('class'=>'control-label', 'for'=>$forms['search_multi_catloc']['id']))?>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['search_multi_catloc']['key'], 1, (bool) $forms['search_multi_catloc']['value'], array('id' => $forms['search_multi_catloc']['id'].'1'))?>
                                    <?=Form::label($forms['search_multi_catloc']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['search_multi_catloc']['key'], 0, ! (bool) $forms['search_multi_catloc']['value'], array('id' => $forms['search_multi_catloc']['id'].'0'))?>
                                    <?=Form::label($forms['search_multi_catloc']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("Once set to TRUE, enables multi select category and location search")?>
                                </span>
                            </div>

                            <?if(Core::config('general.subscriptions') == TRUE):?>
                                <div class="form-group">
                                    <?= FORM::label($forms['subscriptions_expire']['id'], __("Subscription Expire"), array('class'=>'control-label', 'for'=>$forms['subscriptions_expire']['id']))?>
                                    <div class="radio radio-primary">
                                        <?=Form::radio($forms['subscriptions_expire']['key'], 1, (bool) $forms['subscriptions_expire']['value'], array('id' => $forms['subscriptions_expire']['id'].'1'))?>
                                        <?=Form::label($forms['subscriptions_expire']['id'].'1', __('Enabled'))?>
                                        <?=Form::radio($forms['subscriptions_expire']['key'], 0, ! (bool) $forms['subscriptions_expire']['value'], array('id' => $forms['subscriptions_expire']['id'].'0'))?>
                                        <?=Form::label($forms['subscriptions_expire']['id'].'0', __('Disabled'))?>
                                    </div>
                                    <span class="help-block">
                                        <?=__("Once set to TRUE, if user subscription expires, the user and the ads get disabled, until renewal.")?>
                                    </span>
                                </div>
                            <?else:?>
                                <?= FORM::hidden($forms['subscriptions_expire']['key'], $forms['subscriptions_expire']['value']);?>
                            <?endif?>

                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsComments" class="tab-pane fade">
                            <h4><?=__("Comments Configuration")?></h4>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['blog_disqus']['id'], __('Disqus for blog'), array('class'=>'control-label', 'for'=>$forms['blog_disqus']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/how-to-create-a-blog/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['blog_disqus']['key'], $forms['blog_disqus']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'tips form-control',
                                    'id' => $forms['blog_disqus']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("You need to write your disqus ID to enable the service.")?>
                                </span>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['faq_disqus']['id'], __('Disqus for FAQ'), array('class'=>'control-label', 'for'=>$forms['faq_disqus']['id']))?>
                                <a target="_blank" href="https://docs.yclas.com/create-frequent-asked-questions-faq/">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['faq_disqus']['key'], $forms['faq_disqus']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['faq_disqus']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("You need to write your disqus ID to enable the service.")?>
                                </span>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsRecaptcha" class="tab-pane fade">
                            <h4><?=__("reCAPTCHA Configuration")?></h4>

                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['recaptcha_active']['id'], __("Enable reCAPTCHA as captcha provider"), array('class'=>'control-label', 'for'=>$forms['recaptcha_active']['id']))?>
                                <a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['recaptcha_active']['key'], 1, (bool) $forms['recaptcha_active']['value'], array('id' => $forms['recaptcha_active']['id'].'1'))?>
                                    <?=Form::label($forms['recaptcha_active']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['recaptcha_active']['key'], 0, ! (bool) $forms['recaptcha_active']['value'], array('id' => $forms['recaptcha_active']['id'].'0'))?>
                                    <?=Form::label($forms['recaptcha_active']['id'].'0', __('Disabled'))?>
                                </div>
                                <span class="help-block">
                                    <?=__("If advertisement is marked as spam, user is also marked. Can not publish new ads or register until removed from Black List! Also will not allow users from disposable email addresses to register.")?>
                                </span>
                            </div>
                            <div class="form-group">
                                <?= FORM::label($forms['recaptcha_type']['id'], __("reCAPTCHA type"), array('class'=>'control-label', 'for'=>$forms['recaptcha_type']['id']))?>
                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['recaptcha_type']['key'], 'checkbox', $forms['recaptcha_type']['value'] == 'checkbox' ? TRUE : FALSE , array('id' => $forms['recaptcha_type']['id'].'0'))?>
                                    <?=Form::label($forms['recaptcha_type']['id'].'0', __('Checkbox'))?>
                                    <?=Form::radio($forms['recaptcha_type']['key'], 'invisible', $forms['recaptcha_type']['value'] == 'invisible' ? TRUE : FALSE, array('id' => $forms['recaptcha_type']['id'].'1'))?>
                                    <?=Form::label($forms['recaptcha_type']['id'].'1', __('Invisible'))?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['recaptcha_sitekey']['id'], __('reCAPTCHA Site Key'), array('class'=>'control-label', 'for'=>$forms['recaptcha_sitekey']['id']))?>
                                <a target="_blank" href="https://www.google.com/recaptcha/admin#list">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['recaptcha_sitekey']['key'], $forms['recaptcha_sitekey']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['recaptcha_sitekey']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("You need to write reCAPTCHA Site Key to enable the service.")?>
                                </span>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['recaptcha_secretkey']['id'], __('reCAPTCHA Secret Key'), array('class'=>'control-label', 'for'=>$forms['recaptcha_secretkey']['id']))?>
                                <a target="_blank" href="https://www.google.com/recaptcha/admin#list">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                                <?=FORM::input($forms['recaptcha_secretkey']['key'], $forms['recaptcha_secretkey']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['recaptcha_secretkey']['id'],
                                ))?>
                                <span class="help-block">
                                    <?=__("You need to write your reCAPTCHA Secret Key to enable the service.")?>
                                </span>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsNotifications" class="tab-pane fade">
                            <h4><?=__("Notifications")?>
                                <a target="_blank" href="//docs.yclas.com/notification-system">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </h4>
                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['pusher_notifications']['id'], __("Enable Notifications"), array('class'=>'control-label', 'for'=>$forms['pusher_notifications']['id']))?>

                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['pusher_notifications']['key'], 1, (bool) $forms['pusher_notifications']['value'], array('id' => $forms['pusher_notifications']['id'].'1'))?>
                                    <?=Form::label($forms['pusher_notifications']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['pusher_notifications']['key'], 0, ! (bool) $forms['pusher_notifications']['value'], array('id' => $forms['pusher_notifications']['id'].'0'))?>
                                    <?=Form::label($forms['pusher_notifications']['id'].'0', __('Disabled'))?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['pusher_notifications_app_id']['id'], __('App ID'), array('class'=>'control-label', 'for'=>$forms['pusher_notifications_app_id']['id']))?>
                                <?=FORM::input($forms['pusher_notifications_app_id']['key'], $forms['pusher_notifications_app_id']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['pusher_notifications_app_id']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['pusher_notifications_key']['id'], __('Key'), array('class'=>'control-label', 'for'=>$forms['pusher_notifications_key']['id']))?>
                                <?=FORM::input($forms['pusher_notifications_key']['key'], $forms['pusher_notifications_key']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['pusher_notifications_key']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['pusher_notifications_secret']['id'], __('Secret'), array('class'=>'control-label', 'for'=>$forms['pusher_notifications_secret']['id']))?>
                                <?=FORM::input($forms['pusher_notifications_secret']['key'], $forms['pusher_notifications_secret']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['pusher_notifications_secret']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['pusher_notifications_cluster']['id'], __('Cluster'), array('class'=>'control-label', 'for'=>$forms['pusher_notifications_cluster']['id']))?>
                                    <?= FORM::select($forms['pusher_notifications_cluster']['key'],
                                        array('mt1'=>'us-east-1','eu'=>'eu-west-1','ap1'=>'ap-southeast-1','ap2'=>'ap-south-1','us2'=>'us-east-2'), $forms['pusher_notifications_cluster']['value'], array(
                                    'class' => 'form-control',
                                    'id' => $forms['pusher_notifications_cluster']['id'],
                                ))?>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsAlgolia" class="tab-pane fade">
                            <h4>Algolia Search
                                <a target="_blank" href="//docs.yclas.com/algolia-search">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </h4>
                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['algolia_search']['id'], __("Enable Algolia Search"), array('class'=>'control-label', 'for'=>$forms['algolia_search']['id']))?>

                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['algolia_search']['key'], 1, (bool) $forms['algolia_search']['value'], array('id' => $forms['algolia_search']['id'].'1'))?>
                                    <?=Form::label($forms['algolia_search']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['algolia_search']['key'], 0, ! (bool) $forms['algolia_search']['value'], array('id' => $forms['algolia_search']['id'].'0'))?>
                                    <?=Form::label($forms['algolia_search']['id'].'0', __('Disabled'))?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['algolia_search_application_id']['id'], __('Application ID'), array('class'=>'control-label', 'for'=>$forms['algolia_search_application_id']['id']))?>
                                <?=FORM::input($forms['algolia_search_application_id']['key'], $forms['algolia_search_application_id']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['algolia_search_application_id']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['algolia_search_admin_key']['id'], __('Admin API Key'), array('class'=>'control-label', 'for'=>$forms['algolia_search_admin_key']['id']))?>
                                <?=FORM::input($forms['algolia_search_admin_key']['key'], $forms['algolia_search_admin_key']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['algolia_search_admin_key']['id'],
                                ))?>
                            </div>
                            <div class="form-group">
                                <?=FORM::label($forms['algolia_search_only_key']['id'], __('Search-Only API Key'), array('class'=>'control-label', 'for'=>$forms['algolia_search_only_key']['id']))?>
                                <?=FORM::input($forms['algolia_search_only_key']['key'], $forms['algolia_search_only_key']['value'], array(
                                    'placeholder' => "",
                                    'class' => 'form-control',
                                    'id' => $forms['algolia_search_only_key']['id'],
                                ))?>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                        <div id="tabSettingsCarQuery" class="tab-pane fade">
                            <h4>CarQuery
                                <a target="_blank" href="//docs.yclas.com/vehicle-data">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </h4>
                            <hr>

                            <div class="form-group">
                                <?= FORM::label($forms['carquery']['id'], __("Enable CarQuery"), array('class'=>'control-label', 'for'=>$forms['carquery']['id']))?>

                                <div class="radio radio-primary">
                                    <?=Form::radio($forms['carquery']['key'], 1, (bool) $forms['carquery']['value'], array('id' => $forms['carquery']['id'].'1'))?>
                                    <?=Form::label($forms['carquery']['id'].'1', __('Enabled'))?>
                                    <?=Form::radio($forms['carquery']['key'], 0, ! (bool) $forms['carquery']['value'], array('id' => $forms['carquery']['id'].'0'))?>
                                    <?=Form::label($forms['carquery']['id'].'0', __('Disabled'))?>
                                </div>
                            </div>
                            <hr>
                            <p>
                                <?=FORM::button('submit', __('Save'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'settings', 'action'=>'general'))))?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
