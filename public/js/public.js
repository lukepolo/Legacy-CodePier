webpackJsonp([2],{

/***/ 13:
/***/ (function(module, exports) {

eval("document.body.addEventListener('keyup', function (e) {\n    var element = e.target;\n\n    if (element.tagName.toLowerCase() == 'input') {\n        if (determineFloat(element)) {\n            element.classList.add('visited');\n        }\n    }\n});\n\nfunction determineFloat(input) {\n    if (input.value.length === 0) {\n        input.classList.remove('active');\n        return false;\n    }\n    input.classList.add('active');\n    return true;\n}\n\nfunction checkAutoFill() {\n\n    setTimeout(function () {\n\n        var inputs = document.querySelectorAll('input');\n\n        for (i = 0; i < inputs.length; i++) {\n            if (determineFloat(inputs[i])) {\n                inputs[i].classList.add('visited');\n            }\n        }\n        checkAutoFill();\n    }, 100);\n}\n\ncheckAutoFill();//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMTMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vcmVzb3VyY2VzL2Fzc2V0cy9qcy9qY2YtZm9ybXMvaW5kZXguanM/MDJkNiJdLCJzb3VyY2VzQ29udGVudCI6WyJkb2N1bWVudC5ib2R5LmFkZEV2ZW50TGlzdGVuZXIoJ2tleXVwJywgZnVuY3Rpb24gKGUpIHtcbiAgICB2YXIgZWxlbWVudCA9IGUudGFyZ2V0O1xuXG4gICAgaWYgKGVsZW1lbnQudGFnTmFtZS50b0xvd2VyQ2FzZSgpID09ICdpbnB1dCcpIHtcbiAgICAgICAgaWYoZGV0ZXJtaW5lRmxvYXQoZWxlbWVudCkpIHtcbiAgICAgICAgICAgIGVsZW1lbnQuY2xhc3NMaXN0LmFkZCgndmlzaXRlZCcpO1xuICAgICAgICB9XG4gICAgfVxufSk7XG5cbmZ1bmN0aW9uIGRldGVybWluZUZsb2F0KGlucHV0KSB7XG4gICAgaWYoaW5wdXQudmFsdWUubGVuZ3RoID09PSAwKSB7XG4gICAgICAgIGlucHV0LmNsYXNzTGlzdC5yZW1vdmUoJ2FjdGl2ZScpO1xuICAgICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICAgIGlucHV0LmNsYXNzTGlzdC5hZGQoJ2FjdGl2ZScpO1xuICAgIHJldHVybiB0cnVlO1xufVxuXG5mdW5jdGlvbiBjaGVja0F1dG9GaWxsKCl7XG5cbiAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXG4gICAgICAgIHZhciBpbnB1dHMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCdpbnB1dCcpO1xuXG4gICAgICAgIGZvciAoaSA9IDA7IGkgPCBpbnB1dHMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgIGlmKGRldGVybWluZUZsb2F0KGlucHV0c1tpXSkpIHtcbiAgICAgICAgICAgICAgICBpbnB1dHNbaV0uY2xhc3NMaXN0LmFkZCgndmlzaXRlZCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIGNoZWNrQXV0b0ZpbGwoKTtcbiAgICB9LCAxMDApO1xufVxuXG5jaGVja0F1dG9GaWxsKCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gcmVzb3VyY2VzL2Fzc2V0cy9qcy9qY2YtZm9ybXMvaW5kZXguanMiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=");

/***/ }),

/***/ 168:
/***/ (function(module, exports, __webpack_require__) {

eval("/* WEBPACK VAR INJECTION */(function($) {\n__webpack_require__(13);\n\n$(document).on('click', '.js-toggle-forms', function () {\n    $('input[type=\"email\"]:hidden').val($('input[type=\"email\"]:visible').val());\n    $('input[type=\"password\"]:hidden').val($('input[type=\"password\"]:visible').val());\n    $('#register_form, #login_form').toggleClass('hide');\n});\n\n$(document).on('click', '.js-toggle-forgot', function () {\n    $('input[type=\"email\"]:hidden').val($('input[type=\"email\"]:visible').val());\n    $('#forgot_form, #login_form').toggleClass('hide');\n});\n/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(4)))//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMTY4LmpzIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vL3Jlc291cmNlcy9hc3NldHMvanMvcHVibGljLmpzP2IzOTEiXSwic291cmNlc0NvbnRlbnQiOlsiXG5yZXF1aXJlKCcuL2pjZi1mb3JtcycpXG5cbiQoZG9jdW1lbnQpLm9uKCdjbGljaycsICcuanMtdG9nZ2xlLWZvcm1zJywgZnVuY3Rpb24gKCkge1xuICAgICQoJ2lucHV0W3R5cGU9XCJlbWFpbFwiXTpoaWRkZW4nKS52YWwoJCgnaW5wdXRbdHlwZT1cImVtYWlsXCJdOnZpc2libGUnKS52YWwoKSlcbiAgICAkKCdpbnB1dFt0eXBlPVwicGFzc3dvcmRcIl06aGlkZGVuJykudmFsKCQoJ2lucHV0W3R5cGU9XCJwYXNzd29yZFwiXTp2aXNpYmxlJykudmFsKCkpXG4gICAgJCgnI3JlZ2lzdGVyX2Zvcm0sICNsb2dpbl9mb3JtJykudG9nZ2xlQ2xhc3MoJ2hpZGUnKVxufSlcblxuJChkb2N1bWVudCkub24oJ2NsaWNrJywgJy5qcy10b2dnbGUtZm9yZ290JywgZnVuY3Rpb24gKCkge1xuICAgICQoJ2lucHV0W3R5cGU9XCJlbWFpbFwiXTpoaWRkZW4nKS52YWwoJCgnaW5wdXRbdHlwZT1cImVtYWlsXCJdOnZpc2libGUnKS52YWwoKSlcbiAgICAkKCcjZm9yZ290X2Zvcm0sICNsb2dpbl9mb3JtJykudG9nZ2xlQ2xhc3MoJ2hpZGUnKVxufSlcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyByZXNvdXJjZXMvYXNzZXRzL2pzL3B1YmxpYy5qcyJdLCJtYXBwaW5ncyI6IjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBIiwic291cmNlUm9vdCI6IiJ9");

/***/ }),

/***/ 786:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(168);


/***/ })

},[786]);