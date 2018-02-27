function toggleForms() {
  $('input[type="email"]:hidden').val($('input[type="email"]:visible').val());
  $('input[type="password"]:hidden').val(
    $('input[type="password"]:visible').val(),
  );
  $("#register_form, #login_form").toggleClass("hide");
}

let url = new URL(window.location.href);
if (url.searchParams.get("showRegisterForm")) {
  toggleForms();
}

$(document).on("click", ".js-toggle-forms", function() {
  toggleForms();
});

$(document).on("click", ".js-toggle-forgot", function() {
  $('input[type="email"]:hidden').val($('input[type="email"]:visible').val());
  $("#forgot_form, #login_form").toggleClass("hide");
});
