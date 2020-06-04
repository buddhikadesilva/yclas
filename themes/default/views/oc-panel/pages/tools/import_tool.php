<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="row">
    <div class="col-lg-12 page-title-container">
        <h1 class="page-header page-title"><?=__('Import tool for locations and categories')?></h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=__('Upload CSV file')?></h3>
            </div>
            <div class="panel-body">
                <p>
                    <?=__('Please use the correct CSV format')?>,<br><?=__('limited to 10.000 at a time')?>,<br><?=__('1 MB file')?>.
                    <br>
                    <?=__('Categories')?>: <a href="https://cdn.rawgit.com/yclas/yclas/master/install/samples/import/categories.csv"><?=__('download example')?>.</a><br>
                    <?=__('Locations')?>: <a href="https://cdn.rawgit.com/yclas/yclas/master/install/samples/import/locations.csv"><?=__('download example')?>.</a>
                </p>
                <hr>
                <?= FORM::open(Route::url('oc-panel',array('controller'=>'tools','action'=>'import_tool')), array('class'=>'', 'enctype'=>'multipart/form-data'))?>
                    <div class="form-group">
                        <label for=""> <?=__('import Categories')?></label>
                        <input type="file" name="csv_file_categories" id="csv_file_categories" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for=""><?=__('import Locations')?></label>
                        <input type="file" name="csv_file_locations" id="csv_file_locations" class="form-control"/>
                    </div>
                        <?= FORM::button('submit', __('Upload'), array('type'=>'submit', 'class'=>'btn btn-primary', 'action'=>Route::url('oc-panel',array('controller'=>'tools','action'=>'import_tool'))))?>
                <?= FORM::close()?>
            </div>
        </div>
    </div>
</div>
