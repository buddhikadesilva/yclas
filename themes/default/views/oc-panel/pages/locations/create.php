<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title" id="crud-<?=$name?>">
    <?=__('New')?> <?=Text::ucfirst(__($name))?>
</h1>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=__('Location details')?></h3>
            </div>
            <div class="panel-body">
                <?= FORM::open(Route::url('oc-panel',array('controller'=>'location','action'=>'create')), array('class'=>'form-horizontal', 'enctype'=>'multipart/form-data'))?>
                    <fieldset>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('name', __('Name'), array('class'=>'control-label', 'for'=>'name'))?>
                                <?= FORM::input('name', core::request('name'), array('placeholder' => __('Name'), 'class' => 'form-control', 'id' => 'name', 'required'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('id_location_parent', __('Parent'), array('class'=>'control-label', 'for'=>'id_location_parent'))?>
                                <?= FORM::select('id_location_parent', $locations, core::request('id_location_parent'), array('placeholder' => __('Parent'), 'class' => 'form-control', 'id' => 'id_location_parent'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('seoname', __('Seoname'), array('class'=>'control-label', 'for'=>'seoname'))?>
                                <?= FORM::input('seoname', core::request('seoname'), array('placeholder' => __('Seoname'), 'class' => 'form-control', 'id' => 'seoname'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('description', __('Description'), array('class'=>'control-label', 'for'=>'description'))?>
                                <?= FORM::textarea('description', '', array('class'=>'form-control','id' => 'description','data-editor'=>'html', 'placeholder'=>__('Description')))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('latitude', __('Latitude'), array('class'=>'control-label', 'for'=>'latitude'))?>
                                <?= FORM::input('latitude', core::request('latitude'), array('placeholder' => __('Latitude'), 'class' => 'form-control', 'id' => 'latitude'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::label('longitude', __('Longitude'), array('class'=>'control-label', 'for'=>'longitude'))?>
                                <?= FORM::input('longitude', core::request('longitude'), array('placeholder' => __('Longitude'), 'class' => 'form-control', 'id' => 'longitude'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?= FORM::button('submit', __('Create'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'location','action'=>'create'))))?>
                            </div>
                        </div>
                    </fieldset>
                <?= FORM::close()?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=__('Find latitude & longitude')?></h3>
            </div>
            <div class="panel-body">
                <?= FORM::input('address', Request::current()->post('address'), array('class'=>'form-control', 'id'=>'address', 'placeholder'=>__('Type address')))?>
                <div class="popin-map-container">
                    <div class="map-inner" id="map" 
                        data-lat="<?=core::config('advertisement.center_lat')?>" 
                        data-lon="<?=core::config('advertisement.center_lon')?>"
                        data-zoom="<?=core::config('advertisement.map_zoom')?>" 
                        style="height:200px;width:100%">
                    </div>
                </div>
                <ul class="list-inline">
                    <li><?=__('Latitude')?>: <span id="preview_lat">0</span></li>
                    <li><?=__('Longitude')?>: <span id="preview_lon">0</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
