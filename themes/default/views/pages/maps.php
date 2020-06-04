<html lang="en">
<head>
    <title><?=$title?></title>
    <meta charset="<?=Kohana::$charset?>">
    <?if (isset($_SERVER['HTTP_USER_AGENT']) and (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) : ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?endif?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?=__('Maps')?>">
    <?if (Theme::get('premium')!=1):?>
        <meta name="author" content="yclas.com">
    <?endif?>
    <script type="text/javascript" src="//maps.google.com/maps/api/js?libraries=geometry&v=3&key=<?=core::config('advertisement.gm_api_key')?>&language=<?=i18n::get_display_language(i18n::$locale)?>"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/maplace-js/0.2.7/maplace.min.js"></script>

    <script type="text/javascript">
        <?if(!core::get('id_user') OR !isset($user)):?>
            var locations = [
                <?if(core::count($ads) > 0):?>
                    <?foreach ($ads as $ad):?>
                        {
                            lat: <?=$ad->latitude?>,
                            lon: <?=$ad->longitude?>,
                            title: '<?=htmlentities(str_replace('"',' ',json_encode($ad->title)),ENT_QUOTES)?>',
                            animation: google.maps.Animation.DROP,
                            <?if(( $icon_src = $ad->category->get_icon() )!==FALSE AND !is_numeric(core::get('id_ad'))):?>
                                icon: '<?=Core::imagefly($icon_src,50,50)?>',
                            <?endif?>

                            <?if (core::get('controls') != 0) :?>
                                html: '<div style="overflow: visible; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; padding: 6px 0; width: 100%; height: auto;"><p style="margin-bottom:10px;margin-top:-5px;"><?=htmlentities($ad->address,ENT_QUOTES)?></p><p style="margin:0;"><?if($ad->get_first_image() !== NULL):?><img src="<?=Core::imagefly($ad->get_first_image(),100,100)?>" style="float:left; width:100px; margin-right:10px; margin-bottom:6px;"><?endif?><a target="_blank" style="text-decoration:none; margin-bottom:15px; color:#4272db;" href="<?=Route::url('ad',  array('category'=>$ad->category,'seotitle'=>$ad->seotitle))?>"><?=htmlentities($ad->title,ENT_QUOTES)?></a><br><br><?=htmlentities(Text::limit_chars(Text::removenl(Text::removebbcode($ad->description)), 255, NULL, TRUE),ENT_QUOTES)?></p></div>',
                            <?else:?>
                                html: '<div style="overflow: visible; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; padding: 6px 0; width: 100%; height: auto;"><p style="margin:0;"><a target="_blank" style="text-decoration:none; margin-bottom:15px; color:#4272db;" href="<?=Route::url('ad',  array('category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?=htmlentities($ad->title,ENT_QUOTES)?></a></p></div>',
                            <?endif?>
                        },
                    <?endforeach?>
                <?elseif(Core::config('advertisement.center_lat') AND Core::config('advertisement.center_lon')):?>
                    {
                        lat: <?=Core::config('advertisement.center_lat')?>,
                        lon: <?=Core::config('advertisement.center_lon')?>,
                        icon: ' ',
                    },
                <?endif?>
            ];
        <?else:?>
            var locations = [
                {
                    lat: <?=$user->latitude?>,
                    lon: <?=$user->longitude?>,
                    title: '<?=htmlentities(str_replace('"',' ',json_encode($user->name)),ENT_QUOTES)?>',
                    animation: google.maps.Animation.DROP,
                    <?if (core::get('controls') != 0) :?>
                        html: '<div style="overflow: visible; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; padding: 6px 0; width: 100%; height: auto;"><p style="margin-bottom:10px;margin-top:-5px;"><?=htmlentities($user->location,ENT_QUOTES)?></p><p style="margin:0;"><?if($user->get_profile_image() !== NULL):?><img src="<?=Core::imagefly($user->get_profile_image(),50,50)?>" style="float:left; width:100px; margin-right:10px; margin-bottom:6px;"><?endif?><a target="_blank" style="text-decoration:none; margin-bottom:15px; color:#4272db;" href="<?=Route::url('profile',  array('seoname'=>$user->seoname))?>"><?=htmlentities($user->name,ENT_QUOTES)?></a><br><br><?=htmlentities(Text::limit_chars(Text::removenl(Text::removebbcode($user->description)), 255, NULL, TRUE),ENT_QUOTES)?></p></div>',
                    <?else:?>
                        html: '<div style="overflow: visible; cursor: default; clear: both; position: relative; background-color: rgb(255, 255, 255); border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; padding: 6px 0; width: 100%; height: auto;"><p style="margin:0;"><a target="_blank" style="text-decoration:none; margin-bottom:15px; color:#4272db;" href="<?=Route::url('profile',  array('seoname'=>$user->seoname))?>"><?=htmlentities($user->name,ENT_QUOTES)?></a></p></div>',
                    <?endif?>
                }
            ];
        <?endif?>

        $(function() {
            new Maplace({
                locations: locations,
                controls_on_map: false,
                map_options: {
                    <?if(Core::config('advertisement.map_zoom')) :?>
                        maxZoom: <?=Core::config('advertisement.map_zoom')?>,
                    <?endif?>
                },
                <?if(! Core::config('advertisement.map_zoom')) :?>
                    pan_on_click: false,
                <?endif?>

                <? if(core::config('advertisement.map_style') != '') :?>
                    styles: {
                        'Default': <?=core::config('advertisement.map_style')?>
                    }
                <?endif?>
            }).Load();
        });
    </script>
    <style type="text/css">
        .close {
            color: #000;
            float: right;
            font-size: 25px;
            line-height: 1;
            opacity: 0.2;
            padding: 0;
            cursor: pointer;
            border: 0 none;
            background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
            position: relative;
            right: -20px;
            top: 0px;
        }
        .close:after {
            content: '✖';
        }
    </style>
</head>

<body style="<?=core::get('controls') != 0 ? 'padding:0 20px 20px;' : 'margin:0;'?>">
    <?if (core::get('controls') != 0) :?>
        <div>
            <button class="close" onclick="window.history.back();">
                <span>&nbsp;</span>
            </button>
        </div>
    <?endif?>

    <div id="gmap" style="height:<?=$height?>;width:<?=$width?>;"></div>

    <?=(Kohana::$environment === Kohana::DEVELOPMENT)? View::factory('profiler'):''?>
</body>
</html>
