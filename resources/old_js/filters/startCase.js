import Vue from "vue";

Vue.filter("startCase", (value) => {
  return _.startCase(value);
});
