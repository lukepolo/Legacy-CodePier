import Vue from "vue";
import { format } from "date-fns";
Vue.mixin({
  methods: {
    moment(date = null, format) {
      return format(date, format);
    },
  },
});
