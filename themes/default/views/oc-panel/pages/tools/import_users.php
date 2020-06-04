<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title">
    <?=__('Import tool for users')?>

    <a target="_blank" href="https://docs.yclas.com/how-to-import-users/">
        <i class="fa fa-question-circle"></i>
    </a>
</h1>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?=__('Upload CSV file')?></div>
            </div>

            <div class="panel-body">
                <p>
                    <?=__('Please use the correct CSV format')?> <a href="https://cdn.rawgit.com/yclas/yclas/master/install/samples/import/users.csv">
                        <?=__('download example')?>.
                    </a>

                    <br><br>

                    <span class="label label-info"><?=__('Hosting limit')?></span>

                    <br>upload_max_filesize: <?=ini_get('upload_max_filesize')?>,
                    <br>max_execution_time: <?=ini_get('max_execution_time')?> <?=__('seconds')?> <?=__('limited to 10.000 at a time')?>, <?=__('1 MB file')?>.
                </p>

                <?= FORM::open(Route::url('oc-panel', ['controller' => 'importusers', 'action' => 'csv']), ['enctype' => 'multipart/form-data'])?>
                    <div class="form-group">
                        <label for=""> <?=__('Import Users')?></label>
                        <input type="file" name="csv_file_users" id="csv_file_users" class="form-control"/>
                    </div>

                    <?= FORM::button('submit', __('Upload'), ['type'=>'submit', 'id'=>'csv_upload', 'class'=>'btn btn-primary'])?>
                <?= FORM::close()?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><?=__('Process Queue')?></div>
            </div>
            <div class="panel-body">
                <p>
                    <?if($users_import > 0):?>
                        <div id="count_import">
                            <?=sprintf(__('You got %d users to get processed'), $users_import)?>
                        </div>
                        <p>
                            <a class="btn btn-success" id="import_process" href="<?=Route::url('oc-panel', ['controller' => 'importusers', 'action'=>'process'])?>">
                                <?=__('Process')?>
                            </a>

                            <a class="btn btn-danger btn-xs" id="delete_queue" href="<?=Route::url('oc-panel', ['controller' => 'importusers', 'action' => 'deletequeue'])?>">
                                <?=__('Delete')?>
                            </a>
                        <p>
                    <?else:?>
                        <?=__('Not any users to be processed')?>
                    <?endif?>
                </p>
            </div>

        </div>
    </div>

</div>
