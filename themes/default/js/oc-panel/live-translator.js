// selectize for category and location selects
$(function(){
    $('.editable').editable({
        type: 'text',
        toggle: 'mouseenter',
        params: {where: 'original', exact: 1},
        placement: 'bottom',
    });
});
