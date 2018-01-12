import Vue from "vue";
import { default as routes } from "../app/routes.js";

window.VueRouter = require("vue-router/dist/vue-router.common");

Vue.use(VueRouter);

const router = new VueRouter({
  mode: "history",
  routes: routes
});

router.beforeEach((to, from, next) => {
  if (!to.params || !to.params.disabled) {
    next();
  }
});

export default router;
