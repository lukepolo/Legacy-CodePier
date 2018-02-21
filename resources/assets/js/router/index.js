import Vue from "vue";
import { default as routes } from "../app/routes.js";

window.VueRouter = require("vue-router/dist/vue-router.common");

Vue.use(VueRouter);

const router = new VueRouter({
  mode: "history",
  routes: routes,
  scrollBehavior() {
    let elements = document.getElementsByClassName("section-content");

    for (let i = 0; i < elements.length; i++) {
      elements[i].scrollTop = 0;
    }

    return { x: 0, y: 0 };
  }
});

router.beforeEach((to, from, next) => {
  if (!to.params || !to.params.disabled) {
    next();
  }
});

export default router;
