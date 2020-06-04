$(function(){
    var last_page = $('ul.pagination #last').attr('data-last');
    $('#users').infinitescroll({
            navSelector     : "#next", // selector for the paged navigation (it will be hidden)
            nextSelector    : "a#next:last", // selector for the NEXT link (to page 2)
            itemSelector    : "#users", // selector for all items you'll retrieve
            loadingImg      : "//cdn.jsdelivr.net/jquery.infinitescroll/2.1/ajax-loader.gif",
            maxPage         : last_page,
            donetext        : 'No more users',
        });
});