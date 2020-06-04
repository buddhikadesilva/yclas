<?defined('SYSPATH') or exit('Install must be loaded from within index.php!');?>

<?if (!empty(install::$error_msg)):?>
    <div class="alert alert-danger animated fadeInDown">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?=install::$error_msg?>
    </div>
<?endif?>

<?if(!empty(install::$msg)):?>
    <?if(install::is_compatible()):?>
        <div class="alert alert-info animated fadeInDown">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?=__("We have detected some incompatibilities, installation may not work as expected but you can try.")?>
        </div>
    <?endif?>
    <?foreach(install::$msg as $msg):?>
        <div class="alert alert-warning animated fadeInDown">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?=$msg?>
        </div>
    <?endforeach?>
<?endif?>

<div class="alert animated fadeInDown">
    <p class="text-danger"><strong>Your hosting seems to be not compatible. Check your settings.</strong></p>
    <p>We have partnership with hosting companies to assure compatibility. And we include:</p>
    <br>
    <ul>
        <li>100% Compatible High Speed Hosting</li>
        <li>Get a huge discount on our Pro pack with all themes.</li>
        <li>Professional Installation and Support.</li>
    </ul>
    <br>
    <p>
        <a class="btn btn-default btn-large" href="https://yclas.com/self-hosted.html#hosting">
            <i class=" icon-shopping-cart icon-white"></i> Get Hosting! Less than $4 Month
        </a>
    </p>
</div>