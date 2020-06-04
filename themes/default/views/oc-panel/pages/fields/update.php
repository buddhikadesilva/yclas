<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title">
    <?=__('Edit Custom Field')?>
</h1>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <form method="post" action="<?=Route::url('oc-panel',array('controller'=>'fields','action'=>'update','id'=>$name))?>">
                    <?=Form::errors()?>

                    <div class="form-group">
                        <label class="control-label"><?=__('Name')?></label>
                        <input DISABLED class="form-control" type="text" name="name" value="<?=$name?>" placeholder="<?=__('Name')?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?=__('Type')?></label>
                        <input  DISABLED class="form-control" type="text" id="cf_type_field_input" name="type" value="<?=$field_data['type']?>" placeholder="<?=__('Type')?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?=__('Label')?></label>
                        <input class="form-control" type="text" name="label" value="<?=$field_data['label']?>" placeholder="<?=__('Label')?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?=__('Tooltip')?></label>
                        <input class="form-control" type="text" name="tooltip" value="<?=(isset($field_data['tooltip']))?$field_data['tooltip']:""?>" placeholder="<?=__('Tooltip')?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?=__('Values')?></label>
                        <input class="form-control" type="text" id="cf_values_input" name="values" value="<?=(is_array($field_data['values']))? implode(",", $field_data['values']): $field_data['values']?>" placeholder="<?=__('Comma separated for select')?>">
                    </div>

                    <!-- multycategory selector -->
                    <div class="form-group">
                        <label class="control-label"><?=__('Categories')?></label>
                        <select id="categories" name="categories[]" multiple data-placeholder="<?=__('Choose 1 or several categories')?>">
                            <option></option>
                            <?foreach ($categories as $categ => $ctg):?>
                                <?if($categ !== 1 ):?>
                                    <?if(isset($field_data['categories']) AND is_array($field_data['categories']) AND in_array($categ, $field_data['categories'])):?>
                                        <option value="<?=$categ?>" selected><?=$ctg['name']?></option>
                                    <?else:?>
                                        <option value="<?=$categ?>"><?=$ctg['name']?></option>
                                    <?endif?>
                                <?endif?>
                            <?endforeach?>
                        </select>
                        <p class="help-block"><?=__('Selecting parent category also selects child categories.')?></p>
                    </div>

                    <div class="form-group">
                        <div class="checkbox check-success">
                            <input name="required" type="checkbox" id="checkbox_required" <?=($field_data['required']==TRUE)?'checked':''?>>
                            <label for="checkbox_required"><?=__('Required')?></label>
                        </div>
                        <div class="help-block"><?=__('Required field to submit a new ad.')?></div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox check-success">
                            <input name="searchable" type="checkbox" id="checkbox_searchable" <?=(isset($field_data['searchable']) AND $field_data['searchable']==TRUE)?'checked':''?>>
                            <label for="checkbox_searchable"><?=__('Searchable')?></label>
                        </div>
                        <div class="help-block"><?=__('Search in ads will include this field as well.')?></div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox check-success">
                            <input name="admin_privilege" type="checkbox" id="checkbox_admin_privilege" <?=(isset($field_data['admin_privilege']) AND $field_data['admin_privilege']==TRUE)?'checked':''?>>
                            <label for="checkbox_admin_privilege"><?=__('Admin Privileged')?></label>
                        </div>
                        <div class="help-block"><?=__('Can be seen and edited only by admin.')?></div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox check-success">
                            <input name="show_listing" type="checkbox" id="checkbox_show_listing" <?=(isset($field_data['show_listing']) AND $field_data['show_listing']==TRUE)?'checked':''?>>
                            <label for="checkbox_show_listing"><?=__('Show Listing')?></label>
                        </div>
                        <div class="help-block"><?=__('Can be seen in the list of ads while browsing.')?></div>
                    </div>

                    <hr>
                    <div class="form-actions">
                        <a href="<?=Route::url('oc-panel',array('controller'=>'fields','action'=>'index'))?>" class="btn btn-default ajax-load" title="<?=__('Cancel')?>"><?=__('Cancel')?></a>
                        <button type="submit" class="btn btn-primary"><?=__('Save')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <? if (Core::config('general.multilingual')) : ?>
            <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?= Route::url('oc-panel', array('controller' => 'fields', 'action' => 'update_translations', 'id' => $name)) ?>">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= __('Translations') ?></h3>
                    </div>
                    <? foreach (i18n::get_selectable_languages() as $locale => $language) : ?>
                        <? if (Core::config('i18n.locale') != $locale) : ?>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" href="#<?= $locale ?>" aria-expanded="false" aria-controls="<?= $locale ?>">
                                        <small class="fa fa-chevron-down pull-right"></small>
                                        <?= $locale ?>
                                    </a>
                                </h4>
                            </div>
                            <div class="panel-body collapse" id="<?= $locale ?>">
                                <div class="form-group">
                                    <?= FORM::label('translations_label_' . $locale, _e('Name'), array('class' => 'col-xs-12 control-label', 'for' => 'translations_label_' . $locale)) ?>
                                    <div class="col-sm-12">
                                        <?= FORM::input('translations[label][' . $locale . ']', Model_Field::translate_label($field_data, $locale), array(
                                            'placeholder' => '',
                                            'class' => 'form-control',
                                            'id' => 'translations_label_' . $locale,
                                        )) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= FORM::label('translations_tooltip_' . $locale, _e('Tooltip'), array('class' => 'col-xs-12 control-label', 'for' => 'translations_tooltip_' . $locale)) ?>
                                    <div class="col-sm-12">
                                        <?= FORM::textarea('translations[tooltip][' . $locale . ']', Model_Field::translate_tooltip($field_data, $locale), array(
                                            'placeholder' => '',
                                            'rows' => 3, 'cols' => 50,
                                            'class' => 'form-control',
                                            'id' => 'translations_tooltip_' . $locale,
                                        )) ?>
                                    </div>
                                </div>
                                <? if ($field_data['type'] == 'select' OR $field_data['type'] == 'radio') : ?>
                                    <div class="form-group">
                                        <?= FORM::label('translations_values_' . $locale, _e('Values'), array('class' => 'col-xs-12 control-label', 'for' => 'translations_values_' . $locale)) ?>
                                        <div class="col-sm-12">
                                            <?= FORM::input('translations[values][' . $locale . ']', (is_array(Model_Field::translate_values($field_data, $locale))) ? implode(",", Model_Field::translate_values($field_data, $locale)) : Model_Field::translate_values($field_data, $locale), array(
                                                'placeholder' => __('Comma separated for select'),
                                                'class' => 'form-control',
                                                'id' => 'translations_values_' . $locale,
                                            )) ?>
                                        </div>
                                    </div>
                                <? else : ?>
                                    <?= FORM::hidden('translations[values][' . $locale . ']') ?>
                                <? endif ?>
                                <button type="submit" class="btn btn-primary"><?= __('Submit translations') ?></button>
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                </div>
            </form>
        <? endif ?>
    </div>
</div>
