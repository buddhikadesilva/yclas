$('textarea[name=message]:not(.disable-bbcode)').sceditor({
    plugins: "bbcode,plaintext",
    toolbar: "bold,italic,underline,strike,|left,center,right,justify|" +
    "bulletlist,orderedlist|link,unlink,youtube|source",
    resizeEnabled: "true",
    rtl: $('meta[name="application-name"]').data('rtl'),
    style: $('meta[name="application-name"]').data('baseurl') + "themes/default/css/jquery.sceditor.default.min.css",
	emoticonsEnabled: false
});

$(".message").hover(function() {
        $(this).css('cursor','pointer');
    },
    function() {
        $(this).css('cursor','auto');
});

$(".message").click(function() {
    window.location = $(this).data("url");
    return false;
});

// Modal confirmation
$('[data-toggle="confirmation"]').click(function(event) {
    var href = $(this).attr('href');
    var title = $(this).attr('title');
    var text = $(this).data('text');
    var confirmButtonText = $(this).data('btnoklabel');
    var cancelButtonText = $(this).data('btncancellabel');
    event.preventDefault();
    swal({
        title: title,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        allowOutsideClick: true,
    },
    function(){
        window.open(href,"_self");
    });
});
