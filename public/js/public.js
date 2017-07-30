webpackJsonp([2],{

/***/ "./resources/assets/js/public.js":
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {
$(document).on('click', '.js-toggle-forms', function () {
    $('input[type="email"]:hidden').val($('input[type="email"]:visible').val());
    $('input[type="password"]:hidden').val($('input[type="password"]:visible').val());
    $('#register_form, #login_form').toggleClass('hide');
});

$(document).on('click', '.js-toggle-forgot', function () {
    $('input[type="email"]:hidden').val($('input[type="email"]:visible').val());
    $('#forgot_form, #login_form').toggleClass('hide');
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__("./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ 1:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("./resources/assets/js/public.js");


/***/ })

},[1]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9yZXNvdXJjZXMvYXNzZXRzL2pzL3B1YmxpYy5qcyJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJvbiIsInZhbCIsInRvZ2dsZUNsYXNzIl0sIm1hcHBpbmdzIjoiOzs7Ozs7QUFDQUEsRUFBRUMsUUFBRixFQUFZQyxFQUFaLENBQWUsT0FBZixFQUF3QixrQkFBeEIsRUFBNEMsWUFBWTtBQUNwREYsTUFBRSw0QkFBRixFQUFnQ0csR0FBaEMsQ0FBb0NILEVBQUUsNkJBQUYsRUFBaUNHLEdBQWpDLEVBQXBDO0FBQ0FILE1BQUUsK0JBQUYsRUFBbUNHLEdBQW5DLENBQXVDSCxFQUFFLGdDQUFGLEVBQW9DRyxHQUFwQyxFQUF2QztBQUNBSCxNQUFFLDZCQUFGLEVBQWlDSSxXQUFqQyxDQUE2QyxNQUE3QztBQUNILENBSkQ7O0FBTUFKLEVBQUVDLFFBQUYsRUFBWUMsRUFBWixDQUFlLE9BQWYsRUFBd0IsbUJBQXhCLEVBQTZDLFlBQVk7QUFDckRGLE1BQUUsNEJBQUYsRUFBZ0NHLEdBQWhDLENBQW9DSCxFQUFFLDZCQUFGLEVBQWlDRyxHQUFqQyxFQUFwQztBQUNBSCxNQUFFLDJCQUFGLEVBQStCSSxXQUEvQixDQUEyQyxNQUEzQztBQUNILENBSEQsRSIsImZpbGUiOiIvanMvcHVibGljLmpzIiwic291cmNlc0NvbnRlbnQiOlsiXHJcbiQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWZvcm1zJywgZnVuY3Rpb24gKCkge1xyXG4gICAgJCgnaW5wdXRbdHlwZT1cImVtYWlsXCJdOmhpZGRlbicpLnZhbCgkKCdpbnB1dFt0eXBlPVwiZW1haWxcIl06dmlzaWJsZScpLnZhbCgpKVxyXG4gICAgJCgnaW5wdXRbdHlwZT1cInBhc3N3b3JkXCJdOmhpZGRlbicpLnZhbCgkKCdpbnB1dFt0eXBlPVwicGFzc3dvcmRcIl06dmlzaWJsZScpLnZhbCgpKVxyXG4gICAgJCgnI3JlZ2lzdGVyX2Zvcm0sICNsb2dpbl9mb3JtJykudG9nZ2xlQ2xhc3MoJ2hpZGUnKVxyXG59KVxyXG5cclxuJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtZm9yZ290JywgZnVuY3Rpb24gKCkge1xyXG4gICAgJCgnaW5wdXRbdHlwZT1cImVtYWlsXCJdOmhpZGRlbicpLnZhbCgkKCdpbnB1dFt0eXBlPVwiZW1haWxcIl06dmlzaWJsZScpLnZhbCgpKVxyXG4gICAgJCgnI2ZvcmdvdF9mb3JtLCAjbG9naW5fZm9ybScpLnRvZ2dsZUNsYXNzKCdoaWRlJylcclxufSlcclxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vcmVzb3VyY2VzL2Fzc2V0cy9qcy9wdWJsaWMuanMiXSwic291cmNlUm9vdCI6IiJ9