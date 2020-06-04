$(function(){
    $('#csv_upload').click( function() {
        //check whether browser fully supports all File API
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            //get the file size and file type from file input field
            var fsize = $('#csv_file_categories')[0].files[0].size;
            
            if(fsize>1048576) //do something if file size more than 1 mb (1048576)
            {
                alert(fsize +" bites\nToo big!");
                event.preventDefault();
            }

            //get the file size and file type from file input field
            var fsize = $('#csv_file_locations')[0].files[0].size;
            
            if(fsize>1048576) //do something if file size more than 1 mb (1048576)
            {
                alert(fsize +" bites\nToo big!");
                event.preventDefault();
            }
        }
    });
});