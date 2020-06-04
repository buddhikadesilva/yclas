<?if (Core::config('general.sms_auth') == TRUE ):?>
<div class="pad_10tb">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12':'col-xs-12">
                <div class="page-header">
                    <h1><?=_e('Phone Register')?></h1>
                </div>
                <?=View::factory('pages/auth/phoneregister-form')?>
            </div>
        </div>
    </div>
<?endif?>
