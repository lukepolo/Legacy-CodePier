// NOTE - this will not work with PUT!!!
// https://github.com/symfony/symfony/issues/9226
export const getFormData = function(form) {
    if (!$(form).is('form')) {
        form = $(form).find('form')[0]
    }
    return new FormData(form)
}