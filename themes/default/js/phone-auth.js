// intlTelInput plugin
if ($('input[name="phone"]').length != 0) {
    $('input[name="phone"]').intlTelInput({
        formatOnDisplay: false,
        initialCountry: $('input[name="phone"]').data('country')
    });

    $('form').submit(function() {
        var $phoneField = $(this).find('input[name="phone"]');
        $phoneField.val($phoneField.intlTelInput("getNumber"));
    });
}
