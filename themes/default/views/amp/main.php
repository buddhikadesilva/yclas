<!doctype html>
<html amp>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title><?=$title?></title>
    <?if (isset($canonical)):?>
        <link rel="canonical" href="<?=$canonical?>" />
    <?endif?>
    <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
    <script src="https://cdn.ampproject.org/v0.js" async></script>
    <?if (isset($structured_data)):?>
        <?=$structured_data?>
    <?endif?>
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <style amp-custom>
        /* Merriweather fonts */
        @font-face {
            font-family:'Merriweather';
            src:url('https://s1.wp.com/i/fonts/merriweather/merriweather-regular-webfont.woff2') format('woff2'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-regular-webfont.woff') format('woff'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-regular-webfont.ttf') format('truetype'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-regular-webfont.svg#merriweatherregular') format('svg');
            font-weight:400;
            font-style:normal;
        }

        @font-face {
            font-family:'Merriweather';
            src:url('https://s1.wp.com/i/fonts/merriweather/merriweather-italic-webfont.woff2') format('woff2'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-italic-webfont.woff') format('woff'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-italic-webfont.ttf') format('truetype'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-italic-webfont.svg#merriweatheritalic') format('svg');
            font-weight:400;
            font-style:italic;
        }

        @font-face {
            font-family:'Merriweather';
            src:url('https://s1.wp.com/i/fonts/merriweather/merriweather-bold-webfont.woff2') format('woff2'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bold-webfont.woff') format('woff'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bold-webfont.ttf') format('truetype'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bold-webfont.svg#merriweatherbold') format('svg');
            font-weight:700;
            font-style:normal;
        }

        @font-face {
            font-family:'Merriweather';
            src:url('https://s1.wp.com/i/fonts/merriweather/merriweather-bolditalic-webfont.woff2') format('woff2'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bolditalic-webfont.woff') format('woff'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bolditalic-webfont.ttf') format('truetype'),
                url('https://s1.wp.com/i/fonts/merriweather/merriweather-bolditalic-webfont.svg#merriweatherbold_italic') format('svg');
            font-weight:700;
            font-style:italic;
        }

        /* Generic WP styling */
        amp-img.alignright { float: right; margin: 0 0 1em 1em; }
        amp-img.alignleft { float: left; margin: 0 1em 1em 0; }
        amp-img.aligncenter { display: block; margin-left: auto; margin-right: auto; }
        .alignright { float: right; }
        .alignleft { float: left; }
        .aligncenter { display: block; margin-left: auto; margin-right: auto; }

        .oc-caption.alignleft { margin-right: 1em; }
        .oc-caption.alignright { margin-left: 1em; }

        .amp-oc-enforced-sizes {
            /** Our sizes fallback is 100vw, and we have a padding on the container; the max-width here prevents the element from overflowing. **/
            max-width: 100%;
        }

        .amp-oc-unknown-size img {
            /** Worst case scenario when we can't figure out dimensions for an image. **/
            /** Force the image into a box of fixed dimensions and use object-fit to scale. **/
            object-fit: contain;
        }

        /* Logo */
        .fixed-height-container {
            position: relative;
            width: 100%;
            height: 50px;
        }

        /* Site Name */
        .amp-oc-title-bar h3{
            margin: 0 auto;
        }

        /* Template Styles */
        .amp-oc-content, .amp-oc-title-bar div {
            max-width: 720px;
            margin: 0 auto;
        }

        body {
            font-family: 'Merriweather', Serif;
            font-size: 16px;
            line-height: 1.8;
            background: #fff;
            color: #444444;
            padding-bottom: 100px;
        }

        .amp-oc-content {
            padding: 16px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            font-weight: 400;
            color: #444444;
        }

        .amp-oc-title {
            margin: 20px 0 0 0;
            font-size: 36px;
            line-height: 1.1;
            font-weight: 700;
            color: #379e15;
        }

        .amp-oc-meta {
            margin-bottom: 16px;
        }

        p,
        ol,
        ul,
        figure {
            margin: 0 0 24px 0;
        }

        a,
        a:visited {
            color: #41bb19;
        }

        a:hover,
        a:active,
        a:focus {
            color: #00BE87;
        }


        /* UI Fonts */
        .amp-oc-meta,
        nav.amp-oc-title-bar,
        .oc-caption-text,
        .amp-oc-link {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen-Sans", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif;
            font-size: 15px;
        }


        /* Meta */
        ul.amp-oc-meta {
            padding: 24px 0 0 0;
            margin: 0 0 24px 0;
        }

        ul.amp-oc-meta li {
            list-style: none;
            display: inline-block;
            margin: 0;
            line-height: 24px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        ul.amp-oc-meta li:before {
            content: "\2022";
            margin: 0 8px;
        }

        ul.amp-oc-meta li:first-child:before {
            display: none;
        }

        .amp-oc-meta,
        .amp-oc-meta a {
            color: #555555;
        }

        .amp-oc-meta .screen-reader-text {
            /* from twentyfifteen */
            clip: rect(1px, 1px, 1px, 1px);
            height: 1px;
            overflow: hidden;
            position: absolute;
            width: 1px;
        }

        .amp-oc-byline amp-img {
            border-radius: 50%;
            border: 0;
            background: #f3f6f8;
            position: relative;
            top: 6px;
            margin-right: 6px;
        }


        /* Titlebar */
        nav.amp-oc-title-bar {
            background: #41bb19;
            padding: 4px 16px;
        }

        nav.amp-oc-title-bar div {
            line-height: 54px;
            color: #fff;
        }

        nav.amp-oc-title-bar a {
            color: #fff;
            text-decoration: none;
        }

        nav.amp-oc-title-bar .amp-oc-site-icon {
            /** site icon is 32px **/
            float: left;
            margin: 11px 8px 0 0;
            border-radius: 50%;
        }

        /* Captions */
        .oc-caption-text {
            padding: 8px 16px;
            font-style: italic;
        }

        .amp-oc-link > a {
            background: #41bb19;
            color: #fff;
            margin: 20px 0;
            padding: 15px;
            font-size: 14px;
            display: block;
            border-radius: 4px;
            text-align: center;
            text-decoration:none;
        }

        .amp-oc-link > a:active,
        .amp-oc-link > a:hover {
            opacity: 0.9;
            color: #fff;
        }

        .amp-oc-text-center {
            text-align: center;
        }
        ul.amp-oc-cf{
            padding: 0;
        }
        ul.amp-oc-cf li{
            list-style-type: none;
        }

        /* Quotes */
        blockquote {
            padding: 16px;
            margin: 8px 0 24px 0;
            border-left: 2px solid #87a6bc;
            color: #555555;
            background: #e9eff3;
        }

        blockquote p:last-child {
            margin-bottom: 0;
        }

        /* Other Elements */
        amp-carousel {
            background: #000;
        }

        amp-iframe,
        amp-youtube,
        amp-instagram,
        amp-vine {
            background: #f3f6f8;
        }

        amp-carousel > amp-img > img {
            object-fit: contain;
        }

        <?if (Theme::get('amp_top_bar_color') AND Theme::get('amp_custom_color')) :?>
            a,
            a:visited,
            a:hover,
            a:active,
            a:focus{
                color: <?=Theme::get('amp_custom_color')?>;
            }
            nav.amp-oc-title-bar {
                background: <?=Theme::get('amp_top_bar_color')?>;
            }
            .amp-oc-title {
                color: <?=Theme::get('amp_custom_color')?>;
            }
            .amp-oc-link > a {
                background: <?=Theme::get('amp_top_bar_color')?>;
            }

        <?endif?>

    </style>
</head>
<body>
<?=View::factory('amp/header')?>
<div class="amp-oc-content">
    <?=$content?>
</div>
</body>
</html>
