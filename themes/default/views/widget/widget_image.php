<?if ($widget->text_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->text_title?></h3>
    </div>
<?endif?>

<div class="panel-body">

<?if($widget->redirect_url != ''):?><a href="<?=$widget->redirect_url?>"><?endif?>

<img src="<?=$widget->image_url?>" class="img-responsive center-block">

<?if($widget->redirect_url != ''):?></a><?endif?>

</div>