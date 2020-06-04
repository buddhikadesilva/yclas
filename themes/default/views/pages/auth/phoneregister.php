<?if (Core::config('general.sms_auth') == TRUE ):?>
    <div class="page-header">
        <h1><?=_e('Phone Register')?></h1>
    </div>

    <?=View::factory('pages/auth/phoneregister-form')?>
<?endif?>
