//settins scripts

// $('#allowed_formats option').each(function(){
//  $(this).attr('selected', 'selected');
// });

// jQuery.validator with bootstrap integration
jQuery.validator.setDefaults({
    highlight: function(element) {
        jQuery(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        jQuery(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'span',
    errorClass: 'label label-danger',
    errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$('.config').validate();

$('.plan-add').click(function() {
    $("#modalplan input[name='featured_days']").val('');
    $("#modalplan input[name='featured_price']").val('');
    $("#modalplan input[name='featured_days_key']").val('');
});
$('.plan-edit').click(function() {
    $('#modalplan').modal('show');
    $("#modalplan input[name='featured_days']").val($(this).data('days'));
    $("#modalplan input[name='featured_days_key']").val($(this).data('days'));
    $("#modalplan input[name='featured_price']").val($(this).data('price'));
});
$('.plan-delete').click(function(e) {
    e.preventDefault();
    $(this).closest('li').slideUp();
    $.ajax({url: $(this).attr('href')});
});

initPNotify();

function initPNotify() {
    $('form.ajax-load').submit(function(event) {
        $form = $(this);

        // process the form
        $.ajax({
            type        : $form.attr('method'),
            url         : $form.attr('action'),
            data        : $form.serialize(),
        })

            // using the done promise callback
            .done(function(data) {

                $(data).find('.alert').each(function() {
                    var notifyType = 'notice';
                    var notifyTitle = $(this).find('.alert-heading:first').text();
                    var notifyTitle = $(this).find('strong:first').text() + notifyTitle;
                    var notifyText = $(this).find('.close').remove();
                    var notifyText = $(this).find('.alert-heading').remove();
                    var notifyText = $(this).html();

                    if ($(this).hasClass('alert-info')) notifyType = 'info';
                    else if ($(this).hasClass('alert-success')) notifyType = 'success';
                    else if ($(this).hasClass('alert-danger')) notifyType = 'error';

                    new PNotify({
                        title: notifyTitle,
                        text: notifyText,
                        type: notifyType,
                        insert_brs: false,
                        delay: 4000,
                        styling: 'bootstrap3',
                    });
                });
            })

            .fail(function(data) {
                // show any errors
                console.log(data);
            });

        event.preventDefault();
    });
}

$('form').change(function () {
    emailService = $("input[name='service']:checked").val();

    if (emailService == 'elasticemail') {
        $('#tab-settings a[href="#tabSettingsElasticEmail"]').tab('show')
    } else if (emailService == 'mailgun') {
        $('#tab-settings a[href="#tabSettingsMailgun"]').tab('show')
    } else if (emailService == 'smtp') {
        $('#tab-settings a[href="#tabSettingsSMTPConfiguration"]').tab('show')
    }
});
