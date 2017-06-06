
require('jcf-forms')

$(document).on('click', '.toggle-forms', function() {

    $('input[type="email"]:hidden').val($('input[type="email"]:visible').val())
    $('input[type="password"]:hidden').val($('input[type="password"]:visible').val())

    $('#register_form, #login_form').toggleClass('hide')
})