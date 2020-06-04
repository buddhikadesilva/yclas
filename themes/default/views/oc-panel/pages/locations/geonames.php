<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="page-header">
    <h1><?=($location AND $location->id_location > 1) ? $location->name.' – ':NULL?> <?=__('Import Locations')?></h1>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= FORM::open(Route::url('oc-panel',array('controller'=>'location','action'=>'geonames')).'?id_location='.Core::get('id_location', 1), array('id'=>'auto_locations_form', 'class'=>'form-horizontal', 'role'=>'form','enctype'=>'multipart/form-data'))?>
                            <div class="form-group" id="group-continent">
                                <label for="continent" class="col-sm-3 control-label" data-action="<?=__('Import continents')?>"><?=__('Continent')?></label>
                                <div class="col-sm-8">
                                    <select name="continent" id="continent" onchange="getPlaces(this.value,'country');" class="disable-select2 form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="group-country">
                                <label for="country" class="col-sm-3 control-label" data-action="<?=__('Import countries')?>"><?=__('Country')?></label>
                                <div class="col-sm-8">
                                    <select name="country" id="country" onchange="getPlaces(this.value,'province');" class="disable-select2 form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="group-province">
                                <label for="province" class="col-sm-3 control-label" data-action="<?=__('Import states/provinces')?>"><?=__('State')?> / <?=__('Province')?></label>
                                <div class="col-sm-8">
                                    <select name="province" id="province" onchange="getPlaces(this.value,'region');" class="disable-select2 form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="group-region">
                                <label for="region" class="col-sm-3 control-label" data-action="<?=__('Import counties/regions')?>"><?=__('County')?> / <?=__('Region')?></label>
                                <div class="col-sm-8">
                                    <select name="region" id="region" onchange="getPlaces(this.value,'city');" class="disable-select2 form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="group-city">
                                <label for="city" class="col-sm-3 control-label" data-action="<?=__('Import cities')?>"><?=__('City')?></label>
                                <div class="col-sm-8">
                                    <select name="city" id="city" class="disable-select2 form-control">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-8">
                                    <?if($location AND $location->id_geoname AND $location->fcodename_geoname):?>
                                        <input type="hidden" id="current_location_id_geoname" value="<?=$location->id_geoname?>" name="current_location_id_geoname">
                                        <input type="hidden" id="current_location_fcodename_geoname" value="<?=$location->fcodename_geoname?>" name="current_location_fcodename_geoname">
                                    <?endif?>
                                    <input type="hidden" id="auto_locations" value="" name="geonames_locations">
                                    <input type="hidden" id="auto_locations_lang" value="<?=substr(Core::config('i18n.locale'), 0, -3)?>" name="auto_locations_lang">
                                    <?= FORM::button('submit', __('Import'), array('type'=>'submit', 'class'=>'btn btn-primary', 'id'=>'auto_locations_import', 'action'=>Route::url('oc-panel',array('controller'=>'location','action'=>'geonames')).'?id_location='.Core::get('id_location', 1)))?>
                                    <?= FORM::button('reset', __('Reset'), array('type'=>'button', 'class'=>'btn btn-default', 'id'=>'auto_locations_import_reset'))?>
                                </div>
                            </div>
                        <?= FORM::close()?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
