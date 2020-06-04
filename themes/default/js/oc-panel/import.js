$(function(){
    $("#import_process").click(function(event) {
        var href = $(this).attr('href');
        event.preventDefault();
        $(this).text("Processing...");
        $("#delete_queue").hide();
        process(href);            
    }); 

    $('#csv_upload').click( function() {
        //check whether browser fully supports all File API
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            //get the file size and file type from file input field
            var fsize = $('#csv_file_ads')[0].files[0].size;
            
            if(fsize>1048576) //do something if file size more than 1 mb (1048576)
            {
                alert(fsize +" bites\nToo big!");
                event.preventDefault();
            }
        }
    });
    
});


function process(href)
{
    $.ajax({ url: href,
        }).done(function ( data ) {

            $("#count_import").text(data+"%");

            if (isNumeric(data) && data < 100)
                process(href);
            else
                $("#import_process").hide();
            
    });
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}