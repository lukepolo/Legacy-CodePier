import Vue from "vue";

Vue.directive("focus", {
  inserted: function(el) {
    if (el.tagName !== "INPUT") {
      el = el.querySelector("input");
    }
    el.focus();
  },
});
