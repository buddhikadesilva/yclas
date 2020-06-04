<?if (Core::config('general.sms_auth') == TRUE ):?>
<div class="page-header">
    <h1><?=_e('Phone Login')?></h1>
</div>
<form class="well form-horizontal auth" method="post" action="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'phonelogin'))?>">         
    <?=Form::errors()?>
     
    <div class="form-group">
        <label class="col-sm-4 control-label"><?=_e('Phone')?></label>
        <div class="col-sm-8">
            <?= FORM::input('phone', '', array('class'=>'form-control', 'id'=>'phone', 'type'=>'phone' ,'required','placeholder'=>__('Phone'), 'data-country' => core::config('general.country')))?>
        </div>
    </div>
    
    <hr>

    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-8">
            <ul class="list-inline">
                <li>
                    <button type="submit" class="btn btn-primary">
                        <?=_e('Login')?>
                    </button>
                </li>
                <li>
                    <?=_e('Don’t Have an Account?')?>
                    <a data-toggle="modal" data-dismiss="modal" href="<?=Route::url('oc-panel',array('directory'=>'user','controller'=>'auth','action'=>'register'))?>#register-modal">
                        <?=_e('Register')?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?=Form::redirect()?>
    <?=Form::CSRF('phonelogin')?>
</form>
<?endif?>