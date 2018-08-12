import Vue from "vue";
import moment from "moment-timezone";

Vue.mixin({
  methods: {
    moment(data, format) {
      return moment(data, format);
    },
  },
});
