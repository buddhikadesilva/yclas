$(function(){
    $('textarea[name=description]').sceditor({
    		format: 'bbcode',
            plugins: "bbcode,plaintext",
            toolbar: "bold,italic,underline,strike|left,center,right,justify|" +
            "bulletlist,orderedlist|link,unlink,image,youtube|source",
            resizeEnabled: "true",
            width: '100%',
            emoticonsEnabled: false,
            emoticonsCompat: "false",
            rtl: $('meta[name="application-name"]').data('rtl'),
            style: $('meta[name="application-name"]').data('baseurl') + "themes/default/css/jquery.sceditor.default.min.css",
            enablePasteFiltering: "true"});

    //sceditor for validation, updates iframe on submit
    $("button[name=submit]").click(function(){
        $("textarea[name=description]").data("sceditor").updateOriginal();
    });
});
